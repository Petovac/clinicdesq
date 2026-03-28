<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Vet;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganisationVetController extends Controller
{
    /**
     * Vet search & listing
     */
    public function index(Request $request)
    {
        $orgId = auth()->user()->organisation_id;

        // Vets already working with this org
        $assignedVets = \App\Models\Vet::whereHas('clinics', function ($q) use ($orgId) {
            $q->where('organisation_id', $orgId)
            ->where('clinic_vet.is_active', 1);
        })
        ->with(['clinics' => function ($q) use ($orgId) {
            $q->where('organisation_id', $orgId);
        }])
        ->get();

        $searchedVet = null;

        if ($request->filled('q')) {
            $searchedVet = \App\Models\Vet::where('email', $request->q)
                ->orWhere('phone', $request->q)
                ->orWhere('registration_number', $request->q)
                ->first();
        }

        return view('organisation.vets.index', compact(
            'assignedVets',
            'searchedVet'
        ));
    }

    /**
     * View vet profile (read-only)
     */
    public function show(Vet $vet)
    {
        $clinics = Clinic::where('organisation_id', auth()->user()->organisation_id)->get();

        $assignedClinicIds = DB::table('clinic_vet')
            ->where('vet_id', $vet->id)
            ->whereIn('clinic_id', $clinics->pluck('id'))
            ->where('is_active', 1)
            ->pluck('clinic_id')
            ->toArray();

        $manageClinicIds = DB::table('clinic_vet')
            ->where('vet_id', $vet->id)
            ->whereIn('clinic_id', $clinics->pluck('id'))
            ->where('is_active', 1)
            ->where('can_manage_clinic', 1)
            ->pluck('clinic_id')
            ->toArray();

        return view('organisation.vets.show', compact(
            'vet',
            'clinics',
            'assignedClinicIds',
            'manageClinicIds'
        ));
    }

    /**
     * Assign / remove clinics for vet
     */
    public function assignClinics(Request $request, Vet $vet)
    {
        $request->validate([
            'clinic_ids' => 'nullable|array',
            'clinic_ids.*' => 'exists:clinics,id',
            'manage_clinic_ids' => 'nullable|array',
            'manage_clinic_ids.*' => 'exists:clinics,id',
        ]);

        $clinicIds = $request->clinic_ids ?? [];
        $manageClinicIds = $request->manage_clinic_ids ?? [];
        $orgId = auth()->user()->organisation_id;

        $orgClinicIds = Clinic::where('organisation_id', $orgId)
            ->pluck('id')
            ->toArray();

        foreach ($orgClinicIds as $clinicId) {

            if (in_array($clinicId, $clinicIds)) {
                $canManage = in_array($clinicId, $manageClinicIds);

                // Check if already exists
                $existing = DB::table('clinic_vet')
                    ->where('clinic_id', $clinicId)
                    ->where('vet_id', $vet->id)
                    ->first();

                if ($existing) {
                    // Update existing record
                    DB::table('clinic_vet')
                        ->where('id', $existing->id)
                        ->update([
                            'is_active' => 1,
                            'can_manage_clinic' => $canManage,
                            'updated_at' => now(),
                        ]);
                } else {
                    // New assignment — send as pending request
                    DB::table('clinic_vet')->insert([
                        'clinic_id' => $clinicId,
                        'vet_id' => $vet->id,
                        'is_active' => 0,
                        'status' => 'pending',
                        'can_manage_clinic' => $canManage,
                        'role' => 'vet',
                        'created_by' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Auto-create linked user account if can_manage_clinic enabled
                if ($canManage) {
                    \App\Http\Controllers\RoleSwitchController::createLinkedUser($vet, $orgId, $clinicId);
                }
            } else {
                DB::table('clinic_vet')
                    ->where('clinic_id', $clinicId)
                    ->where('vet_id', $vet->id)
                    ->update([
                        'is_active' => 0,
                        'can_manage_clinic' => false,
                        'updated_at'=> now(),
                    ]);
            }
        }

        return redirect()
        ->route('organisation.vets.index')
        ->with('success', 'Vet clinics updated successfully');
    }

    public function offboard(Request $request, Vet $vet)
    {
        $orgId = auth()->user()->organisation_id;

        \DB::table('clinic_vet')
            ->where('vet_id', $vet->id)
            ->whereIn('clinic_id', function ($q) use ($orgId) {
                $q->select('id')
                ->from('clinics')
                ->where('organisation_id', $orgId);
            })
            ->update([
                'is_active' => 0,
                'offboarded_at' => now(),
            ]);

        return redirect()
            ->route('organisation.vets.index')
            ->with('success', 'Vet offboarded successfully');
    }
}