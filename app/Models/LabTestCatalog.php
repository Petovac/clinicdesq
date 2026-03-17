<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTestCatalog extends Model
{
    protected $table = 'lab_test_catalog';

    protected $fillable = [
        'organisation_id', 'name', 'code', 'category', 'sample_type',
        'parameters', 'estimated_time', 'price', 'cost_price', 'is_active',
    ];

    protected $casts = [
        'parameters' => 'array',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Check availability at a specific clinic.
     */
    public function availabilityAt($clinicId)
    {
        return $this->hasOne(LabTestAvailability::class, 'lab_test_catalog_id')
            ->where('clinic_id', $clinicId);
    }

    public function availability()
    {
        return $this->hasMany(LabTestAvailability::class, 'lab_test_catalog_id');
    }

    /**
     * Scope: only tests available in-house at a specific clinic.
     */
    public function scopeAvailableAtClinic($query, $clinicId)
    {
        return $query->where('is_active', true)
            ->whereHas('availability', function ($q) use ($clinicId) {
                $q->where('clinic_id', $clinicId)->where('is_available', true);
            });
    }
}
