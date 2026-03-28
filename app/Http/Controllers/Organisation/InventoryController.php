<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use App\Models\Clinic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{

public function store(Request $request)
{

$request->validate([
    'item_type' => 'required|in:drug,consumable,product,surgical,vaccine',
    'name' => 'required|string|max:255',
    'drug_brand_id' => 'nullable|unique:inventory_items,drug_brand_id,NULL,id,organisation_id,'.auth()->user()->organisation_id
]);

$item = InventoryItem::create([

'organisation_id' => Auth::user()->organisation_id,

'item_type' => $request->item_type,

'generic_name' => $request->generic_name,

'name' => $request->name,

'brand_id' => $request->brand_id,

'drug_generic_id' => $request->generic_id ?: null,

'drug_brand_id' => $request->drug_brand_id ?: null,

'unit' => $request->unit,

'package_type' => $request->package_type,

'unit_volume_ml' => $request->unit_volume_ml,

'pack_unit' => $request->pack_unit,

'strength_value' => $request->strength_value,

'strength_unit' => $request->strength_unit,

'track_inventory' => $request->track_inventory ? 1 : 0,

'is_multi_use' => $request->is_multi_use ? 1 : 0,

]);

// Auto-submit to KB if this is a drug/vaccine without a KB link (new brand)
if (in_array($request->item_type, ['drug', 'vaccine']) && !$request->drug_brand_id) {
    \App\Models\DrugSubmission::create([
        'organisation_id' => Auth::user()->organisation_id,
        'submitted_by' => Auth::id(),
        'type' => 'brand',
        'generic_name' => $request->generic_name,
        'drug_generic_id' => $request->generic_id ?: null,
        'submitted_generic_name' => !$request->generic_id ? $request->generic_name : null,
        'brand_name' => $request->name,
        'form' => $request->package_type,
        'strength_value' => $request->strength_value,
        'strength_unit' => $request->strength_unit,
        'pack_size' => $request->unit_volume_ml,
        'pack_unit' => $request->pack_unit,
        'status' => 'pending',
    ]);
}

if ($request->wantsJson() || $request->quick_add) {
    return response()->json(['success' => true]);
}

return redirect()->back()->with('success','Inventory item created');

}


public function storeBatch(Request $request)
{

$request->validate([
    'inventory_item_id' => 'required|exists:inventory_items,id',
    'quantity' => 'required|numeric|min:0',
]);

$batch = \App\Models\InventoryBatch::create([
    'inventory_item_id' => $request->inventory_item_id,
    'clinic_id' => null,
    'batch_number' => $request->batch_number,
    'expiry_date' => $request->expiry_date,
    'quantity' => $request->quantity,
    'purchase_price' => $request->purchase_price,
    'created_by' => auth()->id()
]);

InventoryMovement::create([
    'clinic_id'          => 0,
    'inventory_item_id'  => $request->inventory_item_id,
    'inventory_batch_id' => $batch->id,
    'quantity'           => $request->quantity,
    'movement_type'      => 'purchase',
    'notes'              => 'Central stock added' . ($request->batch_number ? " — Batch: {$request->batch_number}" : ''),
    'created_by'         => auth()->id(),
]);

return redirect()->back()->with('success','Central stock batch added');

}


public function items()
{

$organisationId = auth()->user()->organisation_id;

$items = InventoryItem::where('organisation_id',$organisationId)
            ->orderBy('name')
            ->get();

$existingBrandIds = InventoryItem::where('organisation_id',$organisationId)
        ->whereNotNull('drug_brand_id')
        ->pluck('drug_brand_id')
        ->toArray();

return view('organisation.inventory.items', compact('items','existingBrandIds'));

}


public function stock(Request $request)
{
    $organisationId = auth()->user()->organisation_id;
    $search = $request->get('q');

    $clinics = Clinic::where('organisation_id', $organisationId)->orderBy('name')->get();

    $items = InventoryItem::where('organisation_id', $organisationId)
        ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
        ->with(['allBatches' => fn($q) => $q->where('quantity', '>', 0)->orderBy('expiry_date')])
        ->orderBy('name')
        ->get();

    // Compute per-item totals
    $items->each(function ($item) use ($clinics) {
        $item->central_qty = $item->allBatches->whereNull('clinic_id')->sum('quantity');
        $item->total_qty   = $item->allBatches->sum('quantity');
        $item->clinic_breakdown = $clinics->mapWithKeys(function ($clinic) use ($item) {
            return [$clinic->id => $item->allBatches->where('clinic_id', $clinic->id)->sum('quantity')];
        });
    });

    return view('organisation.inventory.stock', compact('items', 'clinics', 'search'));
}


public function update(Request $request,$id)
{

$item = InventoryItem::where('organisation_id',auth()->user()->organisation_id)
        ->findOrFail($id);

$item->update([

'name'=>$request->name,

'package_type'=>$request->package_type,

'strength_value'=>$request->strength_value,

'strength_unit'=>$request->strength_unit,

'unit_volume_ml'=>$request->unit_volume_ml,

'pack_unit'=>$request->pack_unit

]);

return response()->json(['success'=>true]);

}


public function delete($id)
{

InventoryItem::where('organisation_id',auth()->user()->organisation_id)
    ->where('id',$id)
    ->delete();

return response()->json(['success'=>true]);

}

public function searchDrugs(Request $request)
{
    $term = $request->q;

    $drugs = \App\Models\DrugBrand::with('generic')
        ->where('brand_name', 'like', "%{$term}%")
        ->orWhereHas('generic', function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%");
        })
        ->limit(10)
        ->get();

    return response()->json(
        $drugs->map(function ($drug) {

            return [
                'id' => $drug->id,
                'generic' => $drug->generic->name ?? '',
                'brand' => $drug->brand_name,
                'strength_value' => $drug->strength_value,
                'strength_unit' => $drug->strength_unit,
                'pack_size' => $drug->pack_size,
                'pack_unit' => $drug->pack_unit,
                'form' => $drug->form
            ];

        })
    );
}

