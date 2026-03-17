<?php

namespace App\Http\Controllers\PetParent;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use Illuminate\Http\Request;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $parent = auth('pet_parent')->user();
        $pets = $parent->pets()->orderBy('name')->get();

        return view('parent.dashboard', compact('parent', 'pets'));
    }

    public function showPet(Pet $pet)
    {
        abort_if($pet->pet_parent_id !== auth('pet_parent')->id(), 403);

        $appointments = Appointment::where('pet_id', $pet->id)
            ->with([
                'clinic',
                'vet',
                'prescription.items',
                'caseSheet',
                'bill.items',
            ])
            ->orderByDesc('scheduled_at')
            ->get();

        return view('parent.pets.show', compact('pet', 'appointments'));
    }

    public function showAppointment(Appointment $appointment)
    {
        abort_if(
            $appointment->pet->pet_parent_id !== auth('pet_parent')->id(),
            403
        );

        $appointment->load([
            'clinic',
            'vet',
            'prescription.items',
            'caseSheet',
            'bill.items',
            'treatments.drugGeneric',
        ]);

        return view('parent.appointments.show', compact('appointment'));
    }
}
