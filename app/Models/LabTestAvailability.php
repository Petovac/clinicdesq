<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTestAvailability extends Model
{
    protected $table = 'lab_test_availability';

    protected $fillable = [
        'lab_test_catalog_id', 'clinic_id', 'is_available',
        'unavailable_reason', 'updated_by',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function test()
    {
        return $this->belongsTo(LabTestCatalog::class, 'lab_test_catalog_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function updatedByUser()
    {
        return $this->belongsTo(LabUser::class, 'updated_by');
    }
}
