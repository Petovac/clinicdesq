<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug'
    ];

    public function roles()
    {
        return $this->belongsToMany(
            OrganisationRole::class,
            'role_permissions',
            'permission_id',
            'role_id'
        );
    }
}