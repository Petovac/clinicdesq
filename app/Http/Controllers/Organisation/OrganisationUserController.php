<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Clinic;
use App\Models\OrganisationRole;
use App\Models\OrganisationUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OrganisationUserController extends Controller
{
    public function index()
    {
        abort_if(!auth()->user()->hasPermission('users.view'), 403);

        $orgId = auth()->user()->organisation_id;

        $users = DB::table('organisation_user_roles as our')
            ->join('users as u', 'u.id', '=', 'our.user_id')
            ->join('organisation_roles as r', 'r.id', '=', 'our.role_id')
            ->leftJoin('clinics as c', 'c.id', '=', 'our.clinic_id')
            ->where('our.organisation_id', $orgId)
            ->select('u.id', 'u.name', 'u.phone', 'u.email', 'u.is_active',
                     'r.name as role_name', 'c.name as clinic_name')
            ->get();

        return view('organisation.users.index', compact('users'));
    }

    public function create()
    {
        $orgId = auth()->user()->organisation_id;

        $roles = OrganisationRole::where('organisation_id', $orgId)
            ->orderBy('name')
            ->get();

        $clinics = Clinic::where('organisation_id', $orgId)->get();

        return view('organisation.users.create', compact('roles', 'clinics'));
    }

    public function store(Request $request)
    {
        $orgId = auth()->user()->organisation_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'role_id' => 'required|exists:organisation_roles,id',
            'clinic_id' => 'nullable|exists:clinics,id',
            'clinic_ids' => 'nullable|array',
            'clinic_ids.*' => 'exists:clinics,id',
        ]);

        // Verify role belongs to this org
        $role = OrganisationRole::where('id', $request->role_id)
            ->where('organisation_id', $orgId)
            ->firstOrFail();

        // Validate clinic requirement based on scope
        if ($role->clinic_scope === 'single' && !$request->clinic_id) {
            return back()->withErrors(['clinic_id' => 'Please select a clinic for this role.'])->withInput();
        }
        if ($role->clinic_scope === 'multiple' && empty($request->clinic_ids)) {
            return back()->withErrors(['clinic_ids' => 'Please select at least one clinic for this role.'])->withInput();
        }

        $clinicId = null;
        if ($role->clinic_scope === 'single' && $request->clinic_id) {
            $clinic = Clinic::where('id', $request->clinic_id)
                ->where('organisation_id', $orgId)->firstOrFail();
            $clinicId = $clinic->id;
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role' => $role->name, // store role name for display
            'organisation_id' => $orgId,
            'clinic_id' => $clinicId,
            'password' => Hash::make('changeme123'),
        ]);

        // Create the org-user-role assignment (THIS is what powers permissions)
        OrganisationUserRole::create([
            'organisation_id' => $orgId,
            'user_id' => $user->id,
            'role_id' => $role->id,
            'clinic_id' => $clinicId,
        ]);

        // Multi-clinic assignment
        if ($role->clinic_scope === 'multiple' && $request->filled('clinic_ids')) {
            $user->assignedClinics()->sync($request->clinic_ids);
        }

        return redirect()->route('organisation.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        abort_if($user->organisation_id !== auth()->user()->organisation_id, 403);

        $orgId = auth()->user()->organisation_id;

        $roles = OrganisationRole::where('organisation_id', $orgId)
            ->orderBy('name')
            ->get();

        $clinics = Clinic::where('organisation_id', $orgId)->get();

        // Get user's current role assignment
        $currentAssignment = OrganisationUserRole::where('user_id', $user->id)
            ->where('organisation_id', $orgId)
            ->first();

        $currentRoleId = $currentAssignment ? $currentAssignment->role_id : null;
        $assignedClinicIds = $user->assignedClinics()->pluck('clinics.id')->toArray();

        return view('organisation.users.edit', compact('user', 'roles', 'clinics', 'currentRoleId', 'assignedClinicIds'));
    }

    public function update(Request $request, User $user)
    {
        $orgId = auth()->user()->organisation_id;
        abort_if($user->organisation_id !== $orgId, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:organisation_roles,id',
            'clinic_id' => 'nullable|exists:clinics,id',
            'clinic_ids' => 'nullable|array',
            'clinic_ids.*' => 'exists:clinics,id',
        ]);

        $role = OrganisationRole::where('id', $request->role_id)
            ->where('organisation_id', $orgId)
            ->firstOrFail();

        $clinicId = null;
        if ($role->clinic_scope === 'single' && $request->clinic_id) {
            $clinic = Clinic::where('id', $request->clinic_id)
                ->where('organisation_id', $orgId)->firstOrFail();
            $clinicId = $clinic->id;
        }

        // Update user
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role' => $role->name,
            'clinic_id' => $clinicId,
        ]);

        // Update role assignment
        OrganisationUserRole::updateOrCreate(
            ['user_id' => $user->id, 'organisation_id' => $orgId],
            ['role_id' => $role->id, 'clinic_id' => $clinicId]
        );

        // Handle clinic assignments
        if ($role->clinic_scope === 'multiple' && $request->filled('clinic_ids')) {
            $user->assignedClinics()->sync($request->clinic_ids);
        } elseif ($role->clinic_scope !== 'multiple') {
            $user->assignedClinics()->detach();
        }

        return redirect()->route('organisation.users.index')
            ->with('success', 'User updated successfully.');
    }
}
