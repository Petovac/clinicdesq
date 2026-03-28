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

    $clinics = $vet->clinics()->with('organisation')->wherePivot('status', 'accepted')->wherePivot('is_active', 1)->get();

    $activeClinicId = session('active_clinic_id');

    $today = Carbon::today();

    $todayCounts = Appointment::where('vet_id', $vet->id)
        ->whereDate('scheduled_at', $today)
        ->whereIn('clinic_id', $clinics->pluck('id'))
        ->selectRaw('clinic_id, COUNT(*) as total')
        ->groupBy('clinic_id')
        ->pluck('total', 'clinic_id');

    $activeClinic = $clinics->firstWhere('id', $activeClinicId);

    // Pending onboarding requests
    $pendingClinicRequests = \DB::table('clinic_vet')
        ->join('clinics', 'clinics.id', '=', 'clinic_vet.clinic_id')
        ->join('organisations', 'organisations.id', '=', 'clinics.organisation_id')
        ->where('clinic_vet.vet_id', $vet->id)
        ->where('clinic_vet.status', 'pending')
        ->select('clinic_vet.*', 'clinics.name as clinic_name', 'clinics.city', 'organisations.name as org_name')
        ->get();

    return view('vet.dashboard', compact(
        'clinics',
        'activeClinic',
        'todayCounts',
        'pendingClinicRequests'
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
        'awaitingLabCount' => $appointments
                                ->where('status', 'awaiting_lab_results')
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

/**
 * Accept clinic onboarding request
 */
public function acceptClinicRequest(\Illuminate\Http\Request $request)
{
    $vet = auth('vet')->user();

    \DB::table('clinic_vet')
        ->where('vet_id', $vet->id)
        ->where('clinic_id', $request->clinic_id)
        ->where('status', 'pending')
        ->update([
            'status' => 'accepted',
            'is_active' => 1,
            'updated_at' => now(),
        ]);

    return redirect()->route('vet.dashboard')
        ->with('success', 'Clinic onboarding accepted. You can now receive appointments from this clinic.');
}

/**
 * Reject clinic onboarding request
 */
public function rejectClinicRequest(\Illuminate\Http\Request $request)
{
    $vet = auth('vet')->user();

    \DB::table('clinic_vet')
        ->where('vet_id', $vet->id)
        ->where('clinic_id', $request->clinic_id)
        ->where('status', 'pending')
        ->update([
            'status' => 'rejected',
            'updated_at' => now(),
        ]);

    return redirect()->route('vet.dashboard')
        ->with('success', 'Clinic request declined.');
}
}