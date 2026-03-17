<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTestCatalog extends Model
{
    protected $table = 'lab_test_catalog';

    protected $fillable = [
        'clinic_id', 'name', 'code', 'category', 'sample_type',
        'price_list_item_id', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function priceItem()
    {
        return $this->belongsTo(PriceListItem::class, 'price_list_item_id');
    }
}
