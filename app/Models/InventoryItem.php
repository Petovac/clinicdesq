<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $table = 'inventory_items';

    protected $fillable = [
        'organisation_id',
        'item_type',
        'generic_name',
        'brand_id',
        'drug_brand_id',
        'drug_generic_id',
        'name',
        'unit',
        'package_type',
        'unit_volume_ml',
        'pack_unit',
        'strength_value',
        'strength_unit',
        'track_inventory',
        'is_multi_use',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function drugBrand()
    {
        return $this->belongsTo(DrugBrand::class);
    }

    public function drugGeneric()
    {
        return $this->belongsTo(DrugGeneric::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /** Org-level (central) stock — clinic_id is null */
    public function batches()
    {
        return $this->hasMany(InventoryBatch::class)->whereNull('clinic_id');
    }

    /** Clinic-specific stock */
    public function clinicBatches(int $clinicId)
    {
        return $this->hasMany(InventoryBatch::class)
                    ->where('clinic_id', $clinicId);
    }

    /** All batches regardless of scope */
    public function allBatches()
    {
        return $this->hasMany(InventoryBatch::class);
    }

    /**
     * Available stock quantity for a given clinic (FEFO-ready query).
     * Counts batches with clinic_id = $clinicId that haven't expired.
     */
    public function availableQty(int $clinicId): float
    {
        return $this->hasMany(InventoryBatch::class)
                    ->where('clinic_id', $clinicId)
                    ->where('quantity', '>', 0)
                    ->where(function ($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', now()->toDateString());
                    })
                    ->sum('quantity');
    }
}
