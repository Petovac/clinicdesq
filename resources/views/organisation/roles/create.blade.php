@extends('organisation.layout')

@section('content')

<style>
.page-title { font-size: 22px; font-weight: 600; margin-bottom: 20px; }
.role-form { max-width: 900px; }
.form-card { background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 24px; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
.form-group input, .form-group select {
    width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 14px; background: #fff;
}
.form-group input:focus, .form-group select:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 2px rgba(79,70,229,0.15); }

.perm-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-top: 16px; }
@media (max-width: 768px) { .perm-grid { grid-template-columns: 1fr; } }

.perm-card {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.perm-card-header {
    display: flex; justify-content: space-between; align-items: center;
    padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px solid #f3f4f6;
}
.perm-card-title { font-size: 14px; font-weight: 700; color: #1f2937; }
.perm-card-toggle { font-size: 11px; color: #6366f1; cursor: pointer; font-weight: 500; }
.perm-card-toggle:hover { text-decoration: underline; }
.perm-item { display: flex; align-items: center; gap: 8px; padding: 5px 0; }
.perm-item input[type="checkbox"] { width: 16px; height: 16px; accent-color: #4f46e5; cursor: pointer; }
.perm-item label { font-size: 13px; color: #374151; cursor: pointer; margin: 0; }

.btn-primary {
    background: #4f46e5; color: #fff; padding: 10px 20px; border: none; border-radius: 8px;
    font-size: 14px; font-weight: 500; cursor: pointer; margin-top: 8px;
}
.btn-primary:hover { background: #4338ca; }
</style>

<h2 class="page-title">Create Role</h2>

<div class="role-form">
    <form method="POST" action="{{ route('organisation.roles.store') }}">
        @csrf

        <div class="form-card">
            <div class="form-group">
                <label>Role Name</label>
                <input type="text" name="name" required placeholder="e.g. Clinic Manager">
            </div>

            <div class="form-group">
                <label>Clinic Scope</label>
                <select name="clinic_scope" required>
                    <option value="none">No Clinic (Central Role)</option>
                    <option value="single">Single Clinic (Manager / Receptionist)</option>
                    <option value="multiple">Multiple Clinics (Area / Regional)</option>
                </select>
            </div>
        </div>

        <h3 style="font-size:16px;font-weight:600;color:#1f2937;margin-bottom:4px;">Permissions</h3>
        <p style="font-size:12px;color:#6b7280;margin-bottom:12px;">Select what this role can access and manage.</p>

        @php
            $centralSlugs = ['clinics.view','clinics.manage','roles.view','roles.manage','users.view','users.manage','vets.view','vets.assign','pricing.view','pricing.manage','settings.manage','whatsapp.manage','webhooks.manage','inventory.metrics','billing.metrics','doctors.performance_view'];
        @endphp

        <div class="perm-grid">
            @foreach($groupedPermissions as $group => $perms)
                <div class="perm-card">
                    <div class="perm-card-header">
                        <span class="perm-card-title">{{ $group }}</span>
                        <span class="perm-card-toggle" onclick="toggleGroup(this)">Select All</span>
                    </div>
                    @foreach($perms as $perm)
                        <div class="perm-item">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}">
                            <label for="perm_{{ $perm->id }}">
                                {{ $perm->name }}
                                @if(in_array($perm->slug, $centralSlugs))
                                    <span style="font-size:10px;color:#7c3aed;background:#f5f3ff;padding:1px 6px;border-radius:4px;margin-left:4px;font-weight:600;">Org-wide</span>
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn-primary">Create Role</button>
        </div>
    </form>
</div>

<script>
function toggleGroup(el) {
    const card = el.closest('.perm-card');
    const boxes = card.querySelectorAll('input[type="checkbox"]');
    const allChecked = Array.from(boxes).every(cb => cb.checked);
    boxes.forEach(cb => cb.checked = !allChecked);
    el.textContent = allChecked ? 'Select All' : 'Deselect All';
}
</script>

@endsection
