<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\PetVaccination;
use App\Models\DrugGeneric;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VaccinationController extends Controller
{
    /**
     * Store vaccination(s) from case sheet
     */
    public function store(Request $request)
    {
        $request->validate([
            'vaccinations' => 'required|array|min:1',
            'vaccinations.*.pet_id' => 'required|exists:pets,id',
            'vaccinations.*.vaccine_name' => 'required|string|max:255',
            'vaccinations.*.administered_date' => 'required|date',
        ]);

        $vet = Auth::guard('vet')->user();
        $clinicId = session('active_clinic_id');

        $created = [];
        foreach ($request->vaccinations as $v) {
            $created[] = PetVaccination::create([
                'pet_id' => $v['pet_id'],
                'appointment_id' => $v['appointment_id'] ?? null,
                'clinic_id' => $clinicId,
                'vet_id' => $vet->id,
                'vaccine_name' => $v['vaccine_name'],
                'brand_name' => $v['brand_name'] ?? null,
                'manufacturer' => $v['manufacturer'] ?? null,
                'batch_number' => $v['batch_number'] ?? null,
                'dose_number' => $v['dose_number'] ?? '1st',
                'administered_date' => $v['administered_date'],
                'next_due_date' => $v['next_due_date'] ?? null,
                'route' => $v['route'] ?? null,
                'site' => $v['site'] ?? null,
                'notes' => $v['notes'] ?? null,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'count' => count($created)]);
        }

        return back()->with('success', count($created) . ' vaccination(s) recorded.');
    }

    /**
     * Get vaccination history for a pet (AJAX)
     */
    public function history(Pet $pet)
    {
        $vaccinations = $pet->vaccinations()->with('vet', 'clinic')->get();

        return response()->json($vaccinations->map(fn($v) => [
            'id' => $v->id,
            'vaccine_name' => $v->vaccine_name,
            'brand_name' => $v->brand_name,
            'manufacturer' => $v->manufacturer,
            'dose_number' => $v->dose_number,
            'administered_date' => $v->administered_date->format('d M Y'),
            'next_due_date' => $v->next_due_date?->format('d M Y'),
            'is_overdue' => $v->isOverdue(),
            'is_due_soon' => $v->isDueSoon(),
            'batch_number' => $v->batch_number,
            'route' => $v->route,
            'vet_name' => $v->vet->name ?? '—',
            'clinic_name' => $v->clinic->name ?? '—',
            'notes' => $v->notes,
        ]));
    }

    /**
     * Delete a vaccination record
     */
    public function destroy(PetVaccination $vaccination)
    {
        $vaccination->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Vaccination record deleted.');
    }

    /**
     * Search vaccine generics from KB (AJAX)
     */
    public function searchVaccines(Request $request)
    {
        $q = $request->get('q', '');

        $generics = DrugGeneric::where('drug_class', 'like', '%vaccine%')
            ->where('name', 'like', "%{$q}%")
            ->with('brands')
            ->limit(20)
            ->get();

        return response()->json($generics->map(fn($g) => [
            'id' => $g->id,
            'name' => $g->name,
            'brands' => $g->brands->map(fn($b) => [
                'id' => $b->id,
                'brand_name' => $b->brand_name,
                'manufacturer' => $b->manufacturer,
            ]),
        ]));
    }
}
