<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class LabUser extends Authenticatable
{
    protected $fillable = [
        'external_lab_id', 'name', 'email', 'password', 'phone', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function lab()
    {
        return $this->belongsTo(ExternalLab::class, 'external_lab_id');
    }
}
