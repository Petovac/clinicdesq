<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\OrganisationRole;
use App\Models\Permission;
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
        $groupedPermissions = Permission::grouped();

        return view('organisation.roles.create', [
            'groupedPermissions' => $groupedPermissions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'clinic_scope'   => 'required|in:none,single,multiple',
            'permissions'    => 'nullable|array',
            'permissions.*'  => 'exists:permissions,id',
        ]);

        $role = OrganisationRole::create([
            'organisation_id' => auth()->user()->organisation_id,
            'name'            => $request->name,
            'clinic_scope'    => $request->clinic_scope,
        ]);

        if ($request->filled('permissions')) {
            $rows = collect($request->permissions)->map(fn($pid) => [
                'role_id'       => $role->id,
                'permission_id' => $pid,
                'created_at'    => now(),
                'updated_at'    => now(),
            ])->toArray();

            \DB::table('role_permissions')->insert($rows);
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

        $groupedPermissions = Permission::grouped();

        $rolePermissions = \DB::table('role_permissions')
            ->where('role_id', $role->id)
            ->pluck('permission_id')
            ->toArray();

        return view('organisation.roles.edit', [
            'role'                => $role,
            'groupedPermissions'  => $groupedPermissions,
            'rolePermissions'     => $rolePermissions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = OrganisationRole::where(
            'organisation_id',
            auth()->user()->organisation_id
        )->findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:255',
            'clinic_scope'   => 'required|in:none,single,multiple',
            'permissions'    => 'nullable|array',
            'permissions.*'  => 'exists:permissions,id',
        ]);

        $role->update([
            'name'         => $request->name,
            'clinic_scope' => $request->clinic_scope,
        ]);

        // Sync permissions: delete old, insert new
        \DB::table('role_permissions')->where('role_id', $role->id)->delete();

        if ($request->filled('permissions')) {
            $rows = collect($request->permissions)->map(fn($pid) => [
                'role_id'       => $role->id,
                'permission_id' => $pid,
                'created_at'    => now(),
                'updated_at'    => now(),
            ])->toArray();

            \DB::table('role_permissions')->insert($rows);
        }

        return redirect()
            ->route('organisation.roles.index')
            ->with('success', 'Role updated successfully');
    }
}
