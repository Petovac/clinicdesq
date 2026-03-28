<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class VetSchedule extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'day_of_week' => 'integer',
        'slot_duration_minutes' => 'integer',
    ];

    const DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    public function vet()
    {
        return $this->belongsTo(Vet::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? '';
    }

    /**
     * Generate available time slots for a given date.
     * Slots during active breaks are marked unavailable.
     */
    public static function generateSlots(Carbon $date, int $vetId, int $clinicId): array
    {
        $dayOfWeek = $date->dayOfWeek; // 0=Sun, 6=Sat

        $schedule = self::where('vet_id', $vetId)
            ->where('clinic_id', $clinicId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        // If schedule exists but is inactive, return empty (day off)
        if ($schedule && !$schedule->is_active) {
            return [];
        }

        // Default schedule if not configured
        $startTime = $schedule ? $schedule->start_time : '09:00';
        $endTime   = $schedule ? $schedule->end_time : '18:00';
        $duration  = $schedule ? $schedule->slot_duration_minutes : 30;

        // Generate time slots
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime);
        $end   = Carbon::parse($date->format('Y-m-d') . ' ' . $endTime);

        // Get existing appointments for this vet on this date
        $bookedTimes = Appointment::where('vet_id', $vetId)
            ->whereDate('scheduled_at', $date->format('Y-m-d'))
            ->whereNotIn('status', ['cancelled'])
            ->pluck('scheduled_at')
            ->map(fn($dt) => Carbon::parse($dt)->format('H:i'))
            ->toArray();

        // Get breaks for this date
        $breaks = VetBreak::breaksOnDate($vetId, $clinicId, $date);

        $slots = [];
        $cursor = $start->copy();
        $now = Carbon::now();

        while ($cursor->lt($end)) {
            $timeStr = $cursor->format('H:i');
            $slotDateTime = Carbon::parse($date->format('Y-m-d') . ' ' . $timeStr);

            // Check if slot falls within any break
            $onBreak = false;
            foreach ($breaks as $brk) {
                if ($brk->coversTime($slotDateTime)) {
                    $onBreak = true;
                    break;
                }
            }

            // Check if slot is in the past (for today)
            $isPast = $date->isToday() && $slotDateTime->lt($now);
            $isBooked = in_array($timeStr, $bookedTimes);

            $slots[] = [
                'time'      => $timeStr,
                'display'   => $cursor->format('g:i A'),
                'available' => !$isBooked && !$isPast && !$onBreak,
                'booked'    => $isBooked,
                'past'      => $isPast,
                'on_break'  => $onBreak,
            ];

            $cursor->addMinutes($duration);
        }

        return $slots;
    }

    /**
     * Get the schedule grid for a vet at a clinic (all 7 days).
     */
    public static function getWeekGrid(int $vetId, int $clinicId): array
    {
        $existing = self::where('vet_id', $vetId)
            ->where('clinic_id', $clinicId)
            ->get()
            ->keyBy('day_of_week');

        $grid = [];
        for ($d = 0; $d < 7; $d++) {
            if ($existing->has($d)) {
                $s = $existing[$d];
                $grid[$d] = [
                    'day_name'   => self::DAYS[$d],
                    'is_active'  => $s->is_active,
                    'start_time' => substr($s->start_time, 0, 5),
                    'end_time'   => substr($s->end_time, 0, 5),
                    'slot_duration_minutes' => $s->slot_duration_minutes,
                ];
            } else {
                $grid[$d] = [
                    'day_name'   => self::DAYS[$d],
                    'is_active'  => $d >= 1 && $d <= 6, // Mon-Sat on, Sun off
                    'start_time' => '09:00',
                    'end_time'   => '18:00',
                    'slot_duration_minutes' => 30,
                ];
            }
        }

        return $grid;
    }
}
