<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Pet;
use App\Models\Prescription;
use App\Models\LabReport;
use App\Models\Vaccination;
use App\Models\Clinic;
use App\Models\Vet;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $fillable = [
        'clinic_id',
        'vet_id',
        'pet_parent_id',
        'pet_id',
        'scheduled_at',
        'weight',
        'pet_age_at_visit',
        'status',
        'appointment_number',
        'created_by',
        'checked_in_at',
        'consultation_started_at',
        'completed_at',
    ];

    protected $casts = [
        'pet_age_at_visit' => 'float',
        'scheduled_at'     => 'datetime',
    ];

    public function vet(): BelongsTo
    {
        return $this->belongsTo(Vet::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    // These can exist even if tables are empty for now
    public function prescription()
    {
        return $this->hasOne(\App\Models\Prescription::class);
    }

    public function caseSheet()
    {
        return $this->hasOne(\App\Models\CaseSheet::class);
    }

    

    public function getCalculatedAgeAtVisitAttribute(): ?string
    {
        if (
            !$this->pet ||
            $this->pet->age === null ||
            $this->pet->age_recorded_at === null
        ) {
            return null;
        }

        $baseYears  = (int) $this->pet->age;
        $baseMonths = (int) ($this->pet->age_months ?? 0);

        $recordedAt = Carbon::parse($this->pet->age_recorded_at)->startOfDay();
        $visitDate  = Carbon::parse($this->scheduled_at)->startOfDay();

        if ($visitDate->lessThan($recordedAt)) {
            return "{$baseYears}y {$baseMonths}m";
        }

        $diffMonths = (int) $recordedAt->diffInMonths($visitDate);

        $totalMonths = (int) (($baseYears * 12) + $baseMonths + $diffMonths);

        $years  = intdiv($totalMonths, 12);
        $months = $totalMonths % 12;

        if ($years > 0 && $months > 0) {
            return "{$years}y {$months}m";
        }

        if ($years > 0) {
            return "{$years}y";
        }

        return "{$months}m";
    }
        public function diagnosticReports()
    {
        return $this->hasMany(\App\Models\DiagnosticReport::class);
    }

    public function treatments()
    {
        return $this->hasMany(\App\Models\AppointmentTreatment::class);
    }

    public function bill()
    {
        return $this->hasOne(\App\Models\Bill::class);
    }

    public function labOrders()
    {
        return $this->hasMany(\App\Models\LabOrder::class);
    }
}