<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\DrugBrand;
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

        abort_if($priceList->organisation_id !== $organisationId,403);

        $priceList->load('items');

        $drugBrands = DrugBrand::orderBy('brand_name')->get();

        $inventoryItems = InventoryItem::where('organisation_id',$organisationId)
            ->orderBy('name')
            ->get();

        return view(
            'organisation.price-lists.edit',
            compact('priceList','drugBrands','inventoryItems')
        );
    }


    /**
     * Update price list + items
     */
    public function update(Request $request, PriceList $priceList)
    {
        $organisationId = auth()->user()->organisation_id;

        abort_if($priceList->organisation_id !== $organisationId,403);

        $request->validate([
            'name' => 'required|string|max:255',

            'items' => 'nullable|array',

            'items.*.name' => 'sometimes|required|string|max:255',
            'items.*.item_type' => 'sometimes|required|in:service,treatment,product',
            'items.*.billing_type' => 'sometimes|required|in:fixed,per_ml,per_vial,per_tablet,per_unit',
            'items.*.drug_brand_id' => 'nullable|exists:drug_brands,id',
            'items.*.inventory_item_id' => 'nullable|exists:inventory_items,id',

            'items.*.price' => 'nullable|numeric|min:0',
            'items.*.procedure_price' => 'nullable|numeric|min:0',

        ]);

        $priceList->update([
            'name' => $request->name
        ]);

        /**
         * Reset items
         */
        $priceList->items()->delete();


        if($request->items){


            if ($request->new_item && !empty($request->new_item['name'])) {

                $priceList->items()->create([
                    'name' => $request->new_item['name'],
                    'item_type' => $request->new_item['item_type'],
                    'billing_type' => $request->new_item['billing_type'],
                    'procedure_price' => $request->new_item['procedure_price'] ?? 0,
                    'price' => $request->new_item['price'] ?? 0,
                    'drug_brand_id' => $request->new_item['drug_brand_id'] ?? null,
                    'inventory_item_id' => $request->new_item['inventory_item_id'] ?? null,
                    'is_active' => 1
                ]);
            }

            foreach($request->items as $item){

                // skip empty rows
                if(empty($item['name'] ?? null)){
                    continue;
                }
            
                $itemType = $item['item_type'] ?? null;
            
                // Treatment must have drug
                if(($item['item_type'] ?? null) === 'treatment' && empty($item['drug_brand_id'])){
                    return back()->withErrors([
                        'items' => 'Treatment items must have a drug selected'
                    ])->withInput();
                }
            
                $priceList->items()->create([
            
                    'name' => $item['name'],
            
                    'code' => $item['code'] ?? null,
            
                    'item_type' => $itemType ?? 'service',
            
                    'price' => $item['price'] ?? 0,
            
                    'procedure_price' => $item['procedure_price'] ?? 0,
            
                    'billing_type' => $item['billing_type'] ?? 'fixed',
            
                    'drug_brand_id' => $item['drug_brand_id'] ?? null,
            
                    'inventory_item_id' => $item['inventory_item_id'] ?? null,
            
                    'is_active' => 1
            
                ]);
            }

        }

        return redirect()
        ->route('organisation.price-lists.index')
        ->with('success','Price list updated');
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