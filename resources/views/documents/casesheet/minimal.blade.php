@extends('documents._base')
@section('doc-title', 'Case Sheet')

@section('doc-styles')
.doc { max-width: 660px; margin: 0 auto; padding: 20px 0; }
.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
.header-logo img, img.header-logo { max-height: 50px; max-width: 120px; }
.header-right { text-align: right; }
.header-right h1 { font-size: 16px; font-weight: 600; }
.header-right p { font-size: 11px; color: #888; }
.title { font-size: 13px; font-weight: 500; color: #888; text-transform: uppercase; letter-spacing: 2px; margin: 16px 0 12px; }
.thin-line { border: none; border-top: 1px solid #eee; margin: 10px 0; }
.info { font-size: 12px; color: #555; margin-bottom: 3px; }
.section { margin-bottom: 10px; }
.section h4 { font-size: 11px; color: #aaa; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
.section p { font-size: 13px; color: #333; }
.footer { margin-top: 40px; display: flex; justify-content: space-between; }
.signature-line { border-top: 1px solid #ccc; width: 160px; text-align: center; padding-top: 4px; font-size: 11px; color: #999; }
@endsection

@section('doc-content')
<div class="doc">
    <div class="header">
        <div>
            @if($logoUrl) <img src="{{ $logoUrl }}" alt="{{ $org->name }}" class="header-logo"> @endif
            <div style="font-weight:600;font-size:14px;margin-top:3px;">{{ $org->name }}</div>
        </div>
        <div class="header-right">
            <h1>{{ $clinic->name }}</h1>
            <p>{{ $clinic->address }}, {{ $clinic->city }} {{ $clinic->pincode }}</p>
        </div>
    </div>

    <hr class="thin-line">
    <div class="title">Case Sheet</div>

    <div class="info"><strong>{{ $pet->name }}</strong> &middot; {{ ucfirst($pet->species) }} &middot; {{ $pet->breed ?? '' }} &middot; {{ $appointment->weight ? $appointment->weight.' kg' : '' }}</div>
    <div class="info">{{ $parent->name }} &middot; {{ $vet->name ?? '' }} &middot; {{ $date }}</div>
    @if($caseSheet->prognosis) <div class="info"><strong>Prognosis:</strong> {{ ucfirst($caseSheet->prognosis) }}</div> @endif

    <hr class="thin-line">

    @foreach([
        'Presenting Complaint' => $caseSheet->presenting_complaint,
        'History' => $caseSheet->history,
        'Clinical Examination' => $caseSheet->clinical_examination,
    ] as $label => $value)
        @if($value)
            <div class="section"><h4>{{ $label }}</h4><p>{{ $value }}</p></div>
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
        <div class="section"><h4>Vitals</h4><p>{{ $vitals->map(fn($v, $k) => "$k: $v")->implode(' | ') }}</p></div>
    @endif

    @foreach([
        'Differentials' => $caseSheet->differentials,
        'Diagnosis' => $caseSheet->diagnosis,
    ] as $label => $value)
        @if($value)
            <div class="section"><h4>{{ $label }}</h4><p>{{ $value }}</p></div>
        @endif
    @endforeach

    @if(isset($drugTreatments) && $drugTreatments->count())
        <div class="section">
            <h4>Drug Treatments Administered</h4>
            <p>
                @foreach($drugTreatments as $dt)
                    {{ optional($dt->drugGeneric)->name ?? '—' }}@if($dt->dose_volume_ml) {{ $dt->dose_volume_ml }}ml @endif @if($dt->route)({{ $dt->route }})@endif{{ !$loop->last ? ' · ' : '' }}
                @endforeach
            </p>
        </div>
    @endif

    @if(isset($procedures) && $procedures->count())
        <div class="section">
            <h4>Procedures Performed</h4>
            <p>
                @foreach($procedures as $proc)
                    {{ optional($proc->priceItem)->name ?? '—' }}{{ !$loop->last ? ' · ' : '' }}
                @endforeach
            </p>
        </div>
    @endif

    @foreach([
        'Treatment Given' => $caseSheet->treatment_given,
        'Procedures Done' => $caseSheet->procedures_done,
        'Further Plan' => $caseSheet->further_plan,
        'Advice' => $caseSheet->advice,
    ] as $label => $value)
        @if($value)
            <div class="section"><h4>{{ $label }}</h4><p>{{ $value }}</p></div>
        @endif
    @endforeach

    @if($caseSheet->followup_date)
        <div class="section"><h4>Follow-up</h4><p>{{ $caseSheet->followup_date->format('d M Y') }} — {{ $caseSheet->followup_reason ?? '' }}</p></div>
    @endif

    <div class="footer">
        <div style="font-size:10px;color:#bbb;">{{ now()->format('d M Y') }}</div>
        @include('documents.partials.vet-stamp')
    </div>
</div>
@endsection
