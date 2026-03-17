<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OrganisationUserController extends Controller
{


    public function create()
    {
        $roles = config('roles');

        $clinics = Clinic::where(
            'organisation_id',
            auth()->user()->organisation_id
        )->get();

        return view('organisation.users.create', [
            'roles' => $roles,
            'clinics' => $clinics,
        ]);
    }

    public function index()
    {
        abort_if(
            !auth()->user()->hasPermission('users.view'),
            403
        );

        $users = DB::table('organisation_user_roles as our')
            ->join('users as u', 'u.id', '=', 'our.user_id')
            ->join('organisation_roles as r', 'r.id', '=', 'our.role_id')
            ->leftJoin('clinics as c', 'c.id', '=', 'our.clinic_id')
            ->where('our.organisation_id', auth()->user()->organisation_id)
            ->select(
                'u.id',
                'u.name',
                'u.phone',
                'u.email',
                'r.name as role_name',
                'c.name as clinic_name'
            )
            ->get();

        return view('organisation.users.index', compact('users'));
    }

    public function assignForm(User $user)
    {
        abort_if(
            $user->organisation_id !== auth()->user()->organisation_id,
            403
        );

        abort_if(
            !in_array($user->role, ['regional_manager','area_manager']),
            422
        );

        $clinics = Clinic::where(
            'organisation_id',
            auth()->user()->organisation_id
        )->get();

        return view('organisation.users.assign', compact('user', 'clinics'));
    }


    public function store(Request $request)
    {
        // $roles = config('roles');

        // $creatorRole = auth()->user()->role;
        // $targetRole  = $request->role;

        // // Validate roles exist
        // abort_if(
        //     !isset($roles[$creatorRole]) || !isset($roles[$targetRole]),
        //     403,
        //     'Invalid role'
        // );

        // // Hierarchy enforcement
        // abort_if(
        //     $roles[$creatorRole] <= $roles[$targetRole],
        //     403,
        //     'You are not allowed to create this role'
        // );

        // Base validation
        $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20|unique:users,phone',
            'email'       => 'nullable|email|unique:users,email',
            'role'        => 'required|string',
            'clinic_id'   => 'nullable|exists:clinics,id',
            'clinic_ids'  => 'nullable|array',
            'clinic_ids.*'=> 'exists:clinics,id',
        ]);

        $targetRole = $request->role;

        $clinic = null;
        $clinicId = null;

        /*
        |--------------------------------------------------------------------------
        | Single-clinic roles
        |--------------------------------------------------------------------------
        */
        if ($request->clinic_id) {

            $clinic = Clinic::where('id', $request->clinic_id)
                ->where('organisation_id', auth()->user()->organisation_id)
                ->firstOrFail();
        
            $clinicId = $clinic->id;
        }

        /*
        |--------------------------------------------------------------------------
        | Create user
        |--------------------------------------------------------------------------
        */
        $user = User::create([
            'name'            => $request->name,
            'phone'           => $request->phone,
            'email'           => $request->email,
            'role'            => $targetRole,
            'organisation_id' => auth()->user()->organisation_id,
            'clinic_id'       => $clinicId,
            'password'        => Hash::make('changeme123'),
        ]);

        // Map config role key to OrganisationRole display name
        $roleName = ucwords(str_replace('_', ' ', $targetRole));
        $role = \App\Models\OrganisationRole::where(
            'organisation_id',
            auth()->user()->organisation_id
        )
        ->where('name', $roleName)
        ->first();

        // Create OrganisationUserRole if matching role exists
        if ($role) {
            \App\Models\OrganisationUserRole::create([
                'organisation_id' => auth()->user()->organisation_id,
                'clinic_id'       => $clinicId,
                'user_id'         => $user->id,
                'role_id'         => $role->id,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Clinic manager ownership (keep existing behaviour)
        |--------------------------------------------------------------------------
        */
        if ($targetRole === 'clinic_manager' && $clinic) {

            // Remove previous manager if exists
            if ($clinic->user_id) {
                User::where('id', $clinic->user_id)->update([
                    'clinic_id' => null,
                ]);
            }

            $clinic->update([
                'user_id' => $user->id,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Multi-clinic roles (assign at creation time)
        |--------------------------------------------------------------------------
        */
        if (
            in_array($targetRole, ['regional_manager', 'area_manager']) &&
            $request->filled('clinic_ids')
        ) {
            $user->assignedClinics()->sync($request->clinic_ids);
        }
        
            return redirect()
        ->route('organisation.users.index')
        ->with('success', 'User created successfully');
    }


        public function edit(User $user)
        {
            abort_if(
                $user->organisation_id !== auth()->user()->organisation_id,
                403
            );

            $roles = config('roles');
            $clinics = Clinic::where('organisation_id', auth()->user()->organisation_id)->get();

            return view('organisation.users.edit', compact('user', 'roles', 'clinics'));
        }


        public function update(Request $request, User $user)
        {
            $roles = config('roles');

            abort_if(
                $user->organisation_id !== auth()->user()->organisation_id,
                403
            );

            $creatorRole = auth()->user()->role;
            $targetRole  = $request->role;

            abort_if(
                $roles[$creatorRole] <= $roles[$targetRole],
                403,
                'You cannot assign this role'
            );

            $request->validate([
                'name'        => 'required|string|max:255',
                'phone'       => 'required|string|max:20|unique:users,phone,' . $user->id,
                'email'       => 'nullable|email|unique:users,email,' . $user->id,
                'role'        => 'required|in:' . implode(',', array_keys($roles)),
                'clinic_id'   => 'nullable|exists:clinics,id',
                'clinic_ids'  => 'nullable|array',
                'clinic_ids.*'=> 'exists:clinics,id',
            ]);

            $clinicId = null;
            $clinic = null;

            /*
            |--------------------------------------------------------------------------
            | Single clinic roles
            |--------------------------------------------------------------------------
            */
            if (in_array($targetRole, ['clinic_manager','receptionist','sales'])) {

                abort_if(empty($request->clinic_id), 422, 'Clinic required');

                $clinic = Clinic::where('id', $request->clinic_id)
                    ->where('organisation_id', auth()->user()->organisation_id)
                    ->firstOrFail();

                $clinicId = $clinic->id;

                // Clear multi-clinic assignments
                $user->assignedClinics()->detach();
            }

            /*
            |--------------------------------------------------------------------------
            | Multi clinic roles
            |--------------------------------------------------------------------------
            */
            if (in_array($targetRole, ['regional_manager','area_manager'])) {
                $clinicId = null;
            }

            /*
            |--------------------------------------------------------------------------
            | Update user
            |--------------------------------------------------------------------------
            */
            $user->update([
                'name'      => $request->name,
                'phone'     => $request->phone,
                'email'     => $request->email,
                'role'      => $targetRole,
                'clinic_id' => $clinicId,
            ]);
            
            $roleName = ucwords(str_replace('_', ' ', $targetRole));
            $orgRole = \App\Models\OrganisationRole::where(
                'organisation_id',
                auth()->user()->organisation_id
            )->where('name', $roleName)->first();

            if ($orgRole) {
                \App\Models\OrganisationUserRole::updateOrCreate(
                    ['user_id' => $user->id],
                    ['role_id' => $orgRole->id, 'clinic_id' => $clinicId, 'organisation_id' => auth()->user()->organisation_id]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Clinic manager ownership logic
            |--------------------------------------------------------------------------
            */
            if ($targetRole === 'clinic_manager' && $clinic) {

                if ($clinic->user_id && $clinic->user_id !== $user->id) {
                    User::where('id', $clinic->user_id)->update([
                        'clinic_id' => null,
                    ]);
                }

                $clinic->update(['user_id' => $user->id]);
            }

            /*
            |--------------------------------------------------------------------------
            | Sync multi clinics
            |--------------------------------------------------------------------------
            */
            if (
                in_array($targetRole, ['regional_manager','area_manager']) &&
                $request->filled('clinic_ids')
            ) {
                $user->assignedClinics()->sync($request->clinic_ids);
            }

            return redirect()
                ->route('organisation.users.index')
                ->with('success', 'User updated successfully');
        }

}
