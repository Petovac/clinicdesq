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

        {{-- Vaccination Records --}}
        <hr class="v-divider" style="margin-top:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <h3 class="v-section-title">💉 Vaccination Records</h3>
        </div>

        @php $vaccinations = $pet->vaccinations; @endphp
        @if($vaccinations->count())
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#fffbeb;">
                        <th style="text-align:left;padding:6px 8px;font-size:11px;font-weight:600;color:#92400e;border-bottom:1px solid #fde68a;">Vaccine</th>
                        <th style="text-align:left;padding:6px 8px;font-size:11px;font-weight:600;color:#92400e;border-bottom:1px solid #fde68a;">Brand</th>
                        <th style="text-align:left;padding:6px 8px;font-size:11px;font-weight:600;color:#92400e;border-bottom:1px solid #fde68a;">Dose</th>
                        <th style="text-align:left;padding:6px 8px;font-size:11px;font-weight:600;color:#92400e;border-bottom:1px solid #fde68a;">Date</th>
                        <th style="text-align:left;padding:6px 8px;font-size:11px;font-weight:600;color:#92400e;border-bottom:1px solid #fde68a;">Next Due</th>
                        <th style="text-align:left;padding:6px 8px;font-size:11px;font-weight:600;color:#92400e;border-bottom:1px solid #fde68a;">Batch #</th>
                        <th style="text-align:left;padding:6px 8px;font-size:11px;font-weight:600;color:#92400e;border-bottom:1px solid #fde68a;">Vet</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vaccinations as $v)
                    <tr>
                        <td style="padding:6px 8px;border-bottom:1px solid #f3f4f6;font-weight:600;">{{ $v->vaccine_name }}</td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f3f4f6;">{{ $v->brand_name ?? '—' }}</td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f3f4f6;">
                            <span style="background:#dbeafe;color:#1d4ed8;padding:1px 6px;border-radius:8px;font-size:10px;font-weight:600;">{{ $v->dose_number }}</span>
                        </td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f3f4f6;">{{ $v->administered_date->format('d M Y') }}</td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f3f4f6;">
                            @if($v->next_due_date)
                                <span style="{{ $v->isOverdue() ? 'color:#ef4444;font-weight:600;' : ($v->isDueSoon() ? 'color:#f59e0b;font-weight:600;' : '') }}">
                                    {{ $v->next_due_date->format('d M Y') }}
                                    @if($v->isOverdue()) (OVERDUE) @elseif($v->isDueSoon()) (Due soon) @endif
                                </span>
                            @else — @endif
                        </td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f3f4f6;">{{ $v->batch_number ?? '—' }}</td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f3f4f6;">{{ $v->vet->name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p style="color:var(--text-muted);font-size:13px;">No vaccination records yet.</p>
        @endif

        {{-- Certificates --}}
        <hr class="v-divider" style="margin-top:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <h3 class="v-section-title">📜 Certificates</h3>
            <a href="{{ route('vet.certificates.create', $pet) }}" style="background:var(--primary);color:#fff;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">+ Issue Certificate</a>
        </div>

        @php $certificates = $pet->certificates; @endphp
        @if($certificates->count())
        <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px;">
            @foreach($certificates as $cert)
            <div style="display:flex;justify-content:space-between;align-items:center;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px 14px;">
                <div>
                    <span style="font-weight:600;color:#111;">{{ $cert->title }}</span>
                    <span style="background:#dbeafe;color:#1d4ed8;padding:1px 6px;border-radius:8px;font-size:10px;font-weight:600;margin-left:6px;">{{ ucfirst($cert->certificate_type) }}</span>
                    <span style="background:{{ $cert->status === 'issued' ? '#dcfce7' : '#fef3c7' }};color:{{ $cert->status === 'issued' ? '#166534' : '#92400e' }};padding:1px 6px;border-radius:8px;font-size:10px;font-weight:600;margin-left:4px;">{{ ucfirst($cert->status) }}</span>
                    <br>
                    <span style="font-size:11px;color:#6b7280;">{{ $cert->certificate_number }} · {{ $cert->issued_date->format('d M Y') }}{{ $cert->valid_until ? ' · Valid until: ' . $cert->valid_until->format('d M Y') : '' }}</span>
                </div>
                <div style="display:flex;gap:6px;">
                    @if($cert->status === 'issued')
                    <a href="{{ route('vet.certificates.download', $cert) }}" style="background:var(--primary);color:#fff;padding:5px 10px;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none;">Download</a>
                    @else
                    <a href="{{ route('vet.certificates.edit', $cert) }}" style="background:#f59e0b;color:#fff;padding:5px 10px;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none;">Edit</a>
                    @endif
                    <a href="{{ route('vet.certificates.preview', $cert) }}" target="_blank" style="background:#e5e7eb;color:#374151;padding:5px 10px;border-radius:5px;font-size:11px;font-weight:600;text-decoration:none;">Preview</a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p style="color:var(--text-muted);font-size:13px;">No certificates issued yet.
            <a href="{{ route('vet.certificates.create', $pet) }}" style="color:var(--primary);">Issue one now →</a>
        </p>
        @endif

    </div>
</div>

@endsection
