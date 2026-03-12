<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetParentAccessOtp extends Model
{
    protected $fillable = [
        'pet_parent_id',
        'clinic_id',
        'mobile',
        'otp',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];
}
