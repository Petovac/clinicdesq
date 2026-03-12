<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Appointment;

class VetAppointmentHistoryController extends Controller
{
    public function index()
{
    $vet = auth('vet')->user();
    abort_if(!$vet, 401);

    $appointments = Appointment::where('vet_id', $vet->id)
        ->where('status', 'completed')
        ->with([
            'clinic:id,name',
            'pet:id,name',
        ])
        ->orderByDesc('scheduled_at')
        ->get();

    return view('vet.appointments.history', [
        'appointments' => $appointments,
    ]);
}

public function show(Appointment $appointment)
{
    $vet = auth('vet')->user();
    abort_if(!$vet || $appointment->vet_id !== $vet->id, 403);

    abort_if($appointment->status !== 'completed', 403);

    $appointment->load([
        'clinic:id,name',
        'pet.petParent',
        'caseSheet',
        'prescription.items',
    ]);

    // 🔑 ADD THIS (pet history for modal + history section)
    $petHistory = Appointment::where('pet_id', $appointment->pet_id)
        ->where('id', '!=', $appointment->id)
        ->orderByDesc('scheduled_at')
        ->with(['caseSheet', 'prescription.items'])
        ->get();

    return view('vet.appointments.case', [
        'appointment' => $appointment,
        'petHistory'  => $petHistory,
        'readOnly'    => true,
    ]);
}
}