@extends('clinic.layout')

@section('content')

<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.card {
    background: #fff;
    padding: 18px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
}

.card h3 {
    margin: 0 0 6px;
    font-size: 14px;
    color: #6b7280;
}

.card .value {
    font-size: 26px;
    font-weight: 600;
    color: #111827;
}

.section {
    background: #fff;
    padding: 18px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
}

.section h4 {
    margin-bottom: 12px;
    font-size: 16px;
}
</style>

<h2>{{ $clinic->name }}</h2>

<div class="dashboard-grid">
    <div class="card">
        <h3>Today's Appointments</h3>
        <div class="value">{{ $todayAppointments }}</div>
    </div>

    <div class="card">
        <h3>Upcoming Appointments</h3>
        <div class="value">{{ $upcomingAppointments }}</div>
    </div>

    <div class="card">
        <h3>Waiting</h3>
        <div class="value" style="color:#f59e0b;">{{ $waitingCount }}</div>
    </div>

    <div class="card">
        <h3>In Consultation</h3>
        <div class="value" style="color:#0ea5e9;">{{ $consultationCount }}</div>
    </div>

    <div class="card">
        <h3>Ready for Billing</h3>
        <div class="value" style="color:#ef4444;">{{ $needsBillingCount }}</div>
    </div>

    <div class="card">
        <h3>Doctors Linked</h3>
        <div class="value">{{ $clinic->vets->count() }}</div>
    </div>
</div>

<div class="section" style="margin-bottom:20px;">
    <h4>Quick Links</h4>
    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        @if(auth()->user()->hasPermission('appointments.view'))
        <a href="{{ route('clinic.appointments.index') }}" class="btn btn-primary" style="padding:10px 20px; background:#3b82f6; color:#fff; text-decoration:none; border-radius:8px;">
            View Appointments
        </a>
        @endif

        @if(auth()->user()->hasPermission('appointments.create'))
        <a href="{{ route('clinic.appointments.create') }}" class="btn btn-success" style="padding:10px 20px; background:#22c55e; color:#fff; text-decoration:none; border-radius:8px;">
            + Create Appointment
        </a>
        @endif
    </div>
</div>

<div class="section">
    <h4>Doctors in this Clinic</h4>

    @if($clinic->vets->isEmpty())
        <p>No doctors linked to this clinic.</p>
    @else
        <ul>
            @foreach($clinic->vets as $vet)
                <li>{{ $vet->name }}</li>
            @endforeach
        </ul>
    @endif
</div>

@endsection
