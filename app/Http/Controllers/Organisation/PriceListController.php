<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\PriceList;
use App\Models\PriceListItem;
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
            'item_type'         => 'required|in:service,treatment,product',
            'billing_type'      => 'required|in:fixed,per_ml,per_vial,per_tablet,per_unit',
            'price'             => 'nullable|numeric|min:0',
            'procedure_price'   => 'nullable|numeric|min:0',
            'drug_brand_id'     => 'nullable|exists:drug_brands,id',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
        ]);

        if ($request->item_type === 'treatment' && empty($request->drug_brand_id)) {
            return response()->json(['error' => 'Treatment items must have a drug selected'], 422);
        }

        $item = $priceList->items()->create([
            'name'              => $request->name,
            'item_type'         => $request->item_type,
            'billing_type'      => $request->billing_type,
            'price'             => $request->price ?? 0,
            'procedure_price'   => $request->procedure_price ?? 0,
            'drug_brand_id'     => $request->drug_brand_id,
            'inventory_item_id' => $request->inventory_item_id,
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
            'item_type' => 'required|in:service,treatment,product',
            'billing_type' => 'required|in:fixed,per_ml,per_vial,per_tablet,per_unit',
            'price' => 'nullable|numeric|min:0',
            'procedure_price' => 'nullable|numeric|min:0',
            'drug_brand_id' => 'nullable|exists:drug_brands,id',
            'inventory_item_id' => 'nullable|exists:inventory_items,id'
        ]);

        // treatment must have drug
        if($request->item_type === 'treatment' && empty($request->drug_brand_id)){
            return response()->json([
                'error' => 'Treatment items must have a drug'
            ],422);
        }

        $item->update([
            'name' => $request->name,
            'item_type' => $request->item_type,
            'billing_type' => $request->billing_type,
            'procedure_price' => $request->procedure_price ?? 0,
            'price' => $request->price ?? 0,
            'drug_brand_id' => $request->drug_brand_id,
            'inventory_item_id' => $request->inventory_item_id
        ]);

        return response()->json([
            'success' => true
        ]);
    }

}