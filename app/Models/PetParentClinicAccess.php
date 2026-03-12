<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetParentClinicAccess extends Model
{
    protected $table = 'pet_parent_clinic_access'; // 👈 IMPORTANT

    protected $fillable = [
        'pet_parent_id',
        'clinic_id',
        'granted_at',
        'revoked_at',
        'granted_via',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}
