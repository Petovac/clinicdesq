<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class PetParent extends Authenticatable
{
    protected $fillable = [
        'name',
        'phone',
    ];

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
}
