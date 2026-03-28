<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetVaccination extends Model
{
    protected $guarded = [];

    protected $casts = [
        'administered_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function vet()
    {
        return $this->belongsTo(Vet::class);
    }

    // Scopes
    public function scopeUpcoming($q)
    {
        return $q->whereNotNull('next_due_date')
            ->where('next_due_date', '>=', today())
            ->where('next_due_date', '<=', today()->addDays(30));
    }

    public function scopeOverdue($q)
    {
        return $q->whereNotNull('next_due_date')
            ->where('next_due_date', '<', today());
    }

    public function isOverdue(): bool
    {
        return $this->next_due_date && $this->next_due_date->isPast();
    }

    public function isDueSoon(): bool
    {
        return $this->next_due_date
            && $this->next_due_date->isFuture()
            && $this->next_due_date->diffInDays(today()) <= 14;
    }
}
