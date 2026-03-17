<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalLab extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'address', 'city', 'state', 'pincode', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function organisations()
    {
        return $this->belongsToMany(Organisation::class, 'organisation_lab')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->hasMany(LabUser::class, 'external_lab_id');
    }

    public function labOrders()
    {
        return $this->hasMany(LabOrder::class, 'lab_id');
    }

    public function testOfferings()
    {
        return $this->hasMany(ExternalLabTest::class);
    }
}
