@extends('layouts.vet')

@section('content')

<div class="v-banner v-banner--info">
    <strong>Active Clinic:</strong> {{ $clinic->name }}
</div>

<div class="v-page-header v-page-header--row">
    <div>
        <h1>{{ $clinic->name }}</h1>
        <p>{{ $clinic->organisation?->name }} &middot;
            <a href="{{ route('vet.dashboard') }}" class="v-link" style="font-size:14px;">Switch Clinic</a>
        </p>
    </div>
    <a href="{{ route('vet.appointments.create') }}" class="v-btn v-btn--primary">+ New Appointment</a>
</div>

<div class="v-card v-card--flush">
    <div style="padding:20px 24px 10px;">
        <h3 class="v-section-title" style="border:none;padding:0;margin:0;">My Appointments</h3>
    </div>

    @if($appointments->isEmpty())
        <div class="v-empty">No appointments scheduled for you in this clinic.</div>
    @else
        <table class="v-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date & Time</th>
                    <th>Pet Parent</th>
                    <th>Pet</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    <tr>
                        <td>#{{ $appointment->appointment_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A') }}</td>
                        <td>{{ $appointment->pet?->petParent?->name ?? '-' }}</td>
                        <td>{{ $appointment->pet?->name ?? '-' }}</td>
                        <td>
                            <span class="v-badge
                                @if($appointment->status === 'scheduled') v-badge--blue
                                @elseif($appointment->status === 'completed') v-badge--green
                                @elseif($appointment->status === 'cancelled') v-badge--red
                                @elseif($appointment->status === 'in_consultation') v-badge--yellow
                                @else v-badge--gray
                                @endif">
                                {{ ucfirst(str_replace('_',' ',$appointment->status)) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('vet.appointments.case', $appointment->id) }}" class="v-link">
                                Open Case
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection
