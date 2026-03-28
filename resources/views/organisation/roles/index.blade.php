@extends('organisation.layout')

@section('content')
<style>
.page-hdr { display:flex;justify-content:space-between;align-items:center;margin-bottom:20px; }
.page-hdr h2 { font-size:22px;font-weight:700;margin:0; }
.btn { padding:10px 16px;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px; }
.btn-primary { background:#4f46e5;color:#fff; }
.btn-primary:hover { background:#4338ca; }
.btn-sm { padding:6px 12px;font-size:12px; }
.btn-edit { background:#dbeafe;color:#1d4ed8; }
.btn-edit:hover { background:#bfdbfe; }
.btn-delete { background:#fee2e2;color:#991b1b; }
.btn-delete:hover { background:#fca5a5; }

.roles-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:14px; }
.role-card { background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px;transition:border-color .15s;position:relative; }
.role-card:hover { border-color:#4f46e5; }
.role-name { font-size:16px;font-weight:700;color:#111827; }
.role-scope { display:inline-block;padding:2px 10px;border-radius:10px;font-size:10px;font-weight:600;margin-left:6px; }
.scope-none { background:#f3f4f6;color:#6b7280; }
.scope-single { background:#dbeafe;color:#1d4ed8; }
.scope-multiple { background:#fef3c7;color:#92400e; }

.role-stats { display:flex;gap:16px;margin-top:10px; }
.role-stat { display:flex;align-items:center;gap:4px;font-size:12px;color:#6b7280; }
.role-stat .n { font-weight:700;color:#111827;font-size:14px; }

.perms-preview { margin-top:12px;display:flex;flex-wrap:wrap;gap:4px; }
.perm-tag { font-size:10px;padding:2px 8px;background:#f0fdf4;color:#166534;border-radius:8px;font-weight:500;border:1px solid #bbf7d0; }
.perm-tag--more { background:#f3f4f6;color:#6b7280;border-color:#e5e7eb; }

.role-actions { display:flex;gap:6px;margin-top:14px; }
.success-bar { background:#dcfce7;border:1px solid #bbf7d0;padding:10px 14px;border-radius:6px;margin-bottom:14px;color:#166534;font-size:14px; }
.empty-state { text-align:center;padding:40px;color:#9ca3af;font-size:14px;background:#fff;border-radius:10px;border:1px solid #e5e7eb; }

.scope-legend { display:flex;gap:16px;margin-bottom:14px;padding:10px 14px;background:#f8fafc;border:1px solid #e5e7eb;border-radius:8px; }
.scope-legend-item { display:flex;align-items:center;gap:6px;font-size:12px;color:#6b7280; }
.scope-dot { width:8px;height:8px;border-radius:50%;display:inline-block; }
</style>

<div class="page-hdr">
    <h2>Roles & Permissions</h2>
    <a href="{{ route('organisation.roles.create') }}" class="btn btn-primary">+ Create Role</a>
</div>

@if(session('success'))<div class="success-bar">✓ {{ session('success') }}</div>@endif

<div class="scope-legend">
    <div class="scope-legend-item"><span class="scope-dot" style="background:#9ca3af;"></span> Central (no clinic)</div>
    <div class="scope-legend-item"><span class="scope-dot" style="background:#2563eb;"></span> Single clinic</div>
    <div class="scope-legend-item"><span class="scope-dot" style="background:#f59e0b;"></span> Multiple clinics</div>
</div>

@if($roles->count())
<div class="roles-grid">
    @foreach($roles as $role)
    @php
        $permCount = DB::table('role_permissions')->where('role_id', $role->id)->count();
        $userCount = DB::table('organisation_user_roles')->where('role_id', $role->id)->count();
        $permNames = DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role_id', $role->id)
            ->pluck('permissions.name')
            ->take(6);
        $totalPerms = DB::table('role_permissions')->where('role_id', $role->id)->count();
    @endphp
    <div class="role-card">
        <div>
            <span class="role-name">{{ $role->name }}</span>
            <span class="role-scope scope-{{ $role->clinic_scope }}">
                @if($role->clinic_scope === 'none') Central
                @elseif($role->clinic_scope === 'single') Single Clinic
                @else Multi Clinic @endif
            </span>
        </div>

        <div class="role-stats">
            <div class="role-stat"><span class="n">{{ $userCount }}</span> user{{ $userCount !== 1 ? 's' : '' }}</div>
            <div class="role-stat"><span class="n">{{ $permCount }}</span> permission{{ $permCount !== 1 ? 's' : '' }}</div>
        </div>

        <div class="perms-preview">
            @foreach($permNames as $pn)
            <span class="perm-tag">{{ $pn }}</span>
            @endforeach
            @if($totalPerms > 6)
            <span class="perm-tag perm-tag--more">+{{ $totalPerms - 6 }} more</span>
            @endif
        </div>

        <div class="role-actions">
            <a href="{{ route('organisation.roles.edit', $role->id) }}" class="btn btn-sm btn-edit">Edit Permissions</a>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="empty-state">
    <p style="font-size:18px;margin-bottom:6px;">No roles created yet</p>
    <p>Create roles to define what your team members can access and manage.</p>
    <a href="{{ route('organisation.roles.create') }}" class="btn btn-primary" style="margin-top:12px;">+ Create Your First Role</a>
</div>
@endif
@endsection