public function searchInventoryItems(Request $request)
{
    $term = $request->q;
    $orgId = auth()->user()->organisation_id;

    $items = InventoryItem::where('organisation_id', $orgId)
        ->where('name', 'like', "%{$term}%")
        ->orderBy('name')
        ->limit(10)
        ->get();

    return response()->json(
        $items->map(fn($i) => [
            'id'   => $i->id,
            'name' => $i->name,
            'type' => $i->item_type,
        ])
    );
}

public function searchGenerics(Request $request)
{
    $term = $request->q;
    $clinicId = session('active_clinic_id');

    $generics = \App\Models\DrugGeneric::where('name','like',"%{$term}%")
        ->with(['dosages', 'brands'])
        ->orderBy('name')
        ->limit(15)
        ->get();

    return response()->json(
        $generics->map(function($g) use ($clinicId) {
            $dosage = $g->dosages->first();
            $doseInfo = $dosage ? ($dosage->dose_min . ($dosage->dose_max ? '-' . $dosage->dose_max : '') . ' ' . ($dosage->dose_unit ?? 'mg/kg')) : null;
            $freq = $dosage && $dosage->frequencies ? implode(', ', json_decode($dosage->frequencies, true) ?? []) : null;
            $routes = $dosage && $dosage->routes ? implode(', ', json_decode($dosage->routes, true) ?? []) : null;

            // Inventory strengths
            $strengths = [];
            if ($clinicId) {
                $orgId = \App\Models\Clinic::find($clinicId)?->organisation_id;
                $invItems = \App\Models\InventoryItem::where('drug_generic_id', $g->id)
                    ->where('organisation_id', $orgId)
                    ->get();
                foreach ($invItems as $inv) {
                    $strengths[] = [
                        'inventory_item_id' => $inv->id,
                        'brand_name' => $inv->name,
                        'strength_value' => $inv->strength_value,
                        'strength_unit' => $inv->strength_unit ?? 'mg/ml',
                        'form' => $inv->package_type ?? 'injection',
                    ];
                }
            }

            return [
                'generic_id' => $g->id,
                'generic_name' => $g->name,
                'dose_info' => $doseInfo,
                'frequency' => $freq,
                'routes' => $routes,
                'in_inventory' => count($strengths) > 0,
                'strengths' => $strengths,
            ];
        })
    );
}

public function brandsByGeneric(Request $request)
{
    $genericId = $request->generic_id;

    $brands = \App\Models\DrugBrand::where('generic_id', $genericId)
        ->orderBy('brand_name')
        ->get();

    return response()->json(
        $brands->map(function ($b) {
            return [
                'id' => $b->id,
                'brand' => $b->brand_name,
                'strength_value' => $b->strength_value,
                'strength_unit' => $b->strength_unit,
                'form' => $b->form,
                'pack_size' => $b->pack_size,
                'pack_unit' => $b->pack_unit
            ];
        })
    );
}


