<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcedureInventoryItem extends Model
{
    protected $table = 'procedure_inventory_items';

    protected $fillable = [
        'price_list_item_id',
        'inventory_item_id',
        'quantity_used',
    ];

    public function priceListItem()
    {
        return $this->belongsTo(PriceListItem::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
