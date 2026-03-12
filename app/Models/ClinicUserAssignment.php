<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicUserAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'clinic_id',
    ];
}
