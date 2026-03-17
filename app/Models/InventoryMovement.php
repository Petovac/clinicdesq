<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'clinic_id',
        'inventory_item_id',
        'inventory_batch_id',
        'quantity',
        'movement_unit',
        'movement_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class, 'inventory_batch_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
