<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryOrderItem extends Model
{
    protected $fillable = [
        'inventory_order_id',
        'inventory_item_id',
        'quantity_requested',
        'quantity_fulfilled',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(InventoryOrder::class, 'inventory_order_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
