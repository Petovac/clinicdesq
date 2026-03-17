@extends('layouts.parent')

@section('title', 'Appointment Detail')

@section('styles')
<style>
    .breadcrumb { font-size: 13px; color: #64748b; margin-bottom: 20px; }
    .breadcrumb a { color: #2563eb; }
    .appt-header { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
    .appt-header h2 { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
    .meta-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 6px; font-size: 13px; color: #64748b; }
    .section { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
    .section-title { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #e2e8f0; }
    .field-row { margin-bottom: 8px; }
    .field-label { font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }
    .field-value { font-size: 14px; color: #334155; margin-top: 2px; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th { text-align: left; padding: 8px 10px; background: #f8fafc; color: #64748b; font-weight: 600; font-size: 12px; border-bottom: 1px solid #e2e8f0; }
    td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-green { background: #d1fae5; color: #065f46; }
    .badge-blue { background: #dbeafe; color: #1e40af; }
    .total-row { font-weight: 700; background: #f8fafc; }
</style>
@endsection

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('parent.dashboard') }}">My Pets</a> &rsaquo;
        <a href="{{ route('parent.pets.show', $appointment->pet) }}">{{ $appointment->pet->name }}</a> &rsaquo;
        Appointment
    </div>

    <div class="appt-header">
        <h2>
            Appointment — {{ $appointment->scheduled_at?->format('d M Y, h:i A') }}
            <span class="badge {{ $appointment->status === 'completed' ? 'badge-green' : 'badge-blue' }}" style="margin-left:8px;">
                {{ ucfirst($appointment->status) }}
            </span>
        </h2>
        <div class="meta-grid">
            <div><strong>Clinic:</strong> {{ $appointment->clinic->name ?? '—' }}</div>
            <div><strong>Doctor:</strong> {{ $appointment->vet->name ?? '—' }}</div>
            <div><strong>Weight:</strong> {{ $appointment->weight ? $appointment->weight.' kg' : '—' }}</div>
            <div><strong>Pet:</strong> {{ $appointment->pet->name }} ({{ ucfirst($appointment->pet->species) }})</div>
        </div>
    </div>

    {{-- Case Sheet --}}
    @if($appointment->caseSheet)
        <div class="section">
            <div class="section-title">Case Sheet</div>
            @foreach([
                'Presenting Complaint' => $appointment->caseSheet->presenting_complaint,
                'History' => $appointment->caseSheet->history,
                'Clinical Examination' => $appointment->caseSheet->clinical_examination,
                'Differentials' => $appointment->caseSheet->differentials,
                'Diagnosis' => $appointment->caseSheet->diagnosis,
                'Treatment Given' => $appointment->caseSheet->treatment_given,
                'Procedures Done' => $appointment->caseSheet->procedures_done,
                'Further Plan' => $appointment->caseSheet->further_plan,
                'Advice' => $appointment->caseSheet->advice,
            ] as $label => $val)
                @if($val)
                    <div class="field-row">
                        <div class="field-label">{{ $label }}</div>
                        <div class="field-value">{{ $val }}</div>
                    </div>
                @endif
            @endforeach

            @php
                $vitals = collect([
                    'Temp' => $appointment->caseSheet->temperature ? $appointment->caseSheet->temperature . '°F' : null,
                    'HR' => $appointment->caseSheet->heart_rate ? $appointment->caseSheet->heart_rate . ' bpm' : null,
                    'RR' => $appointment->caseSheet->respiratory_rate ? $appointment->caseSheet->respiratory_rate . ' bpm' : null,
                    'CRT' => $appointment->caseSheet->capillary_refill_time,
                    'MM' => $appointment->caseSheet->mucous_membrane,
                    'Hydration' => $appointment->caseSheet->hydration_status,
                    'PLN' => $appointment->caseSheet->lymph_nodes,
                    'BCS' => $appointment->caseSheet->body_condition_score,
                    'Pain' => $appointment->caseSheet->pain_score,
                ])->filter();
            @endphp
            @if($vitals->isNotEmpty())
                <div class="field-row">
                    <div class="field-label">Vitals</div>
                    <div class="field-value" style="font-size:13px;">{{ $vitals->map(fn($v, $k) => "$k: $v")->implode(' | ') }}</div>
                </div>
            @endif

            @if($appointment->caseSheet->prognosis)
                <div class="field-row">
                    <div class="field-label">Prognosis</div>
                    <div class="field-value"><span class="badge badge-blue">{{ ucfirst($appointment->caseSheet->prognosis) }}</span></div>
                </div>
            @endif
            @if($appointment->caseSheet->followup_date)
                <div class="field-row">
                    <div class="field-label">Follow-up</div>
                    <div class="field-value">{{ $appointment->caseSheet->followup_date->format('d M Y') }}{{ $appointment->caseSheet->followup_reason ? ' — '.$appointment->caseSheet->followup_reason : '' }}</div>
                </div>
            @endif
        </div>
    @endif

    {{-- Prescription --}}
    @if($appointment->prescription)
        <div class="section">
            <div class="section-title">Prescription</div>
            @if($appointment->prescription->notes)
                <div style="font-size:13px;color:#64748b;font-style:italic;margin-bottom:10px;">{{ $appointment->prescription->notes }}</div>
            @endif
            @if($appointment->prescription->items->isNotEmpty())
                <table>
                    <thead><tr><th>#</th><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Instructions</th></tr></thead>
                    <tbody>
                        @foreach($appointment->prescription->items as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td style="font-weight:600;">{{ $item->medicine }}</td>
                                <td>{{ $item->dosage ?? '—' }}</td>
                                <td>{{ $item->frequency ?? '—' }}</td>
                                <td>{{ $item->duration ?? '—' }}</td>
                                <td style="color:#64748b;">{{ $item->instructions ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif

    {{-- Treatments --}}
    @if($appointment->treatments->isNotEmpty())
        <div class="section">
            <div class="section-title">Treatments Administered</div>
            <table>
                <thead><tr><th>#</th><th>Treatment</th><th>Route</th><th>Dose</th></tr></thead>
                <tbody>
                    @foreach($appointment->treatments as $i => $t)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $t->drugGeneric->name ?? $t->treatment_type ?? '—' }}</td>
                            <td>{{ $t->route ?? '—' }}</td>
                            <td>{{ $t->dose_volume_ml ? $t->dose_volume_ml.' ml' : ($t->dose_mg ? $t->dose_mg.' mg' : '—') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Bill --}}
    @if($appointment->bill)
        <div class="section">
            <div class="section-title" style="display:flex;justify-content:space-between;align-items:center;">
                <span>Bill</span>
                <span class="badge {{ $appointment->bill->status === 'confirmed' ? 'badge-green' : 'badge-blue' }}">{{ ucfirst($appointment->bill->status) }}</span>
            </div>
            @if($appointment->bill->items->isNotEmpty())
                <table>
                    <thead><tr><th>Item</th><th style="text-align:right;">Qty</th><th style="text-align:right;">Price</th><th style="text-align:right;">Total</th></tr></thead>
                    <tbody>
                        @foreach($appointment->bill->items->where('status', 'approved') as $item)
                            <tr>
                                <td>{{ $item->description ?? '—' }}</td>
                                <td style="text-align:right;">{{ $item->quantity }}</td>
                                <td style="text-align:right;">&#8377;{{ number_format($item->price, 2) }}</td>
                                <td style="text-align:right;">&#8377;{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="3" style="text-align:right;">Total</td>
                            <td style="text-align:right;">&#8377;{{ number_format($appointment->bill->total_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    @endif
@endsection
