@extends('layouts.vet')

@section('content')

<style>
    .page-wrap {
        max-width: 1100px;
        margin: auto;
        padding: 24px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1f2937;
    }

    h2 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .muted {
        color: #6b7280;
        font-size: 14px;
    }

    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .primary-btn {
        padding: 10px 16px;
        background: #2563eb;
        color: #ffffff;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .primary-btn:hover {
        background: #1e40af;
    }

    .table-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    td {
        padding: 14px 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
        vertical-align: middle;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }

    .status-scheduled {
        background: #e0f2fe;
        color: #0369a1;
    }

    .status-completed {
        background: #dcfce7;
        color: #166534;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .link {
        color: #2563eb;
        font-weight: 500;
        text-decoration: none;
    }

    .link:hover {
        long-decoration: underline;
    }

    @media (max-width: 768px) {
        table {
            font-size: 13px;
        }
    }
</style>

<div class="page-wrap">


<div style="
    background:#eef2ff;
    border:1px solid #c7d2fe;
    padding:12px 16px;
    border-radius:8px;
    margin-bottom:20px;
    font-size:14px;
">
    <strong>Active Clinic:</strong> {{ $clinic->name }}
</div>

    {{-- HEADER --}}
    <div style="margin-bottom:24px;">
        <h2>{{ $clinic->name }}</h2>
        <p class="muted">
            {{ $clinic->organisation?->name }}
        </p>
    </div>

    {{-- ACTION BAR --}}
    <div class="action-bar">
        <div class="muted">
            You are working in this clinic ·
            <a href="{{ route('vet.dashboard') }}" class="link">
                Switch Clinic
            </a>
        </div>

        <a href="{{ route('vet.appointments.create') }}" class="primary-btn">
            + Create Appointment
        </a>
    </div>

    {{-- APPOINTMENTS --}}
    <div class="table-card">
        <h3>My Appointments</h3>

        @if($appointments->isEmpty())
            <p class="muted">
                No appointments scheduled for you in this clinic.
            </p>
        @else
            <table>
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
                            <td>
                            #{{ $appointment->appointment_number }}
                            </td>
                            
                            <td>
                                {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A') }}
                            </td>

                            <td>
                                {{ $appointment->pet?->petParent?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $appointment->pet?->name ?? '-' }}
                            </td>

                            <td>
                                <span class="status-badge
                                @if($appointment->status === 'scheduled') status-scheduled
                                @elseif($appointment->status === 'checked_in') status-waiting
                                @elseif($appointment->status === 'in_consultation') status-consultation
                                @elseif($appointment->status === 'completed') status-completed
                                @else status-cancelled
                                @endif
                                ">
                                    {{ ucfirst(str_replace('_',' ',$appointment->status)) }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('vet.appointments.case', $appointment->id) }}" class="link">
                                    Open Case
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection