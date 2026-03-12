<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\PetParent;

class PetParentProfileController extends Controller
{
    public function show($id)
    {
        // Vet MUST be inside a clinic
        abort_if(!session()->has('active_clinic_id'), 403);

        $petParent = PetParent::with('pets')->findOrFail($id);

        return view('vet.pet_parents.show', compact('petParent'));
    }
}