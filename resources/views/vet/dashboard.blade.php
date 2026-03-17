@extends('layouts.vet')

@section('content')

<div class="v-page-header">
    <h1>My Clinics</h1>
    <p>Select a clinic to continue working</p>
</div>

@if($activeClinic)
    <div class="v-banner v-banner--info">
        <span class="v-badge v-badge--blue">Active</span>
        <div>
            <strong style="color:var(--text-dark);">{{ $activeClinic->name }}</strong>
            <span style="color:var(--text-muted);">{{ $activeClinic->organisation?->name }}</span>
        </div>
    </div>
@endif

@if($clinics->isEmpty())
    <div class="v-empty v-empty--bordered" style="padding:48px 20px;">
        <h3 style="margin:0 0 8px;font-size:18px;color:var(--text-dark);">No Active Clinic Assignment</h3>
        <p style="margin:0;">You are currently not assigned to any clinic. Once a clinic onboards you, it will appear here.</p>
    </div>
@else
    <div class="v-grid v-grid--2">
        @foreach($clinics as $clinic)
            @php $isActive = session('active_clinic_id') == $clinic->id; @endphp

            <div class="v-card" style="{{ $isActive ? 'border-color:var(--primary);box-shadow:0 4px 16px rgba(37,99,235,0.12);' : '' }}position:relative;">

                @if($isActive)
                    <span class="v-badge v-badge--blue" style="position:absolute;top:16px;right:16px;">Active</span>
                @endif

                <h3 style="font-size:18px;font-weight:600;color:var(--text-dark);margin:0 0 4px;">
                    {{ $clinic->name }}
                </h3>

                <p style="font-size:13px;color:var(--text-muted);margin:0 0 18px;">
                    {{ $clinic->organisation?->name }}
                </p>

                <form method="POST" action="{{ route('vet.selectClinic', $clinic->id) }}">
                    @csrf
                    <button type="submit" class="v-btn v-btn--primary v-btn--sm">
                        {{ $isActive ? 'Enter Clinic' : 'Switch to Clinic' }}
                    </button>
                </form>
            </div>
        @endforeach
    </div>
@endif

@endsection
