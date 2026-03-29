<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\InjectionRouteFee;
use App\Models\InventoryItem;
use App\Models\ProcedureInventoryItem;

class FeeConfigController extends Controller
{
    public function index()
    {
        $orgId = auth()->user()->organisation_id;

        // Get visit fee from active price list
        $activeList = PriceList::where('organisation_id', $orgId)
            ->where('is_active', 1)
            ->first();

        $visitFee = null;
        if ($activeList) {
            $visitFee = PriceListItem::where('price_list_id', $activeList->id)
                ->where('item_type', 'visit_fee')
                ->where('is_active', 1)
                ->first();
        }

        // Get injection route fees
        $routeFees = InjectionRouteFee::where('organisation_id', $orgId)
            ->orderByRaw("FIELD(route_code, 'IV','IM','SC','ID','PO','IO','IT')")
            ->get();

        // Get procedure/service price list items
        $procedures = $activeList
            ? PriceListItem::where('price_list_id', $activeList->id)
                ->where('item_type', 'service')
                ->where('is_active', 1)
                ->with('procedureInventoryItems.inventoryItem')
                ->orderBy('name')
                ->get()
            : collect();

        // Get consumable + surgical inventory items for linking
        $consumableItems = InventoryItem::where('organisation_id', $orgId)
            ->whereIn('item_type', ['consumable', 'surgical'])
            ->orderBy('name')
            ->get();

        return view('organisation.fee-config.index', compact('visitFee', 'routeFees', 'activeList', 'procedures', 'consumableItems'));
    }

    public function updateVisitFee(Request $request)
    {
        $request->validate([
            'visit_fee' => 'required|numeric|min:0',
        ]);

        $orgId = auth()->user()->organisation_id;

        $activeList = PriceList::where('organisation_id', $orgId)
            ->where('is_active', 1)
            ->first();

        if (!$activeList) {
            return back()->with('error', 'No active price list found. Create a price list first.');
        }

        $visitFee = PriceListItem::where('price_list_id', $activeList->id)
            ->where('item_type', 'visit_fee')
            ->where('is_active', 1)
            ->first();

        if ($visitFee) {
            $visitFee->update(['price' => $request->visit_fee]);
        } else {
            PriceListItem::create([
                'price_list_id' => $activeList->id,
                'name'          => 'Consultation / Visit Fee',
                'item_type'     => 'visit_fee',
                'billing_type'  => 'fixed',
                'price'         => $request->visit_fee,
                'is_active'     => 1,
            ]);
        }

        return back()->with('success', 'Consultation fee updated successfully.');
    }

    public function updateRouteFees(Request $request)
    {
        $request->validate([
            'fees'              => 'required|array',
            'fees.*.id'         => 'required|exists:injection_route_fees,id',
            'fees.*.fee'        => 'required|numeric|min:0',
            'fees.*.is_active'  => 'sometimes|boolean',
        ]);

        $orgId = auth()->user()->organisation_id;

        foreach ($request->fees as $feeData) {
            InjectionRouteFee::where('id', $feeData['id'])
                ->where('organisation_id', $orgId)
                ->update([
                    'administration_fee' => $feeData['fee'],
                    'is_active'          => $feeData['is_active'] ?? true,
                ]);
        }

        return back()->with('route_success', 'Injection route fees updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Procedure & Service Fees
    |--------------------------------------------------------------------------
    */

    public function updateProcedureFees(Request $request)
    {
        $request->validate([
            'procedures'          => 'required|array',
            'procedures.*.id'     => 'required|exists:price_list_items,id',
            'procedures.*.price'  => 'required|numeric|min:0',
        ]);

        $orgId = auth()->user()->organisation_id;
        $activeList = PriceList::where('organisation_id', $orgId)->where('is_active', 1)->first();

        if (!$activeList) {
            return back()->with('error', 'No active price list found.');
        }

        foreach ($request->procedures as $data) {
            PriceListItem::where('id', $data['id'])
                ->where('price_list_id', $activeList->id)
                ->update(['price' => $data['price']]);
        }

        return back()->with('procedure_success', 'Procedure fees updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Procedure Consumables — AJAX
    |--------------------------------------------------------------------------
    */

    public function procedureConsumables(PriceListItem $procedure)
    {
        $consumables = ProcedureInventoryItem::where('price_list_item_id', $procedure->id)
            ->with('inventoryItem:id,name,item_type,package_type')
            ->get()
            ->map(fn($pi) => [
                'id'               => $pi->id,
                'inventory_item_id'=> $pi->inventory_item_id,
                'name'             => $pi->inventoryItem->name ?? 'Unknown',
                'item_type'        => $pi->inventoryItem->item_type ?? '',
                'quantity_used'    => $pi->quantity_used,
            ]);

        return response()->json($consumables);
    }

    public function saveProcedureConsumables(Request $request, PriceListItem $procedure)
    {
        $request->validate([
            'consumables'                      => 'nullable|array',
            'consumables.*.inventory_item_id'   => 'required|exists:inventory_items,id',
            'consumables.*.quantity_used'        => 'required|numeric|min:0.001',
        ]);

        $orgId = auth()->user()->organisation_id;

        // Verify all inventory items belong to same org
        if ($request->consumables) {
            $itemIds = collect($request->consumables)->pluck('inventory_item_id');
            $validCount = InventoryItem::where('organisation_id', $orgId)
                ->whereIn('id', $itemIds)
                ->count();

            if ($validCount !== $itemIds->count()) {
                return response()->json(['error' => 'Invalid inventory items.'], 422);
            }
        }

        // Delete existing and re-insert
        ProcedureInventoryItem::where('price_list_item_id', $procedure->id)->delete();

        foreach ($request->consumables ?? [] as $item) {
            ProcedureInventoryItem::create([
                'price_list_item_id' => $procedure->id,
                'inventory_item_id'  => $item['inventory_item_id'],
                'quantity_used'      => $item['quantity_used'],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Consumables updated.']);
    }
}
