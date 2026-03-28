@extends('layouts.vet')

@section('content')

<div class="v-page-header">
    <h1>My Clinics</h1>
    <p>Select a clinic to continue working</p>
</div>

{{-- Pending Onboarding Requests --}}
@if(isset($pendingClinicRequests) && $pendingClinicRequests->count())
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:var(--radius-md);padding:16px;margin-bottom:16px;">
    <h3 style="font-size:15px;font-weight:700;color:#92400e;margin:0 0 10px;">🔔 Clinic Onboarding Requests</h3>
    @foreach($pendingClinicRequests as $req)
    <div style="display:flex;justify-content:space-between;align-items:center;background:#fff;border:1px solid #fde68a;border-radius:var(--radius-sm);padding:12px;margin-bottom:8px;">
        <div>
            <div style="font-weight:700;font-size:14px;color:var(--text-dark);">{{ $req->clinic_name }}</div>
            <div style="font-size:12px;color:var(--text-muted);">{{ $req->org_name }} · {{ $req->city ?? '' }}</div>
        </div>
        <div style="display:flex;gap:6px;">
            <form method="POST" action="{{ route('vet.accept-clinic') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="clinic_id" value="{{ $req->clinic_id }}">
                <button type="submit" style="background:#16a34a;color:#fff;border:none;padding:6px 14px;border-radius:var(--radius-sm);font-size:12px;font-weight:600;cursor:pointer;">Accept</button>
            </form>
            <form method="POST" action="{{ route('vet.reject-clinic') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="clinic_id" value="{{ $req->clinic_id }}">
                <button type="submit" style="background:#ef4444;color:#fff;border:none;padding:6px 14px;border-radius:var(--radius-sm);font-size:12px;font-weight:600;cursor:pointer;">Decline</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

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