/*
|--------------------------------------------------------------------------
| Stock Transfer — Form
|--------------------------------------------------------------------------
*/
public function transferForm()
{
    $orgId = auth()->user()->organisation_id;

    $clinics = Clinic::where('organisation_id', $orgId)->orderBy('name')->get();

    $items = InventoryItem::where('organisation_id', $orgId)
        ->with(['allBatches' => fn($q) => $q->where('quantity', '>', 0)])
        ->orderBy('name')
        ->get();

    return view('organisation.inventory.transfer', compact('clinics', 'items'));
}

/*
|--------------------------------------------------------------------------
| Stock Transfer — AJAX: Get all batches for item (central + clinic)
|--------------------------------------------------------------------------
*/
public function centralBatches($itemId)
{
    $orgId = auth()->user()->organisation_id;

    // Ensure item belongs to this org
    $item = InventoryItem::where('organisation_id', $orgId)->findOrFail($itemId);

    $orgClinicIds = Clinic::where('organisation_id', $orgId)->pluck('id', 'id');

    $batches = InventoryBatch::where('inventory_item_id', $itemId)
        ->where('quantity', '>', 0)
        ->where(function ($q) use ($orgClinicIds) {
            $q->whereNull('clinic_id')
              ->orWhereIn('clinic_id', $orgClinicIds->keys());
        })
        ->with('clinic:id,name')
        ->orderBy('expiry_date')
        ->get()
        ->map(fn($b) => [
            'id'           => $b->id,
            'batch_number' => $b->batch_number,
            'quantity'     => $b->quantity,
            'expiry_date'  => $b->expiry_date,
            'clinic_id'    => $b->clinic_id,
            'location'     => $b->clinic_id ? ($b->clinic->name ?? 'Clinic #'.$b->clinic_id) : 'Central',
        ]);

    return response()->json($batches);
}

/*
|--------------------------------------------------------------------------
| Stock Transfer — Execute
|--------------------------------------------------------------------------
*/
public function transfer(Request $request)
{
    $request->validate([
        'clinic_id'          => 'required|exists:clinics,id',
        'inventory_item_id'  => 'required|exists:inventory_items,id',
        'batch_id'           => 'required|exists:inventory_batches,id',
        'quantity'           => 'required|numeric|min:0.001',
    ]);

    $orgId = auth()->user()->organisation_id;

    // Verify clinic belongs to org
    $clinic = Clinic::where('organisation_id', $orgId)->findOrFail($request->clinic_id);

    // Verify batch belongs to the item and is in this org (central or any org clinic)
    $orgClinicIds = Clinic::where('organisation_id', $orgId)->pluck('id')->toArray();
    $sourceBatch = InventoryBatch::where('id', $request->batch_id)
        ->where('inventory_item_id', $request->inventory_item_id)
        ->where(function ($q) use ($orgClinicIds) {
            $q->whereNull('clinic_id')->orWhereIn('clinic_id', $orgClinicIds);
        })
        ->firstOrFail();

    // Cannot transfer to the same clinic
    if ($sourceBatch->clinic_id && $sourceBatch->clinic_id == $clinic->id) {
        return back()->withErrors(['batch_id' => 'Source and destination clinic are the same.'])->withInput();
    }

    if ($request->quantity > $sourceBatch->quantity) {
        return back()->withErrors(['quantity' => "Transfer qty ({$request->quantity}) exceeds available stock ({$sourceBatch->quantity})."])->withInput();
    }

    DB::transaction(function () use ($request, $sourceBatch, $clinic) {
        // 1. Decrement source batch
        $sourceBatch->decrement('quantity', $request->quantity);

        // 2. Create or increment clinic batch with same batch_number
        $clinicBatch = InventoryBatch::firstOrCreate(
            [
                'inventory_item_id' => $request->inventory_item_id,
                'clinic_id'         => $clinic->id,
                'batch_number'      => $sourceBatch->batch_number,
            ],
            [
                'expiry_date'    => $sourceBatch->expiry_date,
                'quantity'       => 0,
                'purchase_price' => $sourceBatch->purchase_price,
                'created_by'     => auth()->id(),
            ]
        );
        $clinicBatch->increment('quantity', $request->quantity);

        $sourceLabel = $sourceBatch->clinic_id
            ? (Clinic::find($sourceBatch->clinic_id)->name ?? 'Clinic #'.$sourceBatch->clinic_id)
            : 'Central';

        // 3. Movement: transfer_out from source
        InventoryMovement::create([
            'clinic_id'          => $sourceBatch->clinic_id ?? 0,
            'inventory_item_id'  => $request->inventory_item_id,
            'inventory_batch_id' => $sourceBatch->id,
            'quantity'           => -$request->quantity,
            'movement_type'      => 'transfer_out',
            'reference_id'       => $clinic->id,
            'notes'              => "Transferred to {$clinic->name}" . ($sourceBatch->batch_number ? " — Batch: {$sourceBatch->batch_number}" : ''),
            'created_by'         => auth()->id(),
        ]);

        // 4. Movement: transfer_in at target clinic
        InventoryMovement::create([
            'clinic_id'          => $clinic->id,
            'inventory_item_id'  => $request->inventory_item_id,
            'inventory_batch_id' => $clinicBatch->id,
            'quantity'           => $request->quantity,
            'movement_type'      => 'transfer_in',
            'reference_id'       => $sourceBatch->clinic_id ?? 0,
            'notes'              => "Received from {$sourceLabel}" . ($sourceBatch->batch_number ? " — Batch: {$sourceBatch->batch_number}" : ''),
            'created_by'         => auth()->id(),
        ]);
    });

    return redirect()
        ->route('organisation.inventory.transfer')
        ->with('success', "Transferred {$request->quantity} units to {$clinic->name} successfully.");
}

