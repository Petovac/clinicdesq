<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'prescription_id',
        'drug_generic_id',
        'inventory_item_id',
        'strength_value',
        'strength_unit',
        'form',
        'medicine',
        'dosage',
        'frequency',
        'duration',
        'instructions',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function drugGeneric()
    {
        return $this->belongsTo(DrugGeneric::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /** Is this item stocked in the given clinic? */
    public function isInStock(int $clinicId): bool
    {
        if (!$this->inventory_item_id) {
            return false;
        }

        return InventoryBatch::where('inventory_item_id', $this->inventory_item_id)
            ->where('clinic_id', $clinicId)
            ->where('quantity', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->exists();
    }
}
