<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vet extends Authenticatable
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'registration_number',
        'specialization',

        // Profile fields
        'degree',
        'skills',
        'certifications',
        'experience',

        'is_active',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * A vet can belong to many clinics
     */
    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(
            Clinic::class,
            'clinic_vet',
            'vet_id',
            'clinic_id'
        )->withPivot('role')->withTimestamps();
    }
}
