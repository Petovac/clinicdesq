<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'published_at' => 'datetime',
        'closes_at' => 'datetime',
        'salary_min' => 'integer',
        'salary_max' => 'integer',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    public function getSalaryRangeAttribute(): string
    {
        if ($this->salary_min && $this->salary_max) {
            return '₹' . number_format($this->salary_min) . ' – ₹' . number_format($this->salary_max);
        }
        if ($this->salary_min) return '₹' . number_format($this->salary_min) . '+';
        if ($this->salary_max) return 'Up to ₹' . number_format($this->salary_max);
        return 'Not disclosed';
    }

    public function getEmploymentLabelAttribute(): string
    {
        return match($this->employment_type) {
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'locum' => 'Locum / Relief',
            'contract' => 'Contract',
            default => ucfirst($this->employment_type),
        };
    }
}
