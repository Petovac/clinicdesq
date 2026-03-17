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

        // Auth & files
        'password',
        'signature_path',
        'license_path',
        'certificate_paths',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'certificate_paths' => 'array',
    ];

    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature_path ? asset('storage/' . $this->signature_path) : null;
    }

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
