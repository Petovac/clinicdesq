@extends('organisation.layout')

@section('content')
<style>
.card { border:1px solid #e5e7eb; padding:20px; border-radius:10px; background:#fff; margin-bottom:20px; }
.clinic-row { display:flex; align-items:center; gap:16px; padding:10px 0; border-bottom:1px solid #f3f4f6; }
.clinic-row:last-child { border-bottom:none; }
.clinic-name { font-weight:600; font-size:14px; min-width:180px; }
.manage-label { font-size:12px; color:#6b7280; display:flex; align-items:center; gap:4px; }
.manage-label input { accent-color:#2563eb; }
.info-badge { display:inline-block; background:#dbeafe; color:#1d4ed8; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:600; }
</style>

<h2 style="font-size:20px;font-weight:700;margin-bottom:16px;">Vet Profile</h2>

<div class="card">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:14px;">
        <p><strong>Name:</strong> {{ $vet->name }}</p>
        <p><strong>Phone:</strong> {{ $vet->phone ?? '-' }}</p>
        <p><strong>Registration No:</strong> {{ $vet->registration_number ?? '-' }}</p>
        <p><strong>Specialization:</strong> {{ $vet->specialization ?? '-' }}</p>
        <p><strong>Degree:</strong> {{ $vet->degree ?? '-' }}</p>
        <p><strong>Experience:</strong> {{ $vet->experience ?? '-' }}</p>
    </div>
</div>

<div class="card">
    <h3 style="font-size:16px;font-weight:700;margin:0 0 14px;">Assign Clinics</h3>

    <form method="POST" action="{{ route('organisation.vets.assignClinics', $vet) }}">
        @csrf

        @foreach($clinics as $clinic)
        <div class="clinic-row">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="clinic_ids[]" value="{{ $clinic->id }}"
                    {{ in_array($clinic->id, $assignedClinicIds) ? 'checked' : '' }}
                    style="width:16px;height:16px;accent-color:#2563eb;"
                    onchange="toggleManage(this, {{ $clinic->id }})">
                <span class="clinic-name">{{ $clinic->name }}</span>
            </label>

            <label class="manage-label" id="manage-{{ $clinic->id }}" style="{{ in_array($clinic->id, $assignedClinicIds) ? '' : 'opacity:0.4;pointer-events:none;' }}">
                <input type="checkbox" name="manage_clinic_ids[]" value="{{ $clinic->id }}"
                    {{ in_array($clinic->id, $manageClinicIds ?? []) ? 'checked' : '' }}>
                Can manage clinic panel
                <span class="info-badge" title="Allows vet to switch to clinic panel without separate login">⇄ Switch</span>
            </label>
        </div>
        @endforeach

        <div style="margin-top:16px;font-size:12px;color:#6b7280;background:#f8fafc;padding:10px;border-radius:6px;">
            <strong>Can manage clinic panel:</strong> When enabled, the vet will see a "Switch to Clinic Panel" button in their vet dashboard.
            This is ideal for solo-doctor clinics where the vet also handles billing, inventory, and appointment management.
            A linked staff account is auto-created for seamless switching.
        </div>

        <button type="submit" style="margin-top:14px;background:#2563eb;color:#fff;padding:10px 20px;border:none;border-radius:6px;font-weight:600;cursor:pointer;">
            Save Assignments
        </button>
    </form>
</div>

<div class="card">
    <form method="POST" action="{{ route('organisation.vets.offboard', $vet) }}" onsubmit="return confirm('Are you sure you want to offboard this vet from all clinics?');">
        @csrf
        <button type="submit" style="background:#fee2e2;color:#dc2626;padding:8px 16px;border:1px solid #fecaca;border-radius:6px;font-weight:600;cursor:pointer;">
            Offboard Vet
        </button>
    </form>
</div>

<script>
function toggleManage(checkbox, clinicId) {
    const manageEl = document.getElementById('manage-' + clinicId);
    if (checkbox.checked) {
        manageEl.style.opacity = '1';
        manageEl.style.pointerEvents = 'auto';
    } else {
        manageEl.style.opacity = '0.4';
        manageEl.style.pointerEvents = 'none';
        manageEl.querySelector('input').checked = false;
    }
}
</script>
@endsection
