<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Appointment;
use Carbon\Carbon;

class Pet extends Model
{
    protected $fillable = [
        'pet_parent_id',
        'name',
        'species',
        'breed',
        'age',
        'age_months',
        'gender',
        'age_recorded_at',
    ];

    public function petParent()
    {
        return $this->belongsTo(PetParent::class);
    }

    // 🔑 REQUIRED for appointment history
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    

    public function getCurrentAgeAttribute(): ?string
    {
        if (!$this->age || !$this->age_recorded_at) {
            return null;
        }

        $recordedDate = Carbon::parse($this->age_recorded_at);
        $now = Carbon::now();

        $monthsPassed = $recordedDate->diffInMonths($now);
        $yearsFromMonths = floor($monthsPassed / 12);
        $remainingMonths = $monthsPassed % 12;

        $currentYears = $this->age + $yearsFromMonths;

        if ($remainingMonths > 0) {
            return "{$currentYears}y {$remainingMonths}m";
        }

        return "{$currentYears}y";
    }
}

