@extends('layouts.vet')

@section('content')
<div class="card">
    <h2>Create Appointment</h2>

    <form method="POST" action="{{ route('vet.appointments.requestAccess') }}">
        @csrf
        <label>Pet Parent Mobile Number</label>
        <input name="mobile" required>
        <button type="submit">Request Access</button>
    </form>
</div>
@endsection
