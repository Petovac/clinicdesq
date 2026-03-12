<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceListItem extends Model
{
    protected $fillable = [
        'price_list_id',
        'item_type',
        'name',
        'code',
        'billing_type',
        'price',
        'procedure_price',
        'drug_brand_id',
        'inventory_item_id',
        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(\App\Models\InventoryItem::class);
    }

    public function drugBrand()
    {
        return $this->belongsTo(\App\Models\DrugBrand::class);
    }

    public function treatments()
    {
        return $this->hasMany(\App\Models\AppointmentTreatment::class);
    }

    /** Inventory items consumed when this procedure is performed */
    public function procedureInventoryItems()
    {
        return $this->hasMany(ProcedureInventoryItem::class);
    }

    public function isProcedure(): bool  { return $this->item_type === 'procedure'; }
    public function isVisitFee(): bool   { return $this->item_type === 'visit_fee'; }
    public function isDrug(): bool       { return $this->item_type === 'drug'; }
}