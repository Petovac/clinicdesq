<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpdAdmission extends Model
{
    protected $fillable = [
        'clinic_id', 'pet_id', 'pet_parent_id', 'appointment_id',
        'admitted_by_type', 'admitted_by_id',
        'admission_date', 'admission_reason', 'tentative_diagnosis',
        'cage_number', 'ward', 'status',
        'discharged_at', 'discharge_notes', 'discharge_summary',
        'discharged_by_type', 'discharged_by_id',
    ];

    protected $casts = [
        'admission_date' => 'datetime',
        'discharged_at'  => 'datetime',
    ];

    /* ── Relationships ── */

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function petParent()
    {
        return $this->belongsTo(PetParent::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function vitals()
    {
        return $this->hasMany(IpdVital::class);
    }

    public function treatments()
    {
        return $this->hasMany(IpdTreatment::class);
    }

    public function notes()
    {
        return $this->hasMany(IpdNote::class);
    }

    /* ── Actor resolution ── */

    public function getAdmittedByNameAttribute(): string
    {
        if ($this->admitted_by_type === 'vet') {
            return Vet::find($this->admitted_by_id)?->name ?? '—';
        }
        return User::find($this->admitted_by_id)?->name ?? '—';
    }

    public function getDischargedByNameAttribute(): ?string
    {
        if (!$this->discharged_by_id) return null;
        if ($this->discharged_by_type === 'vet') {
            return Vet::find($this->discharged_by_id)?->name ?? '—';
        }
        return User::find($this->discharged_by_id)?->name ?? '—';
    }

    /* ── Scopes ── */

    public function scopeActive($query)
    {
        return $query->where('status', 'admitted');
    }

    public function scopeForClinic($query, $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }

    /* ── Helpers ── */

    public function isAdmitted(): bool
    {
        return $this->status === 'admitted';
    }
}
