<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabOrderTest extends Model
{
    protected $fillable = [
        'lab_order_id', 'lab_test_catalog_id', 'external_lab_test_id',
        'test_name', 'parameters', 'price', 'status', 'notes',
    ];

    protected $casts = [
        'parameters' => 'array',
        'price' => 'decimal:2',
    ];

    public function labOrder()
    {
        return $this->belongsTo(LabOrder::class);
    }

    public function catalogItem()
    {
        return $this->belongsTo(LabTestCatalog::class, 'lab_test_catalog_id');
    }

    public function externalLabTest()
    {
        return $this->belongsTo(ExternalLabTest::class, 'external_lab_test_id');
    }

    public function results()
    {
        return $this->hasMany(LabResult::class);
    }
}
