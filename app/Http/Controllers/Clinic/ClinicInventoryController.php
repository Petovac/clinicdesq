<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use App\Models\InventoryOrder;
use App\Models\InventoryOrderItem;
use Illuminate\Support\Facades\DB;

class ClinicInventoryController extends Controller
{
    private function clinicId()
    {
        return auth()->user()->clinic_id;
    }

    private function orgId()
    {
        return auth()->user()->organisation_id;
    }

    /*
    |--------------------------------------------------------------------------
    | Stock Overview
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $clinicId = $this->clinicId();
        $orgId    = $this->orgId();
        $search   = $request->get('q');

        $items = InventoryItem::where('organisation_id', $orgId)
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get();

        // Attach clinic stock totals
        $items->each(function ($item) use ($clinicId) {
            $item->clinic_qty = InventoryBatch::where('inventory_item_id', $item->id)
                ->where('clinic_id', $clinicId)
                ->where('quantity', '>', 0)
                ->sum('quantity');
        });

        return view('clinic.inventory.index', compact('items', 'search'));
    }

    /*
    |--------------------------------------------------------------------------
    | Item Detail — batches + movements
    |--------------------------------------------------------------------------
    */
    public function show($itemId)
    {
        $clinicId = $this->clinicId();
        $item = InventoryItem::findOrFail($itemId);

        $batches = InventoryBatch::where('inventory_item_id', $itemId)
            ->where('clinic_id', $clinicId)
            ->orderBy('expiry_date')
            ->get();

        $movements = InventoryMovement::where('inventory_item_id', $itemId)
            ->where('clinic_id', $clinicId)
            ->with('createdBy')
            ->latest()
            ->limit(50)
            ->get();

        return view('clinic.inventory.show', compact('item', 'batches', 'movements'));
    }

