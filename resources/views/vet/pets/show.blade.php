@extends('layouts.vet')

@section('content')

<div style="max-width:1000px;margin:0 auto;">
    <div class="v-card">
        <h2 style="text-align:center;font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 14px;">
            {{ $pet->name }} &ndash; Pet Profile
        </h2>

        <div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:16px;">
            <p style="margin:0;"><strong style="color:var(--text-dark);">Species:</strong> {{ ucfirst($pet->species) }}</p>
            <p style="margin:0;"><strong style="color:var(--text-dark);">Breed:</strong> {{ $pet->breed ?? '-' }}</p>
            <p style="margin:0;"><strong style="color:var(--text-dark);">Age:</strong> {{ $pet->age ?? '-' }}</p>
        </div>

        <hr class="v-divider">

        <h3 class="v-section-title">Medical History</h3>

        @if($pet->appointments->count() === 0)
            <p style="color:var(--text-muted);">No appointments yet.</p>
        @else
            @foreach($pet->appointments as $appointment)
                <div class="v-card v-card--compact" style="background:var(--bg-soft);">

                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <div>
                            <strong style="color:var(--text-dark);">
                                {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A') }}
                            </strong>
                            <span class="v-badge v-badge--gray" style="margin-left:8px;">
                                {{ ucfirst($appointment->status ?? 'scheduled') }}
                            </span>
                        </div>
                        @if($appointment->status === 'scheduled')
                            <a href="{{ route('vet.appointments.case', $appointment->id) }}" class="v-btn v-btn--primary v-btn--sm">
                                Open Case
                            </a>
                        @endif
                    </div>

                    {{-- Prescription --}}
                    @if($appointment->prescription && $appointment->prescription->items->count())
                        <div style="margin-bottom:10px;">
                            <strong style="font-size:13px;color:var(--text-dark);">Prescription:</strong>
                            <ul style="padding-left:18px;margin:6px 0 0;font-size:14px;">
                                @foreach($appointment->prescription->items as $item)
                                    <li style="margin-bottom:4px;">
                                        {{ $item->medicine }}
                                        &mdash; {{ $item->dosage }},
                                        {{ $item->frequency }},
                                        {{ $item->duration }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Case Sheet --}}
                    @if($appointment->caseSheet)
                        @php
                            $isOwnClinic = $appointment->clinic_id === (session('active_clinic_id') ?? 1);
                        @endphp

                        <div style="margin-bottom:10px;">
                            <strong style="font-size:13px;color:var(--text-dark);">Case Sheet:</strong>

                            @if($isOwnClinic)
                                <p style="font-size:13px;margin:4px 0;">
                                    <strong>Clinic:</strong> {{ optional($appointment->clinic)->name ?? '-' }}
                                    &middot; <strong>Doctor:</strong> {{ optional($appointment->vet)->name ?? '-' }}
                                </p>
                            @endif

                            <ul style="padding-left:18px;margin:6px 0 0;font-size:14px;">
                                @foreach(['presenting_complaint' => 'Presenting Complaint', 'history' => 'History', 'clinical_examination' => 'Clinical Examination', 'differentials' => 'Differentials', 'diagnosis' => 'Diagnosis', 'treatment_given' => 'Treatment Given', 'procedures_done' => 'Procedures Done', 'further_plan' => 'Further Plan', 'advice' => 'Advice'] as $field => $label)
                                    @if($appointment->caseSheet->$field)
                                        <li style="margin-bottom:4px;"><strong>{{ $label }}:</strong> {{ $appointment->caseSheet->$field }}</li>
                                    @endif
                                @endforeach
                            </ul>

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
                                <p style="font-size:13px;color:#374151;background:#f9fafb;padding:8px 12px;border-radius:6px;border:1px solid #e5e7eb;margin-top:8px;">
                                    <strong>Vitals:</strong> {{ $vitals->map(fn($v, $k) => "$k: $v")->implode(' | ') }}
                                </p>
                            @endif
                        </div>
                    @endif

                    {{-- Treatments --}}
                    @if($appointment->treatments && $appointment->treatments->count())
                        <div style="margin-bottom:10px;">
                            <strong style="font-size:13px;color:var(--text-dark);">Treatments:</strong>
                            <ul style="padding-left:18px;margin:6px 0 0;font-size:14px;">
                                @foreach($appointment->treatments as $t)
                                    <li style="margin-bottom:4px;">
                                        @if($t->drug_generic_id)
                                            {{ optional($t->drugGeneric)->name ?? 'Drug' }}
                                            @if($t->dose_mg) &mdash; {{ $t->dose_mg }} mg @endif
                                            @if($t->dose_volume_ml) ({{ $t->dose_volume_ml }} ml) @endif
                                            @if($t->route) &middot; {{ $t->route }} @endif
                                        @else
                                            {{ optional($t->priceItem)->name ?? 'Procedure' }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>

@endsection
