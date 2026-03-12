<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganisationRole extends Model
{
    protected $table = 'organisation_roles';

    protected $fillable = [
        'organisation_id',
        'name',
        'description',
        'clinic_scope'
    ];

    public function permissions()
    {
        return $this->belongsToMany(
            \App\Models\Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        );
    }
}