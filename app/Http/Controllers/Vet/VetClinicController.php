<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Clinic;

class VetClinicController extends Controller
{
    public function index()
    {
        $vet = auth('vet')->user();
        abort_if(!$vet, 401);

        $clinicId = session('active_clinic_id');
        abort_if(!$clinicId, 403);

        // Ensure vet still belongs to this clinic
        $clinic = Clinic::where('id', $clinicId)
            ->whereHas('vets', function ($q) use ($vet) {
                $q->where('clinic_vet.vet_id', $vet->id)
                  ->where('clinic_vet.is_active', 1)
                  ->whereNull('clinic_vet.offboarded_at');
            })
            ->firstOrFail();

        // Appointments for this vet in this clinic
        $appointments = Appointment::where('clinic_id', $clinicId)
        ->where('vet_id', $vet->id)
        ->whereNotIn('status', ['completed','cancelled'])
        ->orderBy('appointment_number')
        ->get();

        return view('vet.clinic.dashboard', [
            'clinic'       => $clinic,
            'appointments' => $appointments,
            'readOnly' => false,
        ]);
    }

    public function markComplete(Appointment $appointment)
    {
        $vetId = auth('vet')->id();
    
        abort_if($appointment->vet_id !== $vetId, 403);
    
        $appointment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    
        return redirect()
            ->route('vet.dashboard')
            ->with('success', 'Appointment marked as completed.');
    }
}