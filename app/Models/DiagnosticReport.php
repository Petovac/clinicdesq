<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticReport extends Model
{
    protected $table = 'diagnostic_reports';

    protected $fillable = [
        'appointment_id',
        'pet_id',
        'clinic_id',
        'vet_id',
        'type',
        'title',
        'report_date',
        'lab_or_center',
        'summary',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    /* 🔗 Relationships */

    public function appointment()
    {
        return $this->belongsTo(\App\Models\Appointment::class);
    }

    public function files()
    {
        return $this->hasMany(\App\Models\DiagnosticFile::class);
    }
}