@extends('layouts.vet')

@section('content')

<style>
    /* ===== Main Card ===== */
    .card {
        max-width: 1000px;
        margin: 30px auto;
        padding: 28px 32px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.08);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1f2937;
        line-height: 1.6;
    }

    /* ===== Headings ===== */
    h2 {
        text-align: center;
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 14px;
        color: #111827;
    }

    h3 {
        font-size: 18px;
        font-weight: 600;
        margin-top: 22px;
        margin-bottom: 12px;
        color: #2563eb;
    }

    /* ===== Text ===== */
    p {
        font-size: 14px;
        margin-bottom: 6px;
        color: #374151;
    }

    p strong {
        color: #111827;
        font-weight: 600;
    }

    em {
        color: #6b7280;
        font-style: italic;
    }

    hr {
        margin: 20px 0;
        border: none;
        border-top: 1px solid #e5e7eb;
    }

    /* ===== Appointment Block ===== */
    .card > div > div {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 18px 20px;
        margin-bottom: 18px;
    }

    /* ===== Lists ===== */
    ul {
        padding-left: 18px;
        margin-top: 8px;
        margin-bottom: 10px;
    }

    ul li {
        font-size: 14px;
        margin-bottom: 6px;
        color: #374151;
    }

    ul li strong {
        color: #111827;
    }

    /* ===== Links / Actions ===== */
    a {
        display: inline-block;
        margin-top: 8px;
        padding: 8px 14px;
        font-size: 14px;
        font-weight: 500;
        color: #ffffff;
        background-color: #2563eb;
        border-radius: 6px;
        text-decoration: none;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }

    a:hover {
        background-color: #1e40af;
    }

    a:active {
        transform: scale(0.97);
    }

    /* ===== Status Highlight (optional future use) ===== */
    .status {
        display: inline-block;
        padding: 2px 8px;
        font-size: 12px;
        border-radius: 999px;
        font-weight: 500;
        background: #e5e7eb;
        color: #374151;
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .card {
            padding: 22px;
            margin: 16px;
        }

        h2 {
            font-size: 20px;
        }

        a {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="card">

    <h2>{{ $pet->name }} – Pet Profile</h2>

    <p><strong>Species:</strong> {{ ucfirst($pet->species) }}</p>
    <p><strong>Breed:</strong> {{ $pet->breed ?? '-' }}</p>
    <p><strong>Age:</strong> {{ $pet->age ?? '-' }}</p>

    <hr>

    <h3>Medical History</h3>

    @if($pet->appointments->count() === 0)
        <p>No appointments yet.</p>
    @else
        @foreach($pet->appointments as $appointment)
            <div style="border:1px solid #ccc; padding:15px; margin-bottom:15px;">

                <p>
                    <strong>Appointment Date:</strong>
                    {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A') }}
                </p>

                <p>
                    <strong>Status:</strong>
                    {{ ucfirst($appointment->status ?? 'scheduled') }}
                </p>

                <hr>

                {{-- Prescription --}}
                <p>
                    <strong>Prescription:</strong><br>

                    @if($appointment->prescription)
                        <ul>
                            @foreach($appointment->prescription->items as $item)
                                <li>
                                    {{ $item->medicine }}
                                    — {{ $item->dosage }},
                                    {{ $item->frequency }},
                                    {{ $item->duration }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <em>No prescription</em>
                    @endif
                </p>
                <hr>

                {{-- Case Sheet --}}
                @if($appointment->caseSheet)
                    @php
                        $isOwnClinic = $appointment->clinic_id === (session('active_clinic_id') ?? 1);
                    @endphp

                    <p><strong>Case Sheet:</strong></p>

                    @if($isOwnClinic)
                    <p>
                        <strong>Clinic:</strong>
                        {{ optional($appointment->clinic)->name ?? '-' }} <br>

                        <strong>Doctor:</strong>
                        {{ optional($appointment->vet)->name ?? '-' }}
                    </p>
                    @endif


                    <ul>
                        @if($appointment->caseSheet->presenting_complaint)
                            <li><strong>Presenting Complaint:</strong> {{ $appointment->caseSheet->presenting_complaint }}</li>
                        @endif

                        @if($appointment->caseSheet->history)
                            <li><strong>History:</strong> {{ $appointment->caseSheet->history }}</li>
                        @endif

                        @if($appointment->caseSheet->clinical_examination)
                            <li><strong>Clinical Examination:</strong> {{ $appointment->caseSheet->clinical_examination }}</li>
                        @endif

                        @if($appointment->caseSheet->differentials)
                            <li><strong>Differentials:</strong> {{ $appointment->caseSheet->differentials }}</li>
                        @endif

                        @if($appointment->caseSheet->diagnosis)
                            <li><strong>Diagnosis:</strong> {{ $appointment->caseSheet->diagnosis }}</li>
                        @endif

                        @if($appointment->caseSheet->treatment_given)
                            <li><strong>Treatment Given:</strong> {{ $appointment->caseSheet->treatment_given }}</li>
                        @endif

                        @if($appointment->caseSheet->procedures_done)
                            <li><strong>Procedures Done:</strong> {{ $appointment->caseSheet->procedures_done }}</li>
                        @endif

                        @if($appointment->caseSheet->further_plan)
                            <li><strong>Further Plan:</strong> {{ $appointment->caseSheet->further_plan }}</li>
                        @endif

                        @if($appointment->caseSheet->advice)
                            <li><strong>Advice:</strong> {{ $appointment->caseSheet->advice }}</li>
                        @endif
                    </ul>
                @endif


                {{-- Lab Reports --}}
                <p>
                    <strong>Lab Reports:</strong><br>
                    <em>Not added yet</em>
                </p>

                <hr>

                {{-- Vaccinations --}}
                <p>
                    <strong>Vaccinations:</strong><br>
                    <em>Not added yet</em>
                </p>

                <hr>

                @if($appointment->status === 'scheduled')
                    <a href="{{ route('vet.appointments.case', $appointment->id) }}"
                    style="color:white;font-size:13px;">
                        Open Case →
                    </a>
                @endif

            </div>
        @endforeach
    @endif

</div>
@endsection
