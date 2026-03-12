<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\InventoryBatch;

class BillingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Create / open draft bill
    |--------------------------------------------------------------------------
    */
    public function create(Appointment $appointment)
    {
        $appointment->load([
            'pet',
            'clinic',
            'vet',
            'treatments.priceItem',
            'treatments.drugGeneric',
            'treatments.inventoryItem',
            'prescription.items.inventoryItem',
            'prescription.items.drugGeneric',
        ]);

        $clinic  = $appointment->clinic;
        $orgId   = $clinic->organisation_id;
        $clinicId = $clinic->id;

        $activeList = PriceList::where('organisation_id', $orgId)
            ->where('is_active', 1)
            ->first();

        // Get or create draft bill
        $bill = Bill::firstOrNew([
            'appointment_id' => $appointment->id,
        ]);

        $isNew = !$bill->exists;

        if ($isNew) {
            $bill->clinic_id     = $clinicId;
            $bill->created_by    = auth()->id();
            $bill->total_amount  = 0;
            $bill->payment_status = 'pending';
            $bill->status        = 'draft';
            $bill->save();
        }

        // Populate bill items only if bill is fresh (no items yet)
        if ($isNew || $bill->items()->count() === 0) {
            $this->populateDraftItems($bill, $appointment, $activeList, $clinicId);
        }

        $bill->load(['items.priceItem', 'items.prescriptionItem.inventoryItem', 'items.prescriptionItem.drugGeneric']);
        $bill->recalculateTotal();
        $bill->refresh();

        // All price list items for manual additions
        $priceItems = $activeList
            ? PriceListItem::where('price_list_id', $activeList->id)
                           ->where('is_active', 1)
                           ->whereNotIn('item_type', ['visit_fee'])
                           ->get()
            : collect();

        return view('clinic.billing.create', compact('appointment', 'bill', 'priceItems'));
    }

    /*
    |--------------------------------------------------------------------------
    | Update a bill item (approve / reject / qty change)
    |--------------------------------------------------------------------------
    */
    public function updateItem(Request $request, BillItem $item)
    {
        $bill = $item->bill;

        if ($bill->isConfirmed()) {
            return response()->json(['error' => 'Bill already confirmed'], 422);
        }

        $request->validate([
            'status'   => 'sometimes|in:approved,rejected',
            'quantity' => 'sometimes|numeric|min:0.001',
        ]);

        if ($request->has('status')) {
            $item->status = $request->status;
        }

        if ($request->has('quantity') && $item->price) {
            $item->quantity = $request->quantity;
            $item->total    = round($item->price * $request->quantity, 2);
        }

        $item->save();

        $bill->recalculateTotal();

        return response()->json([
            'success' => true,
            'total'   => number_format($bill->fresh()->total_amount, 2),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Add a manual item to the bill
    |--------------------------------------------------------------------------
    */
    public function addItem(Request $request, Bill $bill)
    {
        if ($bill->isConfirmed()) {
            return back()->with('error', 'Bill already confirmed.');
        }

        $request->validate([
            'price_list_item_id' => 'required|exists:price_list_items,id',
            'quantity'           => 'required|numeric|min:0.001',
        ]);

        $priceItem = PriceListItem::findOrFail($request->price_list_item_id);

        $bill->items()->create([
            'price_list_item_id' => $priceItem->id,
            'quantity'           => $request->quantity,
            'price'              => $priceItem->price,
            'total'              => round($priceItem->price * $request->quantity, 2),
            'source'             => 'manual',
            'status'             => 'approved',
            'description'        => $priceItem->name,
        ]);

        $bill->recalculateTotal();

        return back()->with('success', 'Item added.');
    }

    /*
    |--------------------------------------------------------------------------
    | Confirm bill — deduct inventory (FEFO)
    |--------------------------------------------------------------------------
    */
    public function confirm(Request $request, Bill $bill)
    {
        if ($bill->isConfirmed()) {
            return back()->with('error', 'Bill is already confirmed.');
        }

        // Ensure no prescription items are still pending
        $pendingCount = $bill->items()->where('source', 'prescription')->where('status', 'pending')->count();
        if ($pendingCount > 0) {
            return back()->with('error', "Please approve or reject all {$pendingCount} prescription item(s) before confirming.");
        }

        DB::beginTransaction();
        try {
            $approvedItems = $bill->items()->where('status', 'approved')->get();

            foreach ($approvedItems as $item) {
                $inventoryItemId = null;

                // Resolve inventory item from bill item source
                if ($item->price_list_item_id) {
                    $inventoryItemId = $item->priceItem?->inventory_item_id;
                }

                // For prescription items, use the prescription item's inventory link
                if ($item->source === 'prescription' && $item->prescription_item_id) {
                    $inventoryItemId = $item->prescriptionItem?->inventory_item_id ?? $inventoryItemId;
                }

                if (!$inventoryItemId) {
                    continue; // No inventory to deduct (e.g. procedures, visit fee)
                }

                $this->deductFefo($inventoryItemId, $bill->clinic_id, $item->quantity);
            }

            // Also deduct procedure-linked consumables
            foreach ($approvedItems->where('source', 'procedure') as $item) {
                if (!$item->price_list_item_id) continue;
                $procedureItems = \App\Models\ProcedureInventoryItem::where('price_list_item_id', $item->price_list_item_id)->get();
                foreach ($procedureItems as $pi) {
                    $this->deductFefo($pi->inventory_item_id, $bill->clinic_id, $pi->quantity_used * $item->quantity);
                }
            }

            $bill->recalculateTotal();
            $bill->update([
                'status'         => 'confirmed',
                'payment_status' => 'unpaid',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to confirm bill: ' . $e->getMessage());
        }

        return redirect()
            ->route('billing.create', $bill->appointment_id)
            ->with('success', 'Bill confirmed. Inventory updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | Internal helpers
    |--------------------------------------------------------------------------
    */

    private function populateDraftItems(Bill $bill, Appointment $appointment, ?PriceList $activeList, int $clinicId): void
    {
        // 1. Visit fee — auto-added
        if ($activeList) {
            $visitFee = PriceListItem::where('price_list_id', $activeList->id)
                ->where('item_type', 'visit_fee')
                ->where('is_active', 1)
                ->first();

            if ($visitFee) {
                $bill->items()->create([
                    'price_list_item_id' => $visitFee->id,
                    'quantity'           => 1,
                    'price'              => $visitFee->price,
                    'total'              => $visitFee->price,
                    'source'             => 'visit_fee',
                    'status'             => 'approved',
                    'description'        => $visitFee->name,
                ]);
            }
        }

        // 2. Treatments (injections + procedures)
        foreach ($appointment->treatments as $treatment) {
            if (!$treatment->priceItem) {
                continue;
            }

            $source = $treatment->drug_generic_id ? 'injection' : 'procedure';
            $qty    = $treatment->billing_quantity ?? 1;

            $bill->items()->create([
                'price_list_item_id' => $treatment->priceItem->id,
                'quantity'           => $qty,
                'price'              => $treatment->priceItem->price,
                'total'              => round($treatment->priceItem->price * $qty, 2),
                'source'             => $source,
                'status'             => 'approved',
                'description'        => $treatment->priceItem->name
                    . ($source === 'injection' && $treatment->dose_volume_ml
                        ? ' (' . $treatment->dose_volume_ml . ' ml)'
                        : ''),
            ]);
        }

        // 3. Prescription items — pending staff review
        if ($appointment->prescription) {
            foreach ($appointment->prescription->items as $rxItem) {
                $priceItem = null;

                if ($rxItem->inventory_item_id && $activeList) {
                    $priceItem = PriceListItem::where('price_list_id', $activeList->id)
                        ->where('inventory_item_id', $rxItem->inventory_item_id)
                        ->where('is_active', 1)
                        ->first();
                }

                $inStock = $rxItem->isInStock($clinicId);

                $bill->items()->create([
                    'price_list_item_id'  => $priceItem?->id,
                    'prescription_item_id'=> $rxItem->id,
                    'quantity'            => 1,
                    'price'               => $priceItem?->price ?? 0,
                    'total'               => $priceItem?->price ?? 0,
                    'source'              => 'prescription',
                    'status'              => $inStock ? 'pending' : 'rejected',
                    'description'         => $rxItem->medicine
                        . ($rxItem->strength_value ? ' ' . $rxItem->strength_value . $rxItem->strength_unit : ''),
                ]);
            }
        }
    }

    /**
     * FEFO inventory deduction.
     */
    private function deductFefo(int $inventoryItemId, int $clinicId, float $qty): void
    {
        $batches = InventoryBatch::where('inventory_item_id', $inventoryItemId)
            ->where('clinic_id', $clinicId)
            ->where('quantity', '>', 0)
            ->orderByRaw("CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('expiry_date')
            ->get();

        foreach ($batches as $batch) {
            if ($qty <= 0) break;
            $deduct = min((float) $batch->quantity, $qty);
            $batch->decrement('quantity', $deduct);
            $qty -= $deduct;
        }
    }
}
