<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabOrderTest extends Model
{
    protected $fillable = [
        'lab_order_id', 'lab_test_catalog_id', 'test_name', 'status', 'notes',
    ];

    public function labOrder()
    {
        return $this->belongsTo(LabOrder::class);
    }

    public function catalogItem()
    {
        return $this->belongsTo(LabTestCatalog::class, 'lab_test_catalog_id');
    }

    public function results()
    {
        return $this->hasMany(LabResult::class);
    }
}
