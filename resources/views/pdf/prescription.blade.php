@extends('pdf.layout')

@section('extra-styles')
.rx-symbol { font-size: 22px; font-weight: bold; color: #2563eb; margin-right: 6px; }
@endsection

@section('content')

<div class="doc-badge">PRESCRIPTION</div>

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
            <div class="info-cell"><span class="info-label">Parent:</span> {{ $parent->name ?? '—' }} ({{ $parent->phone ?? '' }})</div>
            <div class="info-cell"><span class="info-label">Doctor:</span> Dr. {{ $vet->name ?? '—' }}</div>
        </div>
    </div>
</div>

{{-- Prescription Items --}}
<div style="margin-top:4px;">
    <span class="rx-symbol">&#8478;</span>
</div>

@if($items->count())
<table class="data-table" style="margin-top:8px;">
    <thead>
        <tr>
            <th style="width:5%;">#</th>
            <th style="width:30%;">Medicine</th>
            <th style="width:15%;">Dosage</th>
            <th style="width:15%;">Frequency</th>
            <th style="width:15%;">Duration</th>
            <th style="width:20%;">Instructions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td style="font-weight:600;">{{ $item->medicine }}</td>
            <td>{{ $item->dosage ?? '—' }}</td>
            <td>{{ $item->frequency ?? '—' }}</td>
            <td>{{ $item->duration ?? '—' }}</td>
            <td style="font-size:10px;color:#6b7280;">{{ $item->instructions ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p style="color:#6b7280;font-style:italic;margin-top:10px;">No medicines prescribed.</p>
@endif

@if($prescription->notes)
<div class="section" style="margin-top:14px;">
    <div class="section-title">Notes</div>
    <div class="section-content">{!! nl2br(e($prescription->notes)) !!}</div>
</div>
@endif

{{-- Footer --}}
<div class="footer" style="margin-top:30px;">
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
