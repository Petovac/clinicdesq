@extends('organisation.layout')

@section('content')
<style>
.card {
    border: 1px solid #e5e7eb;
    padding: 16px;
    border-radius: 8px;
    background: #fff;
}
</style>

<h2>Vet Profile</h2>

<div class="card">
    <p><strong>Name:</strong> {{ $vet->name }}</p>
    <p><strong>Registration No:</strong> {{ $vet->registration_number ?? '-' }}</p>
    <p><strong>Specialization:</strong> {{ $vet->specialization ?? '-' }}</p>
    <p><strong>Experience:</strong> {{ $vet->experience ?? '-' }}</p>
</div>

<h3>Assign Clinics</h3>

<form method="POST" action="{{ route('organisation.vets.assignClinics', $vet) }}">
    @csrf

    @foreach($clinics as $clinic)
        <label>
            <input
                type="checkbox"
                name="clinic_ids[]"
                value="{{ $clinic->id }}"
                {{ in_array($clinic->id, $assignedClinicIds) ? 'checked' : '' }}
            >
            {{ $clinic->name }}
        </label><br>
    @endforeach

    <br>
    <button>Save Assignments</button>
</form>
@endsection