@extends('documents._base')
@section('doc-title', 'Case Sheet')

@section('doc-styles')
.doc { max-width: 700px; margin: 0 auto; padding: 20px 0; }
.header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1a1a1a; padding-bottom: 14px; margin-bottom: 16px; }
.header-logo img { max-height: 60px; max-width: 140px; }
.header-right { text-align: right; }
.header-right h1 { font-size: 18px; font-weight: 700; }
.header-right p { font-size: 11px; color: #555; }
.doc-title { text-align: center; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin: 14px 0; border: 1px solid #ccc; padding: 6px; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 20px; font-size: 13px; margin-bottom: 12px; }
.divider { border: none; border-top: 1px solid #ccc; margin: 12px 0; }
.section-label { font-size: 12px; font-weight: 700; text-transform: uppercase; color: #555; margin: 14px 0 4px; letter-spacing: .5px; }
.section-content { font-size: 13px; margin-bottom: 6px; padding: 6px 0; border-bottom: 1px dotted #e0e0e0; }
.footer { margin-top: 30px; display: flex; justify-content: space-between; }
.signature-line { border-top: 1px solid #555; width: 180px; text-align: center; padding-top: 4px; font-size: 12px; }
@endsection

@section('doc-content')
<div class="doc">
    <div class="header">
        <div class="header-logo">
            @if($logoUrl) <img src="{{ $logoUrl }}" alt="{{ $org->name }}"> @endif
            <div style="font-weight:700;font-size:15px;margin-top:4px;">{{ $org->name }}</div>
        </div>
        <div class="header-right">
            <h1>{{ $clinic->name }}</h1>
            <p>{{ $clinic->address }}, {{ $clinic->city }} {{ $clinic->pincode }}</p>
            @if($clinic->phone) <p>Ph: {{ $clinic->phone }}</p> @endif
        </div>
    </div>

    <div class="doc-title">Case Sheet</div>

    <div class="info-grid">
        <div><strong>Patient:</strong> {{ $pet->name }}</div>
        <div><strong>Date:</strong> {{ $date }}</div>
        <div><strong>Species:</strong> {{ ucfirst($pet->species) }}</div>
        <div><strong>Breed:</strong> {{ $pet->breed ?? '—' }}</div>
        <div><strong>Age:</strong> {{ $appointment->calculated_age_at_visit ?? '—' }}</div>
        <div><strong>Weight:</strong> {{ $appointment->weight ? $appointment->weight.' kg' : '—' }}</div>
        <div><strong>Parent:</strong> {{ $parent->name }} ({{ $parent->phone ?? '' }})</div>
        <div><strong>Doctor:</strong> {{ $vet->name ?? '—' }}</div>
    </div>

    @if($caseSheet->prognosis)
        <div style="font-size:13px;"><strong>Prognosis:</strong> {{ ucfirst($caseSheet->prognosis) }}</div>
    @endif

    <hr class="divider">

    @foreach([
        'Presenting Complaint' => $caseSheet->presenting_complaint,
        'History' => $caseSheet->history,
        'Clinical Examination' => $caseSheet->clinical_examination,
    ] as $label => $value)
        @if($value)
            <div class="section-label">{{ $label }}</div>
            <div class="section-content">{{ $value }}</div>
        @endif
    @endforeach

    @php
        $vitals = collect([
            'Temp' => $caseSheet->temperature ? $caseSheet->temperature . '°F' : null,
            'HR' => $caseSheet->heart_rate ? $caseSheet->heart_rate . ' bpm' : null,
            'RR' => $caseSheet->respiratory_rate ? $caseSheet->respiratory_rate . ' bpm' : null,
            'CRT' => $caseSheet->capillary_refill_time,
            'MM' => $caseSheet->mucous_membrane,
            'Hydration' => $caseSheet->hydration_status,
            'PLN' => $caseSheet->lymph_nodes,
            'BCS' => $caseSheet->body_condition_score,
            'Pain' => $caseSheet->pain_score,
        ])->filter();
    @endphp
    @if($vitals->isNotEmpty())
        <div class="section-label">Vitals</div>
        <div class="section-content">{{ $vitals->map(fn($v, $k) => "$k: $v")->implode(' | ') }}</div>
    @endif

    @foreach([
        'Differentials' => $caseSheet->differentials,
        'Diagnosis' => $caseSheet->diagnosis,
    ] as $label => $value)
        @if($value)
            <div class="section-label">{{ $label }}</div>
            <div class="section-content">{{ $value }}</div>
        @endif
    @endforeach

    @if(isset($drugTreatments) && $drugTreatments->count())
        <div class="section-label">Drug Treatments Administered</div>
        <div class="section-content">
            @foreach($drugTreatments as $dt)
                {{ $loop->iteration }}. {{ optional($dt->drugGeneric)->name ?? '—' }}@if($dt->dose_volume_ml) — {{ $dt->dose_volume_ml }}ml @endif @if($dt->route)({{ $dt->route }})@endif<br>
            @endforeach
        </div>
    @endif

    @if(isset($procedures) && $procedures->count())
        <div class="section-label">Procedures Performed</div>
        <div class="section-content">
            @foreach($procedures as $proc)
                {{ $loop->iteration }}. {{ optional($proc->priceItem)->name ?? '—' }}<br>
            @endforeach
        </div>
    @endif

    @foreach([
        'Treatment Given' => $caseSheet->treatment_given,
        'Procedures Done' => $caseSheet->procedures_done,
        'Further Plan' => $caseSheet->further_plan,
        'Advice' => $caseSheet->advice,
    ] as $label => $value)
        @if($value)
            <div class="section-label">{{ $label }}</div>
            <div class="section-content">{{ $value }}</div>
        @endif
    @endforeach

    @if($caseSheet->followup_date)
        <div class="section-label">Follow-up</div>
        <div class="section-content">{{ $caseSheet->followup_date->format('d M Y') }} — {{ $caseSheet->followup_reason ?? '' }}</div>
    @endif

    <div class="footer">
        <div style="font-size:11px;color:#777;">{{ now()->format('d M Y, h:i A') }}</div>
        @include('documents.partials.vet-stamp')
    </div>
</div>
@endsection
