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
        margin-bottom: 24px;
        color: #111827;
    }

    .muted {
        color: #6b7280;
        font-size: 14px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
    }

    thead {
        background: #f9fafb;
    }

    th {
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    td {
        padding: 14px 16px;
        font-size: 14px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    tr:hover {
        background: #f9fafb;
    }

    .link {
        color: #2563eb;
        font-weight: 500;
        text-decoration: none;
    }

    .link:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        table {
            font-size: 13px;
        }

        th, td {
            padding: 10px;
        }
    }
</style>

<div class="page-wrap">

    <h2>Past Appointments</h2>

    @if($appointments->isEmpty())
        <p class="muted">
            No completed appointments yet.
        </p>
    @else
        <table>
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
                        <td>
                            {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}
                        </td>

                        <td>
                            {{ $appointment->clinic?->name ?? '-' }}
                        </td>

                        <td>
                            {{ $appointment->pet?->name ?? '-' }}
                        </td>

                        <td>
                            <a href="{{ route('vet.appointments.history.show', $appointment->id) }}"
                               class="link">
                                View Case
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>
@endsection