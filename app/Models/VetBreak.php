<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class VetBreak extends Model
{
    protected $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function vet()
    {
        return $this->belongsTo(Vet::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function isActive(): bool
    {
        return is_null($this->ended_at);
    }

    /**
     * Check if a given datetime falls within this break
     */
    public function coversTime(Carbon $time): bool
    {
        if ($time->lt($this->started_at)) return false;
        if ($this->ended_at && $time->gte($this->ended_at)) return false;
        return true;
    }

    /**
     * Get the current active break for a vet at a clinic (if any)
     */
    public static function activeBreak(int $vetId, int $clinicId): ?self
    {
        return self::where('vet_id', $vetId)
            ->where('clinic_id', $clinicId)
            ->whereNull('ended_at')
            ->first();
    }

    /**
     * Get all breaks for a vet on a specific date
     */
    public static function breaksOnDate(int $vetId, int $clinicId, Carbon $date): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('vet_id', $vetId)
            ->where('clinic_id', $clinicId)
            ->whereDate('started_at', $date->format('Y-m-d'))
            ->get();
    }
}
