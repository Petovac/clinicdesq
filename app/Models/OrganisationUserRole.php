<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganisationUserRole extends Model
{
    protected $table = 'organisation_user_roles';

    protected $fillable = [
        'organisation_id',
        'clinic_id',
        'user_id',
        'role_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(OrganisationRole::class, 'role_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
    
}
