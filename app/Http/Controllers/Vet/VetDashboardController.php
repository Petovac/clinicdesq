<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Appointment;
use Illuminate\Support\Carbon;

class VetDashboardController extends Controller
{


public function dashboard()
{
    $vet = auth('vet')->user();

    $clinics = $vet->clinics()->with('organisation')->get();

    $activeClinicId = session('active_clinic_id');

    $today = Carbon::today();

    $todayCounts = Appointment::where('vet_id', $vet->id)
        ->whereDate('scheduled_at', $today)
        ->whereIn('clinic_id', $clinics->pluck('id'))
        ->selectRaw('clinic_id, COUNT(*) as total')
        ->groupBy('clinic_id')
        ->pluck('total', 'clinic_id');

    $activeClinic = $clinics->firstWhere('id', $activeClinicId);

    return view('vet.dashboard', compact(
        'clinics',
        'activeClinic',
        'todayCounts'
    ));
}

    public function index()
{
    $vet = auth('vet')->user();
    abort_if(!$vet, 401);

    // Step A: clinics for this vet
    $clinics = Clinic::whereHas('vets', function ($q) use ($vet) {
        $q->where('clinic_vet.vet_id', $vet->id)
          ->where('clinic_vet.is_active', 1)
          ->whereNull('clinic_vet.offboarded_at');
    })
    ->with('organisation')
    ->orderBy('name')
    ->get();

    // 🔑 Step C: if NO active clinic, behave like Step A
    if (!session()->has('active_clinic_id')) {
        return view('vet.dashboard', [
            'clinics' => $clinics,
            'activeClinic' => null,
        ]);
    }

    $activeClinicId = session('active_clinic_id');

    // 🔐 Safety: ensure vet still belongs to active clinic
    $isAllowed = $clinics->pluck('id')->contains($activeClinicId);
    abort_if(!$isAllowed, 403);

    // Appointments ONLY for this vet + this clinic
    $appointments = Appointment::where('vet_id', $vet->id)
        ->where('clinic_id', $activeClinicId)
        ->orderBy('scheduled_at')
        ->get();

    return view('vet.dashboard', [
        'clinics'        => $clinics,
        'activeClinic'   => $clinics->firstWhere('id', $activeClinicId),
        'appointments'   => $appointments,
        'todayCount'     => $appointments->whereBetween(
                                'scheduled_at',
                                [now()->startOfDay(), now()->endOfDay()]
                            )->count(),
        'upcomingCount'  => $appointments
                                ->where('scheduled_at', '>', now())
                                ->count(),
        'completedCount' => $appointments
                                ->where('status', 'completed')
                                ->count(),
    ]);
}

    public function selectClinic(Clinic $clinic)
{
    $vet = auth('vet')->user();

    if (!$vet) {
        abort(401);
    }

    /**
     * Security check:
     * Ensure vet is actively onboarded to this clinic
     */
    $isAllowed = $clinic->vets()
        ->where('clinic_vet.vet_id', $vet->id)
        ->where('clinic_vet.is_active', 1)
        ->whereNull('clinic_vet.offboarded_at')
        ->exists();

    abort_if(!$isAllowed, 403);

    /**
     * Set active clinic context
     */
    session(['active_clinic_id' => $clinic->id]);

    return redirect()->route('vet.clinic.dashboard');
}

}