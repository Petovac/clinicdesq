<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class LabUser extends Authenticatable
{
    protected $fillable = [
        'external_lab_id', 'organisation_id', 'clinic_id',
        'name', 'email', 'password', 'phone', 'role', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * External lab this user belongs to (null for in-house lab techs).
     */
    public function lab()
    {
        return $this->belongsTo(ExternalLab::class, 'external_lab_id');
    }

    /**
     * Organisation this user belongs to (set for in-house lab techs).
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Clinic this in-house lab tech is assigned to.
     */
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function isInHouse(): bool
    {
        return $this->organisation_id !== null && $this->external_lab_id === null;
    }

    public function isExternalLab(): bool
    {
        return $this->external_lab_id !== null;
    }
}
