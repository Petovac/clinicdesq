<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Clinic extends Model
{
    protected $fillable = [
        'organisation_id',
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'pincode',
        'gst_number',
        'gmb_review_url',
    ];    


    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
    
    public function vets()
    {
        return $this->belongsToMany(
            Vet::class,
            'clinic_vet',
            'clinic_id',
            'vet_id'
        )->withPivot('role')->withTimestamps();
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(
            User::class,
            'clinic_user_assignments'
        );
    }

}
