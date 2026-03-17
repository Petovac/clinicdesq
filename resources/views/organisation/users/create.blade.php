@extends('organisation.layout')

@section('content')

<style>
.card {
    background: #fff;
    border-radius: 10px;
    padding: 24px;
    max-width: 700px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}

.form-group {
    margin-bottom: 16px;
}

label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 6px;
}

input, select {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
}

.hidden {
    display: none;
}

.btn {
    padding: 10px 18px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #4f46e5;
    color: #fff;
}
</style>



<h2>Create User</h2>

<div class="card">

    @if ($errors->any())
    <div style="background:#fee2e2;padding:10px;margin-bottom:10px;border-radius:6px">
    <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
    </ul>
    </div>
    @endif
    <form method="POST" action="{{ route('organisation.users.store') }}">
        @csrf

        <div class="form-group">
            <label>Name</label>
            <input name="name" required>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input name="phone" required>
        </div>

        <div class="form-group">
            <label>Email (optional)</label>
            <input name="email">
        </div>

        <div class="form-group">
            <label>Role</label>
            <select name="role" id="roleSelect" required>
                <option value="">Select role</option>
                @foreach($roles as $roleKey => $level)
                    <option value="{{ $roleKey }}">
                        {{ ucfirst(str_replace('_', ' ', $roleKey)) }}
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
                    <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Multi clinic --}}
        <div class="form-group hidden" id="multiClinic">
            <label>Clinics</label>
            <select name="clinic_ids[]" multiple>
                @foreach($clinics as $clinic)
                    <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary">Create User</button>
    </form>
</div>

<script>
const roleSelect = document.getElementById('roleSelect');
const singleClinic = document.getElementById('singleClinic');
const multiClinic = document.getElementById('multiClinic');

function updateClinicFields() {
    const val = roleSelect.value;

    singleClinic.classList.add('hidden');
    multiClinic.classList.add('hidden');

    if (['clinic_manager','receptionist','sales'].includes(val)) {
        singleClinic.classList.remove('hidden');
    }

    if (['regional_manager','area_manager'].includes(val)) {
        multiClinic.classList.remove('hidden');
    }
}

roleSelect.addEventListener('change', updateClinicFields);
</script>

@endsection
