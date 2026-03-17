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
        'temperature',
        'heart_rate',
        'respiratory_rate',
        'capillary_refill_time',
        'mucous_membrane',
        'hydration_status',
        'lymph_nodes',
        'body_condition_score',
        'pain_score',
        'differentials',
        'diagnosis',
        'treatment_given',
        'procedures_done',
        'further_plan',
        'advice',
        'prognosis',
        'followup_date',
        'followup_reason',
    ];

    protected $casts = [
        'followup_date' => 'date',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
