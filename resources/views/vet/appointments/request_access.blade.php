@extends('layouts.vet')

@section('content')

<div class="v-form-card v-form-card--narrow">
    <div class="v-card">
        <h2 style="text-align:center;font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 20px;">
            Create Appointment
        </h2>

        <form method="POST" action="{{ route('vet.appointments.requestAccess') }}">
            @csrf

            <div class="v-form-group">
                <label>Pet Parent Mobile Number</label>
                <input name="mobile" required class="v-input">
            </div>

            <button type="submit" class="v-btn v-btn--primary v-btn--block">Request Access</button>
        </form>
    </div>
</div>

@endsection
