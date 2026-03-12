@extends('organisation.layout')

@section('content')

<style>
.page-title {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 20px;
}

.card {
    background: #ffffff;
    max-width: 720px;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-size: 14px;
    background: #fff;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 1px rgba(79,70,229,0.3);
}

.form-group select[multiple] {
    height: 120px;
}

.hidden {
    display: none;
}

.actions {
    margin-top: 24px;
    display: flex;
    gap: 12px;
}

.btn {
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 14px;
    text-decoration: none;
    cursor: pointer;
    border: none;
}

.btn-primary {
    background: #4f46e5;
    color: #fff;
}

.btn-secondary {
    background: #e5e7eb;
    color: #111827;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.hint {
    font-size: 12px;
    color: #6b7280;
    margin-top: 4px;
}
</style>

<h2 class="page-title">Edit User</h2>

<div class="card">
    <form method="POST" action="{{ route('organisation.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Name</label>
            <input name="name" value="{{ $user->name }}" required>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input name="phone" value="{{ $user->phone }}" required>
        </div>

        <div class="form-group">
            <label>Email (optional)</label>
            <input name="email" value="{{ $user->email }}">
        </div>

        <div class="form-group">
            <label>Role</label>
            <select name="role" id="roleSelect" required>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}"
                        @selected($user->role === $role->name)>
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Single clinic --}}
        <div class="form-group hidden" id="singleClinic">
            <label>Clinic</label>
            <select name="clinic_id">
                <option value="">Select clinic</option>
                @foreach($clinics as $clinic)
                    <option value="{{ $clinic->id }}"
                        @selected($user->clinic_id === $clinic->id)>
                        {{ $clinic->name }}
                    </option>
                @endforeach
            </select>
            <div class="hint">Applies to clinic manager, receptionist, sales</div>
        </div>

        {{-- Multi clinic --}}
        <div class="form-group hidden" id="multiClinic">
            <label>Assigned Clinics</label>
            <select name="clinic_ids[]" multiple>
                @foreach($clinics as $clinic)
                    <option value="{{ $clinic->id }}"
                        @selected($user->assignedClinics->contains($clinic->id))>
                        {{ $clinic->name }}
                    </option>
                @endforeach
            </select>
            <div class="hint">Applies to regional & area managers</div>
        </div>

        <div class="actions">
            <button class="btn btn-primary">Update User</button>
            <a href="{{ route('organisation.users.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
const roleSelect = document.getElementById('roleSelect');
const singleClinic = document.getElementById('singleClinic');
const multiClinic = document.getElementById('multiClinic');

function toggleClinicFields() {
    singleClinic.classList.add('hidden');
    multiClinic.classList.add('hidden');

    if (['clinic_manager','receptionist','sales'].includes(roleSelect.value)) {
        singleClinic.classList.remove('hidden');
    }

    if (['regional_manager','area_manager'].includes(roleSelect.value)) {
        multiClinic.classList.remove('hidden');
    }
}

roleSelect.addEventListener('change', toggleClinicFields);
toggleClinicFields();
</script>

@endsection
