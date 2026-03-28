<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\PetParent;
use Illuminate\Http\Request;

class VetPetHistoryController extends Controller
{
    /**
     * Show search screen
     */
    public function index()
    {
        return view('vet.pet_history.search');
    }

    /**
     * Show pet history by mobile number (READ ONLY)
     */
    public function show(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
        ]);

        $petParent = PetParent::where('phone', $request->mobile)
        ->with([
            'pets.appointments' => function ($q) {
                $q->whereIn('status', ['completed', 'awaiting_lab_results'])
                ->with([
                    'caseSheet',
                    'prescription.items',
                    'treatments.drugGeneric',
                    'clinic:id,name',
                    'vet:id,name',
                ])
                ->orderByDesc('scheduled_at');
            },
            'pets.vaccinations' => function ($q) {
                $q->orderByDesc('administered_date');
            },
            'pets.ipdAdmissions' => function ($q) {
                $q->with(['clinic:id,name', 'treatments', 'notes'])
                  ->orderByDesc('admission_date');
            }
        ])
        ->firstOrFail();

        return view('vet.pet_history.result', [
            'petParent' => $petParent,
        ]);
    }
}