@extends('layouts.vet')

@section('content')

<div class="v-form-card">
    <div class="v-card">
        <h2 style="text-align:center;font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 20px;">
            Create Appointment
        </h2>

        {{-- STEP 1: SEARCH --}}
        <form method="POST" action="{{ route('vet.appointments.search') }}">
            @csrf
            <div class="v-form-group">
                <label>Pet Parent Mobile Number</label>
                <input type="text" name="mobile" value="{{ old('mobile', $mobile ?? '') }}" required class="v-input">
            </div>
            <button type="submit" class="v-btn v-btn--primary">Search</button>
        </form>

        <hr class="v-divider">

        {{-- STEP 2A: PET PARENT FOUND --}}
        @isset($petParent)
            <h3 class="v-section-title">Pet Parent Found</h3>
            <p style="margin-bottom:6px;"><strong style="color:var(--text-dark);">Name:</strong> {{ $petParent->name }}</p>
            <p style="margin-bottom:16px;"><strong style="color:var(--text-dark);">Mobile:</strong> {{ $petParent->phone }}</p>

            <form method="POST" action="{{ route('vet.appointments.requestAccess') }}">
                @csrf
                <input type="hidden" name="mobile" value="{{ $petParent->phone }}">
                <button type="submit" class="v-btn v-btn--success">Request Access</button>
            </form>
        @endisset

        {{-- STEP 2B: PET PARENT NOT FOUND --}}
        @isset($notFound)
            <p style="color:var(--danger);font-weight:500;margin-bottom:14px;">
                No pet parent found with this number.
            </p>
            <form method="GET" action="{{ route('vet.petparent.create') }}">
                <button type="submit" class="v-btn v-btn--primary">Create Pet Parent Profile</button>
            </form>
        @endisset
    </div>
</div>

@endsection
