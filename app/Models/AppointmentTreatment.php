<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentTreatment extends Model
{
    protected $fillable = [
        'appointment_id',
        'price_list_item_id',
        'drug_generic_id',
        'drug_brand_id',
        'inventory_item_id',
        'dose_mg',
        'dose_volume_ml',
        'route',
        'billing_quantity',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
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
}
