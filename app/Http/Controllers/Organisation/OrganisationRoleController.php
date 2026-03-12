<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\OrganisationRole;
use Illuminate\Http\Request;

class OrganisationRoleController extends Controller
{
    public function index()
    {
        $roles = OrganisationRole::where(
            'organisation_id',
            auth()->user()->organisation_id
        )->get();

        return view('organisation.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = \App\Models\Permission::orderBy('name')->get();

        return view('organisation.roles.create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'clinic_scope' => 'required|in:none,single,multiple',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = OrganisationRole::create([
            'organisation_id' => auth()->user()->organisation_id,
            'name' => $request->name,
            'clinic_scope' => $request->clinic_scope
        ]);

        if ($request->filled('permissions')) {

            foreach ($request->permissions as $permissionId) {

                \DB::table('role_permissions')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            }
        }

        return redirect()
            ->route('organisation.roles.index')
            ->with('success', 'Role created successfully');
    }
    
    public function edit($id)
    {
        $role = OrganisationRole::where(
            'organisation_id',
            auth()->user()->organisation_id
        )->findOrFail($id);

        $permissions = \App\Models\Permission::orderBy('name')->get();

        $rolePermissions = \DB::table('role_permissions')
            ->where('role_id', $role->id)
            ->pluck('permission_id')
            ->toArray();

        return view('organisation.roles.edit', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function update(Request $request, $id)
{
    $role = OrganisationRole::where(
        'organisation_id',
        auth()->user()->organisation_id
    )->findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'clinic_scope' => 'required|in:none,single,multiple',
        'permissions' => 'nullable|array'
    ]);

    $role->update([
        'name' => $request->name,
        'clinic_scope' => $request->clinic_scope
    ]);

   // delete old permissions
\DB::table('role_permissions')
->where('role_id', $role->id)
->delete();

foreach ($request->permissions as $permissionId) {

\DB::table('role_permissions')->insert([
    'role_id' => $role->id,
    'permission_id' => $permissionId,
    'created_at' => now(),
    'updated_at' => now(),
]);

}

// DEBUG
dd('insert complete', $role->id, $request->permissions);
}

}