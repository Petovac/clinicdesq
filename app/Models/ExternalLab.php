<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalLab extends Model
{
    protected $fillable = [
        'organisation_id', 'name', 'phone', 'email', 'address', 'type', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function users()
    {
        return $this->hasMany(LabUser::class, 'external_lab_id');
    }

    public function labOrders()
    {
        return $this->hasMany(LabOrder::class, 'lab_id');
    }
}
