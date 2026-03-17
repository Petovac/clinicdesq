<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'group',
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

    /**
     * Get all permissions grouped by their group name.
     */
    public static function grouped()
    {
        return static::orderByRaw("FIELD(`group`, 'Dashboard','Clinics','Users & Roles','Vets','Appointments','Clinical Records','Diagnostics & Reports','Billing','Inventory','Pricing','Followups & Performance')")
            ->orderBy('id')
            ->get()
            ->groupBy('group');
    }
}