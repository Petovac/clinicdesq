@extends('organisation.layout')

@section('content')
<style>
.card { background:#fff;border-radius:10px;padding:24px;max-width:700px;box-shadow:0 10px 25px rgba(0,0,0,0.05); }
.form-group { margin-bottom:16px; }
label { display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#374151; }
input, select { width:100%;padding:10px 12px;border-radius:8px;border:1px solid #d1d5db;font-size:14px; }
input:focus, select:focus { outline:none;border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,0.1); }
.hidden { display:none; }
.btn-primary { padding:10px 20px;border-radius:8px;border:none;cursor:pointer;background:#4f46e5;color:#fff;font-size:14px;font-weight:600; }
.btn-primary:hover { background:#4338ca; }
.btn-secondary { padding:10px 20px;border-radius:8px;border:none;cursor:pointer;background:#e5e7eb;color:#374151;font-size:14px;font-weight:600;text-decoration:none; }
.scope-hint { font-size:11px;color:#6b7280;margin-top:4px; }
.error-box { background:#fee2e2;padding:10px 14px;margin-bottom:16px;border-radius:8px;font-size:13px;color:#991b1b; }
</style>

<h2 style="font-size:22px;font-weight:600;margin-bottom:20px;">Edit User: {{ $user->name }}</h2>

<div class="card">
    @if($errors->any())
        <div class="error-box">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('organisation.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Name *</label>
            <input name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group">
            <label>Phone *</label>
            <input name="phone" value="{{ old('phone', $user->phone) }}" required>
        </div>

        <div class="form-group">
            <label>Email (optional)</label>
            <input name="email" type="email" value="{{ old('email', $user->email) }}">
        </div>

        <div class="form-group">
            <label>Role *</label>
            <select name="role_id" id="roleSelect" required>
                <option value="">Select role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                        data-scope="{{ $role->clinic_scope }}"
                        {{ old('role_id', $currentRoleId) == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <div class="scope-hint" id="scopeHint"></div>
        </div>

        {{-- Single clinic --}}
        <div class="form-group hidden" id="singleClinic">
            <label>Assign to Clinic *</label>
            <select name="clinic_id">
                <option value="">Select clinic</option>
                @foreach($clinics as $clinic)
                    <option value="{{ $clinic->id }}" {{ old('clinic_id', $user->clinic_id) == $clinic->id ? 'selected' : '' }}>
                        {{ $clinic->name }}{{ $clinic->city ? " ({$clinic->city})" : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Multi clinic --}}
        <div class="form-group hidden" id="multiClinic">
            <label>Assign to Clinics *</label>
            <select name="clinic_ids[]" multiple style="min-height:120px;">
                @foreach($clinics as $clinic)
                    <option value="{{ $clinic->id }}" {{ in_array($clinic->id, old('clinic_ids', $assignedClinicIds)) ? 'selected' : '' }}>
                        {{ $clinic->name }}{{ $clinic->city ? " ({$clinic->city})" : '' }}
                    </option>
                @endforeach
            </select>
            <div class="scope-hint">Hold Ctrl/Cmd to select multiple clinics</div>
        </div>

        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn-primary">Update User</button>
            <a href="{{ route('organisation.users.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
const roleSelect = document.getElementById('roleSelect');
const singleClinic = document.getElementById('singleClinic');
const multiClinic = document.getElementById('multiClinic');
const scopeHint = document.getElementById('scopeHint');

const scopeMessages = {
    'none': 'Central role — access to all clinics',
    'single': 'This role is assigned to a single clinic',
    'multiple': 'This role manages multiple clinics',
};

function updateClinicFields() {
    singleClinic.classList.add('hidden');
    multiClinic.classList.add('hidden');
    scopeHint.textContent = '';

    const selected = roleSelect.options[roleSelect.selectedIndex];
    if (!selected || !selected.value) return;

    const scope = selected.dataset.scope;
    scopeHint.textContent = scopeMessages[scope] || '';

    if (scope === 'single') singleClinic.classList.remove('hidden');
    if (scope === 'multiple') multiClinic.classList.remove('hidden');
}

roleSelect.addEventListener('change', updateClinicFields);
updateClinicFields();
</script>
@endsection
