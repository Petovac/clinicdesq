@extends('pdf.layout')

@section('content')

<div class="doc-badge">CASE SHEET</div>

{{-- Patient Info --}}
<div class="info-box">
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell"><span class="info-label">Patient:</span> {{ $pet->name }} ({{ ucfirst($pet->species ?? '') }})</div>
            <div class="info-cell"><span class="info-label">Date:</span> {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><span class="info-label">Breed:</span> {{ $pet->breed ?? '—' }}</div>
            <div class="info-cell"><span class="info-label">Weight:</span> {{ $appointment->weight ? $appointment->weight . ' kg' : '—' }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><span class="info-label">Parent:</span> {{ $parent->name ?? '—' }}</div>
            <div class="info-cell"><span class="info-label">Doctor:</span> Dr. {{ $vet->name ?? '—' }}</div>
        </div>
        @if($appointment->appointment_number)
        <div class="info-row">
            <div class="info-cell"><span class="info-label">Appt #:</span> {{ $appointment->appointment_number }}</div>
            <div class="info-cell"><span class="info-label">Age:</span> {{ $appointment->calculated_age_at_visit ?? '—' }}</div>
        </div>
        @endif
    </div>
</div>

@if($caseSheet)
    @if($caseSheet->presenting_complaint)
    <div class="section">
        <div class="section-title">Presenting Complaint</div>
        <div class="section-content">{!! nl2br(e($caseSheet->presenting_complaint)) !!}</div>
    </div>
    @endif

    @if($caseSheet->history)
    <div class="section">
        <div class="section-title">History</div>
        <div class="section-content">{!! nl2br(e($caseSheet->history)) !!}</div>
    </div>
    @endif

    @if($caseSheet->clinical_examination)
    <div class="section">
        <div class="section-title">Clinical Examination</div>
        <div class="section-content">{!! nl2br(e($caseSheet->clinical_examination)) !!}</div>
    </div>
    @endif

    @if($caseSheet->differentials)
    <div class="section">
        <div class="section-title">Differential Diagnosis</div>
        <div class="section-content">{!! nl2br(e($caseSheet->differentials)) !!}</div>
    </div>
    @endif

    @if($caseSheet->diagnosis)
    <div class="section">
        <div class="section-title">Diagnosis</div>
        <div class="section-content">{!! nl2br(e($caseSheet->diagnosis)) !!}</div>
    </div>
    @endif
@endif

{{-- Treatments --}}
@if($treatments->count())
<div class="section">
    <div class="section-title">Treatment Given</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Treatment</th>
                <th>Type</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach($treatments as $t)
            <tr>
                <td style="font-weight:600;">{{ $t->name }}</td>
                <td>{{ ucfirst($t->type ?? '') }}</td>
                <td>
                    @if($t->dose) {{ $t->dose }} @endif
                    @if($t->route) ({{ $t->route }}) @endif
                    @if($t->notes) — {{ $t->notes }} @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@elseif($caseSheet && $caseSheet->treatment_given)
<div class="section">
    <div class="section-title">Treatment Given</div>
    <div class="section-content">{!! nl2br(e($caseSheet->treatment_given)) !!}</div>
</div>
@endif

@if($caseSheet && $caseSheet->procedures_done)
<div class="section">
    <div class="section-title">Procedures Done</div>
    <div class="section-content">{!! nl2br(e($caseSheet->procedures_done)) !!}</div>
</div>
@endif

@if($caseSheet && $caseSheet->further_plan)
<div class="section">
    <div class="section-title">Further Plan</div>
    <div class="section-content">{!! nl2br(e($caseSheet->further_plan)) !!}</div>
</div>
@endif

@if($caseSheet && $caseSheet->advice)
<div class="section">
    <div class="section-title">Advice</div>
    <div class="section-content">{!! nl2br(e($caseSheet->advice)) !!}</div>
</div>
@endif

{{-- Footer with signature --}}
<div class="footer">
    <div class="footer-left">
        {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A') }}
    </div>
    <div class="footer-right">
        <div class="signature-box">
            <div class="signature-name">Dr. {{ $vet->name ?? '' }}</div>
            @if($vet->degree ?? null)
                <div class="signature-detail">{{ $vet->degree }}</div>
            @endif
            @if($vet->registration_number ?? null)
                <div class="signature-detail">Reg. No: {{ $vet->registration_number }}</div>
            @endif
        </div>
    </div>
</div>

@endsection
