<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{

public function store(Request $request)
{

$request->validate([
    'item_type' => 'required|in:drug,consumable',
    'name' => 'required|string|max:255',
    'drug_brand_id' => 'nullable|unique:inventory_items,drug_brand_id,NULL,id,organisation_id,'.auth()->user()->organisation_id
]);

InventoryItem::create([

'organisation_id' => Auth::user()->organisation_id,

'item_type' => $request->item_type,

'generic_name' => $request->generic_name,

'name' => $request->name,

'brand_id' => $request->brand_id,

'drug_brand_id' => $request->drug_brand_id,

'unit' => $request->unit,

'package_type' => $request->package_type,

'unit_volume_ml' => $request->unit_volume_ml,

'pack_unit' => $request->pack_unit,

'strength_value' => $request->strength_value,

'strength_unit' => $request->strength_unit,

'track_inventory' => $request->track_inventory ? 1 : 0,

'is_multi_use' => $request->is_multi_use ? 1 : 0,

]);

return redirect()->back()->with('success','Inventory item created');

}


public function storeBatch(Request $request)
{

$request->validate([
    'inventory_item_id' => 'required|exists:inventory_items,id',
    'quantity' => 'required|numeric|min:0',
]);

\App\Models\InventoryBatch::create([
    'inventory_item_id' => $request->inventory_item_id,
    'clinic_id' => null,
    'batch_number' => $request->batch_number,
    'expiry_date' => $request->expiry_date,
    'quantity' => $request->quantity,
    'purchase_price' => $request->purchase_price,
    'created_by' => auth()->id()
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


public function stock()
{

$organisationId = auth()->user()->organisation_id;

$items = InventoryItem::where('organisation_id',$organisationId)
            ->with('batches')
            ->orderBy('name')
            ->get();

return view('organisation.inventory.stock', compact('items'));

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

public function searchGenerics(Request $request)
{
    $term = $request->q;

    $generics = \App\Models\DrugGeneric::where('name','like',"%{$term}%")
        ->orderBy('name')
        ->limit(10)
        ->get();

    return response()->json(
        $generics->map(function($g){
            return [
                'id' => $g->id,
                'name' => $g->name
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

}