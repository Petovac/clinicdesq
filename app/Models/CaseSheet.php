<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseSheet extends Model
{
    protected $fillable = [
        'appointment_id',
        'presenting_complaint',
        'history',
        'clinical_examination',
        'differentials',
        'diagnosis',
        'treatment_given',
        'procedures_done',
        'further_plan',
        'advice',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
