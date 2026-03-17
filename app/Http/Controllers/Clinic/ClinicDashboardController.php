<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Appointment;
use Carbon\Carbon;

class ClinicDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Clinic users must belong to a clinic
        abort_if(empty($user->clinic_id), 403);

        $clinic = Clinic::with('vets')->findOrFail($user->clinic_id);

        $today = Carbon::today();

        $todayAppointments = Appointment::where('clinic_id', $clinic->id)
            ->whereDate('scheduled_at', $today)
            ->count();

        $upcomingAppointments = Appointment::where('clinic_id', $clinic->id)
            ->whereDate('scheduled_at', '>', $today)
            ->count();

        $waitingCount = Appointment::where('clinic_id', $clinic->id)
            ->where('status', 'checked_in')
            ->count();

        $consultationCount = Appointment::where('clinic_id', $clinic->id)
            ->where('status', 'in_consultation')
            ->count();

        $needsBillingCount = Appointment::where('clinic_id', $clinic->id)
            ->where('status', 'completed')
            ->where(function ($q) {
                $q->whereDoesntHave('bill')
                  ->orWhereHas('bill', fn($b) => $b->where('status', 'draft'));
            })
            ->count();

        return view('clinic.dashboard', compact(
            'clinic',
            'todayAppointments',
            'upcomingAppointments',
            'waitingCount',
            'consultationCount',
            'needsBillingCount'
        ));
    }
}
