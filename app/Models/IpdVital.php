<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpdVital extends Model
{
    protected $fillable = [
        'ipd_admission_id', 'recorded_by_type', 'recorded_by_id',
        'recorded_at', 'temperature', 'heart_rate', 'respiratory_rate',
        'weight', 'spo2', 'blood_pressure_systolic', 'blood_pressure_diastolic',
        'mucous_membrane', 'crt', 'pain_score', 'notes',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function admission()
    {
        return $this->belongsTo(IpdAdmission::class, 'ipd_admission_id');
    }

    public function getRecordedByNameAttribute(): string
    {
        if ($this->recorded_by_type === 'vet') {
            return Vet::find($this->recorded_by_id)?->name ?? '—';
        }
        return User::find($this->recorded_by_id)?->name ?? '—';
    }
}
