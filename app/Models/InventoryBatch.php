<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryBatch extends Model
{
    protected $table = 'inventory_batches';

    protected $fillable = [
        'inventory_item_id',
        'clinic_id',
        'batch_number',
        'expiry_date',
        'quantity',
        'purchase_price',
        'created_by'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}