/*
|--------------------------------------------------------------------------
| Inventory Movement Log — Organisation Level
|--------------------------------------------------------------------------
*/
public function movements(Request $request)
{
    $orgId    = auth()->user()->organisation_id;
    $type     = $request->get('type');
    $clinicId = $request->get('clinic_id');

    $clinics = Clinic::where('organisation_id', $orgId)->orderBy('name')->get();

    // Require a selection — don't load all movements by default
    $movements = null;
    if ($clinicId !== null && $clinicId !== '') {
        $orgClinicIds = $clinics->pluck('id')->toArray();
        $orgClinicIds[] = 0; // include org-level movements

        // Validate the selected clinic belongs to this org (or is 0 for central)
        if ($clinicId !== '0' && !in_array((int) $clinicId, $orgClinicIds)) {
            abort(403);
        }

        $movements = InventoryMovement::where('clinic_id', $clinicId)
            ->when($type, fn($q) => $q->where('movement_type', $type))
            ->with(['inventoryItem', 'createdBy', 'clinic'])
            ->latest()
            ->paginate(50);
    }

    return view('organisation.inventory.movements', compact('movements', 'type', 'clinicId', 'clinics'));
}

/*
|--------------------------------------------------------------------------
| Clinic Inventory Overview — Org admin views a specific clinic's stock
|--------------------------------------------------------------------------
*/
public function clinicOverview(Request $request, $clinicId)
{
    $orgId = auth()->user()->organisation_id;

    $clinics = Clinic::where('organisation_id', $orgId)->orderBy('name')->get();

    // If no clinic specified or invalid, default to first
    $clinic = $clinics->firstWhere('id', $clinicId);
    if (!$clinic) {
        $clinic = $clinics->first();
        if ($clinic) {
            return redirect()->route('organisation.inventory.clinic-overview', $clinic->id);
        }
        // No clinics at all
        return view('organisation.inventory.clinic-overview', [
            'clinics' => $clinics, 'clinic' => null, 'items' => collect(),
            'movements' => collect(), 'search' => '', 'movementType' => '',
        ]);
    }

    $search = $request->get('q');
    $movementType = $request->get('type');

    // Load inventory items with clinic-specific stock
    $items = InventoryItem::where('organisation_id', $orgId)
        ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
        ->orderBy('name')
        ->get();

    $items->each(function ($item) use ($clinicId) {
        $item->clinic_qty = InventoryBatch::where('inventory_item_id', $item->id)
            ->where('clinic_id', $clinicId)
            ->where('quantity', '>', 0)
            ->sum('quantity');
    });

    // Load movements for this clinic
    $movements = InventoryMovement::where('clinic_id', $clinicId)
        ->when($movementType, fn($q) => $q->where('movement_type', $movementType))
        ->with(['inventoryItem', 'createdBy'])
        ->latest()
        ->limit(100)
        ->get();

    return view('organisation.inventory.clinic-overview', compact(
        'clinics', 'clinic', 'items', 'movements', 'search', 'movementType'
    ));
}

}