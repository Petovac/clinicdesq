<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class PriceListController extends Controller
{

    /**
     * List organisation price lists
     */
    public function index()
    {
        $organisationId = auth()->user()->organisation_id;

        $lists = PriceList::where('organisation_id', $organisationId)
            ->withCount('items')
            ->orderBy('created_at','desc')
            ->get();

        return view('organisation.price-lists.index', compact('lists'));
    }


    /**
     * Create new price list
     */
    public function create()
    {
        return view('organisation.price-lists.create');
    }


    /**
     * Store price list
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        PriceList::create([
            'organisation_id' => auth()->user()->organisation_id,
            'name' => $request->name,
            'is_active' => 0
        ]);

        return redirect()
            ->route('organisation.price-lists.index')
            ->with('success','Price list created');
    }


    /**
     * Edit price list
     */
    public function edit(PriceList $priceList)
    {
        $organisationId = auth()->user()->organisation_id;
        abort_if($priceList->organisation_id !== $organisationId, 403);

        $priceList->load('items.drugBrand.generic', 'items.inventoryItem');

        return view('organisation.price-lists.edit', compact('priceList'));
    }


    /**
     * Update price list name
     */
    public function update(Request $request, PriceList $priceList)
    {
        $organisationId = auth()->user()->organisation_id;
        abort_if($priceList->organisation_id !== $organisationId, 403);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $priceList->update(['name' => $request->name]);

        return response()->json(['success' => true]);
    }


    /**
     * AJAX: Add a single item to a price list
     */
    public function storeItem(Request $request, PriceList $priceList)
    {
        $organisationId = auth()->user()->organisation_id;
        abort_if($priceList->organisation_id !== $organisationId, 403);

        $request->validate([
            'name'              => 'required|string|max:255',
            'item_type'         => 'required|in:service,drug,vaccine,consumable,surgical,product',
            'billing_type'      => 'required|in:fixed,per_ml,per_vial,per_tablet,per_unit,per_strip,per_piece,per_sachet,per_tube,per_dose',
            'price'             => 'nullable|numeric|min:0',
            'procedure_price'   => 'nullable|numeric|min:0',
            'drug_brand_id'     => 'nullable|exists:drug_brands,id',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
        ]);

        $item = $priceList->items()->create([
            'name'              => $request->name,
            'item_type'         => $request->item_type,
            'billing_type'      => $request->billing_type,
            'price'             => $request->price ?? 0,
            'procedure_price'   => $request->procedure_price ?? 0,
            'drug_brand_id'     => $request->drug_brand_id ?: null,
            'inventory_item_id' => $request->inventory_item_id ?: null,
            'is_active'         => 1,
        ]);

        return response()->json(['success' => true, 'item' => $item]);
    }


    /**
     * AJAX: Delete a single price list item
     */
    public function deleteItem(PriceListItem $item)
    {
        $organisationId = auth()->user()->organisation_id;
        abort_if($item->priceList->organisation_id !== $organisationId, 403);

        $item->delete();

        return response()->json(['success' => true]);
    }



    /**
     * Activate price list
     */
    public function activate(PriceList $priceList)
    {
        $organisationId = auth()->user()->organisation_id;

        abort_if($priceList->organisation_id !== $organisationId,403);

        PriceList::where('organisation_id',$organisationId)
            ->update(['is_active'=>0]);

        $priceList->update([
            'is_active'=>1
        ]);

        return back()->with('success','Price list activated');
    }

    public function updateItem(Request $request, PriceListItem $item)
    {
        $organisationId = auth()->user()->organisation_id;

        // security check
        abort_if(
            $item->priceList->organisation_id !== $organisationId,
            403
        );

        $request->validate([
            'name' => 'required|string|max:255',
            'item_type' => 'required|in:service,drug,vaccine,consumable,surgical,product',
            'billing_type' => 'required|in:fixed,per_ml,per_vial,per_tablet,per_unit,per_strip,per_piece,per_sachet,per_tube,per_dose',
            'price' => 'nullable|numeric|min:0',
            'procedure_price' => 'nullable|numeric|min:0',
            'drug_brand_id' => 'nullable|exists:drug_brands,id',
            'inventory_item_id' => 'nullable|exists:inventory_items,id'
        ]);

        $item->update([
            'name' => $request->name,
            'item_type' => $request->item_type,
            'billing_type' => $request->billing_type,
            'procedure_price' => $request->procedure_price ?? 0,
            'price' => $request->price ?? 0,
            'drug_brand_id' => $request->drug_brand_id ?: null,
            'inventory_item_id' => $request->inventory_item_id ?: null,
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Import inventory items into the price list.
     */
    public function importFromInventory(Request $request, PriceList $priceList)
    {
        $organisationId = auth()->user()->organisation_id;
        abort_if($priceList->organisation_id !== $organisationId, 403);

        // Get all inventory items for this org
        $inventoryItems = InventoryItem::where('organisation_id', $organisationId)->get();

        // Get already-linked inventory item IDs in this price list
        $existingIds = $priceList->items()
            ->whereNotNull('inventory_item_id')
            ->pluck('inventory_item_id')
            ->toArray();

        $imported = 0;
        foreach ($inventoryItems as $inv) {
            if (in_array($inv->id, $existingIds)) continue;

            // Map inventory package_type to billing_type
            $billingMap = [
                'tablet' => 'per_tablet',
                'capsule' => 'per_tablet',
                'strip' => 'per_strip',
                'injection' => 'per_ml',
                'vial' => 'per_vial',
                'fluid' => 'per_ml',
                'bottle' => 'per_unit',
                'tube' => 'per_tube',
                'sachet' => 'per_sachet',
                'piece' => 'per_piece',
                'packet' => 'per_unit',
            ];

            $billingType = $billingMap[$inv->package_type] ?? 'fixed';
            $itemType = $inv->item_type ?? 'drug'; // drug, consumable, surgical, product

            $priceList->items()->create([
                'name' => $inv->name,
                'item_type' => $itemType,
                'billing_type' => $billingType,
                'price' => 0, // org admin sets price
                'procedure_price' => 0,
                'drug_brand_id' => $inv->drug_brand_id,
                'inventory_item_id' => $inv->id,
                'is_active' => 1,
            ]);
            $imported++;
        }

        return back()->with('success', "Imported {$imported} inventory items. Set their prices below.");
    }

    /**
     * AJAX: Search inventory items for linking.
     */
    public function searchInventory(Request $request)
    {
        $organisationId = auth()->user()->organisation_id;
        $q = $request->get('q', '');

        $items = InventoryItem::where('organisation_id', $organisationId)
            ->where('name', 'like', "%{$q}%")
            ->limit(20)
            ->get(['id', 'name', 'item_type', 'package_type', 'strength_value', 'strength_unit', 'drug_brand_id']);

        return response()->json($items);
    }
}