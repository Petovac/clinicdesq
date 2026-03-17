@extends('documents._base')
@section('doc-title', 'Case Sheet')

@section('doc-styles')
.doc { max-width: 700px; margin: 0 auto; padding: 20px 0; }
.header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #2563eb; padding-bottom: 16px; margin-bottom: 16px; }
.header-logo img, img.header-logo { max-height: 56px; max-width: 140px; }
.header-right { text-align: right; }
.header-right h1 { font-size: 17px; font-weight: 700; color: #2563eb; }
.header-right p { font-size: 11px; color: #6b7280; }
.badge { display: inline-block; background: #2563eb; color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; letter-spacing: .5px; margin: 8px 0 12px; }
.info-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 6px; font-size: 13px; }
.section-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 10px; }
.section-box h4 { font-size: 11px; text-transform: uppercase; color: #2563eb; letter-spacing: .5px; margin-bottom: 4px; font-weight: 600; }
.section-box p { font-size: 13px; color: #334155; }
.footer { margin-top: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
.signature-line { border-top: 2px solid #2563eb; width: 180px; text-align: center; padding-top: 4px; font-size: 12px; color: #2563eb; }
@endsection

@section('doc-content')
<div class="doc">
    <div class="header">
        <div>
            @if($logoUrl) <img src="{{ $logoUrl }}" alt="{{ $org->name }}" class="header-logo"> @endif
            <div style="font-weight:700;font-size:14px;margin-top:3px;">{{ $org->name }}</div>
        </div>
        <div class="header-right">
            <h1>{{ $clinic->name }}</h1>
            <p>{{ $clinic->address }}, {{ $clinic->city }} {{ $clinic->pincode }}</p>
        </div>
    </div>

    <span class="badge">CASE SHEET</span>

    <div class="info-card">
        <div><strong>Patient:</strong> {{ $pet->name }} ({{ ucfirst($pet->species) }})</div>
        <div><strong>Date:</strong> {{ $date }}</div>
        <div><strong>Breed:</strong> {{ $pet->breed ?? '—' }}</div>
        <div><strong>Weight:</strong> {{ $appointment->weight ? $appointment->weight.' kg' : '—' }}</div>
        <div><strong>Parent:</strong> {{ $parent->name }}</div>
        <div><strong>Doctor:</strong> {{ $vet->name ?? '—' }}</div>
        @if($caseSheet->prognosis) <div><strong>Prognosis:</strong> {{ ucfirst($caseSheet->prognosis) }}</div> @endif
    </div>

    @foreach([
        'Presenting Complaint' => $caseSheet->presenting_complaint,
        'History' => $caseSheet->history,
        'Clinical Examination' => $caseSheet->clinical_examination,
    ] as $label => $value)
        @if($value)
            <div class="section-box"><h4>{{ $label }}</h4><p>{{ $value }}</p></div>
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
        <div class="section-box"><h4>Vitals</h4><p>{{ $vitals->map(fn($v, $k) => "$k: $v")->implode(' | ') }}</p></div>
    @endif

    @foreach([
        'Differentials' => $caseSheet->differentials,
        'Diagnosis' => $caseSheet->diagnosis,
    ] as $label => $value)
        @if($value)
            <div class="section-box"><h4>{{ $label }}</h4><p>{{ $value }}</p></div>
        @endif
    @endforeach

    @if(isset($drugTreatments) && $drugTreatments->count())
        <div class="section-box">
            <h4>Drug Treatments Administered</h4>
            @foreach($drugTreatments as $dt)
                <div style="display:flex;align-items:center;gap:8px;padding:5px 0;{{ !$loop->first ? 'border-top:1px solid #e2e8f0;' : '' }}">
                    <span style="background:#2563eb;color:#fff;font-size:10px;font-weight:700;width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">{{ $loop->iteration }}</span>
                    <span style="font-weight:600;font-size:13px;">{{ optional($dt->drugGeneric)->name ?? '—' }}</span>
                    @if($dt->dose_volume_ml)
                        <span style="font-size:12px;color:#64748b;">{{ $dt->dose_volume_ml }}ml</span>
                    @endif
                    @if($dt->route)
                        <span style="font-size:12px;color:#64748b;">{{ $dt->route }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if(isset($procedures) && $procedures->count())
        <div class="section-box">
            <h4>Procedures Performed</h4>
            @foreach($procedures as $proc)
                <div style="display:flex;align-items:center;gap:8px;padding:5px 0;{{ !$loop->first ? 'border-top:1px solid #e2e8f0;' : '' }}">
                    <span style="background:#16a34a;color:#fff;font-size:10px;font-weight:700;width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">{{ $loop->iteration }}</span>
                    <span style="font-weight:600;font-size:13px;">{{ optional($proc->priceItem)->name ?? '—' }}</span>
                </div>
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
            <div class="section-box"><h4>{{ $label }}</h4><p>{{ $value }}</p></div>
        @endif
    @endforeach

    @if($caseSheet->followup_date)
        <div class="section-box"><h4>Follow-up</h4><p>{{ $caseSheet->followup_date->format('d M Y') }} — {{ $caseSheet->followup_reason ?? '' }}</p></div>
    @endif

    <div class="footer">
        <div style="font-size:11px;color:#94a3b8;">{{ now()->format('d M Y, h:i A') }}</div>
        @include('documents.partials.vet-stamp')
    </div>
</div>
@endsection
