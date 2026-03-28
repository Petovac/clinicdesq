<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\VetSchedule;
use App\Models\VetBreak;
use Illuminate\Http\Request;

class VetScheduleController extends Controller
{
    public function index()
    {
        $vetId = auth('vet')->id();
        $clinicId = session('active_clinic_id');

        $grid = VetSchedule::getWeekGrid($vetId, $clinicId);
        $activeBreak = VetBreak::activeBreak($vetId, $clinicId);

        return view('vet.schedule.index', compact('grid', 'activeBreak'));
    }

    public function store(Request $request)
    {
        $vetId = auth('vet')->id();
        $clinicId = session('active_clinic_id');

        $days = $request->input('days', []);

        foreach ($days as $dayOfWeek => $config) {
            VetSchedule::updateOrCreate(
                [
                    'vet_id' => $vetId,
                    'clinic_id' => $clinicId,
                    'day_of_week' => $dayOfWeek,
                ],
                [
                    'is_active' => isset($config['is_active']),
                    'start_time' => $config['start_time'] ?? '09:00',
                    'end_time' => $config['end_time'] ?? '18:00',
                    'slot_duration_minutes' => $config['slot_duration_minutes'] ?? 30,
                ]
            );
        }

        return redirect()->back()->with('success', 'Schedule saved successfully.');
    }

    /**
     * Toggle break — start or end break
     */
    public function toggleBreak(Request $request)
    {
        $vetId = auth('vet')->id();
        $clinicId = session('active_clinic_id');

        $activeBreak = VetBreak::activeBreak($vetId, $clinicId);

        if ($activeBreak) {
            // End break
            $activeBreak->update(['ended_at' => now()]);
            $message = 'Break ended. You are now available for appointments.';
        } else {
            // Start break
            VetBreak::create([
                'vet_id' => $vetId,
                'clinic_id' => $clinicId,
                'started_at' => now(),
                'reason' => $request->reason ?? null,
            ]);
            $message = 'You are now on break. No new appointments can be booked until you end the break.';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'on_break' => !$activeBreak, // toggled
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Check if vet is currently on break (AJAX)
     */
    public function breakStatus()
    {
        $vetId = auth('vet')->id();
        $clinicId = session('active_clinic_id');

        $activeBreak = VetBreak::activeBreak($vetId, $clinicId);

        return response()->json([
            'on_break' => !!$activeBreak,
            'since' => $activeBreak ? $activeBreak->started_at->diffForHumans() : null,
        ]);
    }
}
