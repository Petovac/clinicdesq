<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\PetParentClinicAccess;

class PetProfileController extends Controller
{
    public function show($id)
    {
        $clinicId = session('active_clinic_id') ?? 1;

        $pet = Pet::with([
            'petParent',
            'appointments' => function ($q) {
                $q->orderBy('scheduled_at', 'desc');
            },
            'appointments.caseSheet',
            'appointments.prescription.items',
            'appointments.treatments.drugGeneric',
            'appointments.treatments.priceItem',
        ])->findOrFail($id);
        

        // 🔒 Ensure clinic has access to this pet parent
        $hasAccess = PetParentClinicAccess::where([
            'pet_parent_id' => $pet->pet_parent_id,
            'clinic_id'     => $clinicId,
        ])->exists();

        abort_if(!$hasAccess, 403);

        return view('vet.pets.show', compact('pet'));
    }
}
