@extends('organisation.layout')

@section('content')

<h2>Assign Clinics – {{ $user->name }}</h2>

<form method="POST" action="{{ route('organisation.users.assignClinics', $user) }}">
    @csrf

    <label>Select Clinics</label>
    <select name="clinic_ids[]" multiple required>
        @foreach($clinics as $clinic)
            <option value="{{ $clinic->id }}"
                {{ $user->assignedClinics->contains($clinic->id) ? 'selected' : '' }}>
                {{ $clinic->name }}
            </option>
        @endforeach
    </select>

    <br><br>
    <button class="btn btn-primary">Save Assignments</button>
</form>

@endsection
