@extends('layouts.vet')

@section('content')

<div class="v-page-header">
    <h1>Past Appointments</h1>
</div>

@if($appointments->isEmpty())
    <div class="v-empty v-empty--bordered">No completed appointments yet.</div>
@else
    <div class="v-card v-card--flush">
        <table class="v-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Clinic</th>
                    <th>Pet</th>
                    <th>Medical Records</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}</td>
                        <td>{{ $appointment->clinic?->name ?? '-' }}</td>
                        <td>{{ $appointment->pet?->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('vet.appointments.history.show', $appointment->id) }}" class="v-link">
                                View Case
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