    /*
    |--------------------------------------------------------------------------
    | Add Stock (receive new batch)
    |--------------------------------------------------------------------------
    */
    public function addStock(Request $request)
    {
        $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'batch_number'      => 'nullable|string|max:100',
            'quantity'          => 'required|numeric|min:0.001',
            'expiry_date'       => 'nullable|date',
            'purchase_price'    => 'nullable|numeric|min:0',
        ]);

        $clinicId = $this->clinicId();

        DB::transaction(function () use ($request, $clinicId) {
            $batch = InventoryBatch::create([
                'inventory_item_id' => $request->inventory_item_id,
                'clinic_id'         => $clinicId,
                'batch_number'      => $request->batch_number,
                'quantity'          => $request->quantity,
                'expiry_date'       => $request->expiry_date,
                'purchase_price'    => $request->purchase_price,
                'created_by'        => auth()->id(),
            ]);

            InventoryMovement::create([
                'clinic_id'          => $clinicId,
                'inventory_item_id'  => $request->inventory_item_id,
                'inventory_batch_id' => $batch->id,
                'quantity'           => $request->quantity,
                'movement_type'      => 'purchase',
                'notes'              => 'Stock received' . ($request->batch_number ? " — Batch: {$request->batch_number}" : ''),
                'created_by'         => auth()->id(),
            ]);
        });

        return redirect()
            ->route('clinic.inventory.show', $request->inventory_item_id)
            ->with('success', 'Stock added successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Manual Adjustment
    |--------------------------------------------------------------------------
    */
    public function adjustForm()
    {
        $clinicId = $this->clinicId();
        $orgId    = $this->orgId();

        $items = InventoryItem::where('organisation_id', $orgId)->orderBy('name')->get();

        return view('clinic.inventory.adjust', compact('items'));
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'batch_id'          => 'required|exists:inventory_batches,id',
            'adjustment'        => 'required|numeric',
            'reason'            => 'required|string|max:50',
            'notes'             => 'nullable|string|max:255',
        ]);

        $clinicId = $this->clinicId();
        $batch = InventoryBatch::where('id', $request->batch_id)
            ->where('clinic_id', $clinicId)
            ->firstOrFail();

        DB::transaction(function () use ($request, $batch, $clinicId) {
            $batch->increment('quantity', $request->adjustment);

            // Prevent negative stock
            if ($batch->quantity < 0) {
                $batch->update(['quantity' => 0]);
            }

            InventoryMovement::create([
                'clinic_id'          => $clinicId,
                'inventory_item_id'  => $request->inventory_item_id,
                'inventory_batch_id' => $batch->id,
                'quantity'           => $request->adjustment,
                'movement_type'      => 'manual_adjustment',
                'notes'              => ucfirst($request->reason) . ($request->notes ? " — {$request->notes}" : ''),
                'created_by'         => auth()->id(),
            ]);
        });

        return redirect()
            ->route('clinic.inventory.show', $request->inventory_item_id)
            ->with('success', 'Stock adjusted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Get batches for an item (AJAX)
    |--------------------------------------------------------------------------
    */
    public function itemBatches($itemId)
    {
        $clinicId = $this->clinicId();
        $batches = InventoryBatch::where('inventory_item_id', $itemId)
            ->where('clinic_id', $clinicId)
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date')
            ->get(['id', 'batch_number', 'quantity', 'expiry_date']);

        return response()->json($batches);
    }

    /*
    |--------------------------------------------------------------------------
    | Movement Log
    |--------------------------------------------------------------------------
    */
    public function movements(Request $request)
    {
        $clinicId = $this->clinicId();
        $type = $request->get('type');

        $movements = InventoryMovement::where('clinic_id', $clinicId)
            ->when($type, fn($q) => $q->where('movement_type', $type))
            ->with(['inventoryItem', 'createdBy'])
            ->latest()
            ->paginate(50);

        return view('clinic.inventory.movements', compact('movements', 'type'));
    }

    /*
    |--------------------------------------------------------------------------
    | Orders — List
    |--------------------------------------------------------------------------
    */
    public function orderIndex()
    {
        $clinicId = $this->clinicId();

        $orders = InventoryOrder::where('clinic_id', $clinicId)
            ->with('createdBy')
            ->withCount('items')
            ->latest()
            ->paginate(30);

        return view('clinic.orders.index', compact('orders'));
    }

    /*
    |--------------------------------------------------------------------------
    | Orders — Create
    |--------------------------------------------------------------------------
    */
    public function orderCreate()
    {
        $orgId = $this->orgId();
        $items = InventoryItem::where('organisation_id', $orgId)->orderBy('name')->get();

        return view('clinic.orders.create', compact('items'));
    }

    public function orderStore(Request $request)
    {
        $request->validate([
            'order_type'            => 'required|in:vendor,organisation',
            'vendor_name'           => 'nullable|required_if:order_type,vendor|string|max:255',
            'notes'                 => 'nullable|string',
            'items'                 => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity'      => 'required|numeric|min:0.001',
        ]);

        $clinicId = $this->clinicId();
        $orgId    = $this->orgId();

        $order = DB::transaction(function () use ($request, $clinicId, $orgId) {
            $order = InventoryOrder::create([
                'clinic_id'       => $clinicId,
                'organisation_id' => $orgId,
                'order_number'    => InventoryOrder::generateOrderNumber(),
                'order_type'      => $request->order_type,
                'vendor_name'     => $request->vendor_name,
                'status'          => 'draft',
                'notes'           => $request->notes,
                'created_by'      => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                InventoryOrderItem::create([
                    'inventory_order_id' => $order->id,
                    'inventory_item_id'  => $item['inventory_item_id'],
                    'quantity_requested'  => $item['quantity'],
                ]);
            }

            return $order;
        });

        return redirect()
            ->route('clinic.orders.show', $order->id)
            ->with('success', 'Order created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Orders — Show
    |--------------------------------------------------------------------------
    */
    public function orderShow($id)
    {
        $clinicId = $this->clinicId();
        $order = InventoryOrder::where('clinic_id', $clinicId)
            ->with(['items.inventoryItem', 'createdBy'])
            ->findOrFail($id);

        return view('clinic.orders.show', compact('order'));
    }

    /*
    |--------------------------------------------------------------------------
    | Orders — Submit (change from draft to submitted)
    |--------------------------------------------------------------------------
    */
    public function orderSubmit($id)
    {
        $clinicId = $this->clinicId();
        $order = InventoryOrder::where('clinic_id', $clinicId)
            ->where('status', 'draft')
            ->findOrFail($id);

        $order->update(['status' => 'submitted']);

        return redirect()
            ->route('clinic.orders.show', $order->id)
            ->with('success', 'Order submitted successfully.');
    }
}
