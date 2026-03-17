<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpdTreatment extends Model
{
    protected $fillable = [
        'ipd_admission_id', 'treated_by_type', 'treated_by_id',
        'price_list_item_id', 'drug_generic_id', 'drug_brand_id', 'inventory_item_id',
        'dose_mg', 'dose_volume_ml', 'route', 'billing_quantity',
        'treatment_type', 'notes', 'administered_at',
    ];

    protected $casts = [
        'administered_at' => 'datetime',
    ];

    public function admission()
    {
        return $this->belongsTo(IpdAdmission::class, 'ipd_admission_id');
    }

    public function priceItem()
    {
        return $this->belongsTo(PriceListItem::class, 'price_list_item_id');
    }

    public function drugGeneric()
    {
        return $this->belongsTo(DrugGeneric::class);
    }

    public function drugBrand()
    {
        return $this->belongsTo(DrugBrand::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function getTreatedByNameAttribute(): string
    {
        if ($this->treated_by_type === 'vet') {
            return Vet::find($this->treated_by_id)?->name ?? '—';
        }
        return User::find($this->treated_by_id)?->name ?? '—';
    }
}
