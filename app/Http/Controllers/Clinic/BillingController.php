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
use App\Models\InventoryMovement;
use App\Models\InjectionRouteFee;
use App\Services\WebhookService;
use App\Services\WhatsappService;
use App\Services\PdfService;
use App\Models\WhatsappConfig;

class BillingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Create / open draft bill
    |--------------------------------------------------------------------------
    */
    public function create(Appointment $appointment)
    {
        abort_if($appointment->clinic_id !== (int) session('active_clinic_id'), 403);

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
                           ->whereNotIn('item_type', ['service'])
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
        abort_if($bill->clinic_id !== (int) session('active_clinic_id'), 403);

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
        abort_if($bill->clinic_id !== (int) session('active_clinic_id'), 403);
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
        abort_if($bill->clinic_id !== (int) session('active_clinic_id'), 403);
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

        // Auto-send bill via WhatsApp + fire webhook
        try {
            $bill->load(['appointment.pet.petParent', 'clinic.organisation', 'items']);
            $orgId = $bill->clinic->organisation_id;
            $waConfig = WhatsappConfig::where('organisation_id', $orgId)->first();

            if ($waConfig && $waConfig->isConfigured() && $waConfig->send_bill) {
                $parent = $bill->appointment->pet->petParent ?? null;
                if ($parent && $parent->phone) {
                    $pdfPath = PdfService::generateBill($bill);
                    WhatsappService::sendDocument(
                        organisationId: $orgId,
                        recipientPhone: $parent->phone,
                        recipientName: $parent->name,
                        templateName: 'clinicdesq_bill',
                        messageType: 'bill',
                        filePath: $pdfPath,
                        templateVariables: [
                            'filename' => 'Invoice_' . $bill->id . '.pdf',
                            'body' => [$parent->name, $bill->appointment->pet->name, '₹' . number_format($bill->total_amount, 2), $bill->clinic->name],
                        ],
                        clinicId: $bill->clinic_id,
                        referenceType: Bill::class,
                        referenceId: $bill->id,
                        sentBy: auth()->id(),
                    );
                }
            }

            WebhookService::dispatch($orgId, 'bill.confirmed', [
                'bill_id' => $bill->id,
                'total_amount' => $bill->total_amount,
                'appointment_id' => $bill->appointment_id,
                'pet' => $bill->appointment->pet->toArray(),
                'pet_parent' => $bill->appointment->pet->petParent?->toArray(),
                'items' => $bill->items->toArray(),
                'clinic' => $bill->clinic->only(['id', 'name', 'city']),
            ]);
        } catch (\Exception $e) {
            \Log::error('Auto-send/webhook failed after bill confirm', ['error' => $e->getMessage()]);
        }

        return redirect()
            ->route('clinic.billing.create', $bill->appointment_id)
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
                ->where('item_type', 'service')
                ->where('name', 'like', '%visit%fee%')
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
        $orgId = $appointment->clinic->organisation_id;

        foreach ($appointment->treatments as $treatment) {
            if (!$treatment->priceItem) {
                continue;
            }

            $source = $treatment->drug_generic_id ? 'injection' : 'procedure';
            $qty    = $treatment->billing_quantity ?? 1;

            if ($source === 'injection') {
                // Injection billing: route_admin_fee + (drug_price_per_ml × volume)
                $drugCostPerUnit = (float) $treatment->priceItem->price;
                $volume          = (float) ($treatment->dose_volume_ml ?? $qty);
                $drugCost        = round($drugCostPerUnit * $volume, 2);

                $routeFee = InjectionRouteFee::feeFor($orgId, $treatment->route);
                $total    = $drugCost + $routeFee;

                // Build description with breakdown
                $desc = $treatment->priceItem->name;
                if ($treatment->dose_volume_ml) {
                    $desc .= ' (' . $treatment->dose_volume_ml . ' ml)';
                }
                if ($routeFee > 0 && $treatment->route) {
                    $desc .= ' + ' . strtoupper($treatment->route) . ' admin fee';
                }

                $bill->items()->create([
                    'price_list_item_id' => $treatment->priceItem->id,
                    'quantity'           => $volume,
                    'price'              => $drugCostPerUnit,
                    'total'              => $total,
                    'source'             => 'injection',
                    'status'             => 'approved',
                    'description'        => $desc,
                ]);
            } else {
                // Procedure billing: flat price
                $bill->items()->create([
                    'price_list_item_id' => $treatment->priceItem->id,
                    'quantity'           => $qty,
                    'price'              => $treatment->priceItem->price,
                    'total'              => round($treatment->priceItem->price * $qty, 2),
                    'source'             => 'procedure',
                    'status'             => 'approved',
                    'description'        => $treatment->priceItem->name,
                ]);
            }
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
                $qty = $this->calculatePrescriptionQty($rxItem);

                $bill->items()->create([
                    'price_list_item_id'  => $priceItem?->id,
                    'prescription_item_id'=> $rxItem->id,
                    'quantity'            => $qty,
                    'price'               => $priceItem?->price ?? 0,
                    'total'               => round(($priceItem?->price ?? 0) * $qty, 2),
                    'source'              => 'prescription',
                    'status'              => $inStock ? 'pending' : 'rejected',
                    'description'         => $rxItem->medicine
                        . ($rxItem->strength_value ? ' ' . $rxItem->strength_value . $rxItem->strength_unit : ''),
                ]);
            }
        }
    }

    /**
     * Calculate total quantity from prescription item fields.
     * qty = units_per_dose × frequency_per_day × days
     */
    private function calculatePrescriptionQty($rxItem): float
    {
        // Parse units per dose from dosage field (e.g. "2.3 tabs", "7.5ml", "1 tab")
        $unitsPerDose = 1;
        if (preg_match('/^([\d.]+)\s*(tabs?|ml|capsules?)/i', $rxItem->dosage ?? '', $m)) {
            $unitsPerDose = (float) $m[1];
        }

        // Parse frequency per day
        $freq = strtolower(trim($rxItem->frequency ?? ''));
        $freqMap = [
            'sid' => 1, 'od' => 1, 'once daily' => 1, 'qd' => 1,
            'bid' => 2, 'twice daily' => 2, 'bd' => 2,
            'tid' => 3, 'three times daily' => 3, 'tds' => 3,
            'qid' => 4, 'four times daily' => 4,
            'eod' => 0.5, 'every other day' => 0.5,
        ];
        $timesPerDay = $freqMap[$freq] ?? 1;

        // Parse duration days (e.g. "5 days", "10", "28 days")
        $days = 1;
        if (preg_match('/(\d+)/', $rxItem->duration ?? '', $m)) {
            $days = (int) $m[1];
        }

        $total = round($unitsPerDose * $timesPerDay * $days, 3);
        return max($total, 1);
    }

    /**
     * FEFO inventory deduction.
     */
    private function deductFefo(int $inventoryItemId, int $clinicId, float $qty): void
    {
        // Lock rows to prevent concurrent over-deduction
        $batches = InventoryBatch::where('inventory_item_id', $inventoryItemId)
            ->where('clinic_id', $clinicId)
            ->where('quantity', '>', 0)
            ->orderByRaw("CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('expiry_date')
            ->lockForUpdate()
            ->get();

        // Safety: check total available before deducting
        $totalAvailable = $batches->sum('quantity');
        if ($totalAvailable <= 0) {
            \Log::warning("FEFO: No stock available for item #{$inventoryItemId} at clinic #{$clinicId}");
            return;
        }

        // Cap deduction to available stock (prevent negative)
        $qty = min($qty, $totalAvailable);

        foreach ($batches as $batch) {
            if ($qty <= 0) break;
            $deduct = min((float) $batch->quantity, $qty);
            $batch->decrement('quantity', $deduct);

            InventoryMovement::create([
                'clinic_id'          => $clinicId,
                'inventory_item_id'  => $inventoryItemId,
                'inventory_batch_id' => $batch->id,
                'quantity'           => -$deduct,
                'movement_type'      => 'treatment_usage',
                'notes'              => 'Used in treatment' . ($batch->batch_number ? " — Batch: {$batch->batch_number}" : ''),
                'created_by'         => auth()->id(),
            ]);

            $qty -= $deduct;
        }
    }
}
