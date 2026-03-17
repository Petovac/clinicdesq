@extends('layouts.vet')

@section('content')

<div class="v-form-card">
    <div class="v-card">
        <h2 style="text-align:center;font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 18px;">
            Create Appointment
        </h2>

        <p style="margin-bottom:6px;"><strong style="color:var(--text-dark);">Pet:</strong> {{ $pet->name }}</p>
        <p style="margin-bottom:18px;"><strong style="color:var(--text-dark);">Pet Parent:</strong> {{ $pet->petParent->name }}</p>

        <form method="POST" action="{{ route('vet.appointments.store') }}">
            @csrf
            <input type="hidden" name="pet_id" value="{{ $pet->id }}">
            <input type="hidden" name="pet_parent_id" value="{{ $pet->pet_parent_id }}">

            <div class="v-form-group">
                <label>Appointment Date & Time</label>
                <input type="datetime-local" name="scheduled_at" required class="v-input">
            </div>

            <div class="v-form-group">
                <label>Pet Weight (kg) <span style="color:var(--danger);">*</span></label>
                <input type="number" step="0.1" name="weight" required
                       value="{{ old('weight', $lastWeight) }}"
                       placeholder="Enter weight in kg"
                       class="v-input">
                @if($lastWeight)
                    <p class="v-form-hint">Last recorded weight: {{ $lastWeight }} kg</p>
                @endif
            </div>

            <button type="submit" class="v-btn v-btn--primary v-btn--block">Create Appointment</button>
        </form>
    </div>
</div>

@endsection
