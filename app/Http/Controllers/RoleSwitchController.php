<?php

namespace App\Http\Controllers;

use App\Models\Vet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleSwitchController extends Controller
{
    /**
     * Vet → Clinic Panel switch
     */
    public function switchToClinic(Request $request)
    {
        $vet = Auth::guard('vet')->user();

        if (!$vet || !$vet->linked_user_id) {
            return redirect()->back()->with('error', 'No linked clinic account found.');
        }

        // Verify vet has can_manage_clinic permission
        $clinicId = session('active_clinic_id');
        $pivot = DB::table('clinic_vet')
            ->where('vet_id', $vet->id)
            ->where('clinic_id', $clinicId)
            ->where('can_manage_clinic', true)
            ->first();

        if (!$pivot) {
            return redirect()->back()->with('error', 'You do not have clinic management access.');
        }

        $user = User::find($vet->linked_user_id);
        if (!$user) {
            return redirect()->back()->with('error', 'Linked user account not found.');
        }

        // Store vet session info for switching back
        session(['switch_from_vet_id' => $vet->id]);

        // Login as the clinic user
        Auth::guard('web')->login($user);

        // Set the active clinic
        session(['active_clinic_id' => $clinicId]);

        return redirect('/clinic/dashboard')->with('success', 'Switched to Clinic Panel');
    }

    /**
     * Clinic Panel → Vet switch
     */
    public function switchToVet(Request $request)
    {
        $user = Auth::guard('web')->user();

        if (!$user || !$user->linked_vet_id) {
            return redirect()->back()->with('error', 'No linked vet account found.');
        }

        $vet = Vet::find($user->linked_vet_id);
        if (!$vet) {
            return redirect()->back()->with('error', 'Linked vet account not found.');
        }

        $clinicId = session('active_clinic_id');

        // Login as vet
        Auth::guard('vet')->login($vet);

        // Keep clinic context
        session(['active_clinic_id' => $clinicId]);

        // Clear switch marker
        session()->forget('switch_from_vet_id');

        return redirect('/vet/dashboard')->with('success', 'Switched to Vet Panel');
    }

    /**
     * Auto-create linked user account for a vet (called by org admin)
     */
    public static function createLinkedUser(Vet $vet, int $organisationId, int $clinicId): User
    {
        // Check if already linked
        if ($vet->linked_user_id) {
            $existing = User::find($vet->linked_user_id);
            if ($existing) return $existing;
        }

        // Check if user with same phone exists
        $user = User::where('phone', $vet->phone)->first();

        if (!$user) {
            $user = User::create([
                'name' => $vet->name,
                'phone' => $vet->phone,
                'email' => $vet->email,
                'password' => bcrypt('linked_' . $vet->phone . '_vet'),
                'organisation_id' => $organisationId,
                'clinic_id' => $clinicId,
                'linked_vet_id' => $vet->id,
            ]);

            // Assign to clinic
            DB::table('clinic_user_assignments')->insertOrIgnore([
                'clinic_id' => $clinicId,
                'user_id' => $user->id,
            ]);
        } else {
            // Link existing user to vet
            $user->update(['linked_vet_id' => $vet->id]);
        }

        // Link vet to user
        $vet->update(['linked_user_id' => $user->id]);

        // Auto-assign Clinic Manager role (or first available role with dashboard.view)
        $managerRole = DB::table('organisation_roles')
            ->where('organisation_id', $organisationId)
            ->where('name', 'LIKE', '%Manager%')
            ->first();

        if (!$managerRole) {
            // Fallback: find any role with dashboard.view permission
            $managerRole = DB::table('organisation_roles')
                ->where('organisation_id', $organisationId)
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                       ->from('role_permissions')
                       ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                       ->whereColumn('role_permissions.role_id', 'organisation_roles.id')
                       ->where('permissions.slug', 'dashboard.view');
                })
                ->first();
        }

        if ($managerRole) {
            DB::table('organisation_user_roles')->insertOrIgnore([
                'user_id' => $user->id,
                'role_id' => $managerRole->id,
                'organisation_id' => $organisationId,
                'clinic_id' => $clinicId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $user;
    }
}
