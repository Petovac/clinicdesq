@extends('layouts.vet')

@section('page-class')v-page--split @endsection

@section('head')
<style>
    /* ===== Case Sheet Section Cards ===== */
    .cs-section {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 18px 20px;
        margin-bottom: 16px;
        position: relative;
    }
    .cs-section--blue { border-left: 3px solid #2563eb; }
    .cs-section--green { border-left: 3px solid #16a34a; }
    .cs-section--orange { border-left: 3px solid #f59e0b; }
    .cs-section--purple { border-left: 3px solid #7c3aed; }
    .cs-section--red { border-left: 3px solid #ef4444; }
    .cs-section--teal { border-left: 3px solid #0d9488; }

    .cs-section-title {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 0 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cs-section--blue .cs-section-title { color: #2563eb; }
    .cs-section--green .cs-section-title { color: #16a34a; }
    .cs-section--orange .cs-section-title { color: #f59e0b; }
    .cs-section--purple .cs-section-title { color: #7c3aed; }
    .cs-section--red .cs-section-title { color: #ef4444; }
    .cs-section--teal .cs-section-title { color: #0d9488; }

    .cs-section-icon {
        width: 22px; height: 22px;
        border-radius: 5px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px;
    }
    .cs-section--blue .cs-section-icon { background: #dbeafe; }
    .cs-section--green .cs-section-icon { background: #dcfce7; }
    .cs-section--orange .cs-section-icon { background: #fef3c7; }
    .cs-section--purple .cs-section-icon { background: #ede9fe; }
    .cs-section--red .cs-section-icon { background: #fee2e2; }
    .cs-section--teal .cs-section-icon { background: #ccfbf1; }

    /* Lab dropdown hover fix */
    .lab-option {
        padding: 8px 14px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.1s;
    }
    .lab-option:hover {
        background: #eff6ff !important;
    }
    .lab-option:last-child { border-bottom: none; }
    .lab-option .lab-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
        margin-right: 6px;
    }
    .lab-badge--in { background: #dbeafe; color: #1d4ed8; }
    .lab-badge--ext { background: #dcfce7; color: #166534; }
    .lab-option .lab-price { font-weight: 600; color: #111827; }
    .lab-group-header {
        padding: 8px 14px 4px;
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    .lab-group-header .test-code {
        font-size: 11px;
        font-weight: 500;
        color: #6b7280;
        margin-left: 6px;
    }

    /* Speech-to-text mic button */
    .mic-btn.recording { background: #fee2e2 !important; border-color: #ef4444 !important; animation: mic-pulse 1s infinite; }
    .mic-btn.recording svg { stroke: #ef4444; }
    @keyframes mic-pulse { 0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.3); } 50% { box-shadow: 0 0 0 8px rgba(239,68,68,0); } }

    /* Drug Section Layout */
    .drug-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 10px; }
    .drug-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-top: 10px; }

    .drug-grid-2 label, .drug-grid-3 label { margin-top: 0; }

    #dose-calculator { background: var(--bg-soft); border: 1px solid var(--border); padding: 14px; border-radius: var(--radius-md); }
    #dose-info { font-size: 13px; margin-bottom: 8px; color: var(--text); }
    /* Treatment / Procedure Pills */
    .treatment-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
    .treatment-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 10px 6px 12px;
        background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 20px;
        font-size: 13px; color: #1e40af; line-height: 1.3;
    }
    .treatment-pill--procedure { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
    .treatment-pill .pill-name { font-weight: 600; }
    .treatment-pill .pill-detail { font-weight: 400; color: var(--text-muted); }
    .treatment-pill .pill-delete {
        display: inline-flex; align-items: center; justify-content: center;
        width: 20px; height: 20px; border-radius: 50%; border: none;
        background: rgba(0,0,0,0.08); color: #6b7280; font-size: 14px;
        cursor: pointer; padding: 0; line-height: 1; flex-shrink: 0; transition: all 0.15s;
    }
    .treatment-pill .pill-delete:hover { background: #ef4444; color: #fff; transform: none; }

    /* Clinical exam grid */
    .exam-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 10px; }
    @media (max-width: 640px) { .exam-grid { grid-template-columns: 1fr 1fr; } }

    /* Searchable dropdown */
    .search-dropdown {
        display: none;
        position: absolute;
        top: 100%; left: 0; right: 0;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        max-height: 200px;
        overflow-y: auto;
        z-index: 50;
        box-shadow: var(--shadow-md);
        margin-top: 2px;
    }

    /* Dose warning */
    #dose-warning {
        display: none;
        margin-top: 10px;
        padding: 8px 10px;
        border-radius: var(--radius-sm);
        font-size: 13px;
    }

    /* Preview modal */
    #casesheet-preview-modal { backdrop-filter: blur(3px); }
    #casesheet-preview-modal > div {
        background: #fff;
        width: 700px; max-width: 95%;
        margin: 30px auto;
        padding: 0;
        border-radius: 12px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    .cs-preview-header { display: flex; justify-content: space-between; align-items: center; padding: 24px 30px 16px; border-bottom: 3px solid #2563eb; }
    .cs-preview-header img { max-height: 50px; max-width: 120px; }
    .cs-preview-body { padding: 20px 30px 24px; }
    .cs-preview-badge { display: inline-block; background: #2563eb; color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; letter-spacing: .5px; margin-bottom: 14px; }
    .cs-preview-info { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 6px; font-size: 13px; }
    .cs-preview-section { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 10px; }
    .cs-preview-section h4 { font-size: 11px; text-transform: uppercase; color: #2563eb; letter-spacing: .5px; margin: 0 0 4px; font-weight: 600; }
    .cs-preview-section p { font-size: 13px; color: #334155; margin: 0; line-height: 1.6; }

    /* AI panel */
    #ai-insights-output {
        font-size: 13px;
        background: var(--bg-soft);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 12px;
        margin-top: 10px;
        min-height: 80px;
        max-height: 70vh;
        overflow-y: auto;
    }

    /* Readonly inputs */
    input[readonly] { background: var(--bg-soft); color: var(--text); border-color: var(--border); cursor: not-allowed; }

    @media (max-width: 640px) {
        .drug-grid-2, .drug-grid-3 { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

<div class="v-main">
    <div class="v-card">

    @php
        $pet = $appointment->pet;

        $petSummaryParts = [];
        if ($appointment->calculated_age_at_visit) { $petSummaryParts[] = $appointment->calculated_age_at_visit; }
        if ($pet->gender) { $petSummaryParts[] = ucfirst($pet->gender); }
        if ($pet->breed) { $petSummaryParts[] = ucfirst($pet->breed); }
        if ($appointment->weight) { $petSummaryParts[] = $appointment->weight . ' kg'; }
        $petSummary = implode(' · ', $petSummaryParts);
    @endphp

    <h2 style="font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 10px;">
        @if($caseSheet) Edit Case Sheet @else Add Case Sheet @endif
    </h2>

    <p style="font-size:14px;margin:0 0 6px;">
        <strong>Pet:</strong> {{ ucfirst($pet->name) }}
        @if($petSummary) · {{ $petSummary }} @endif
    </p>
    <p style="font-size:14px;margin:0;">
        <strong>Appointment Date:</strong>
        {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A') }}
    </p>

    <hr class="v-divider">

    <form method="POST" action="{{ route('vet.casesheet.store', $appointment->id) }}">
        @csrf

        @php
            function aiTextarea($name, $label, $value = '', $rows = 3, $placeholder = '') {
                return "
                <div class='v-form-group'>
                    <label>{$label}</label>
                    <div style='position:relative;'>
                        <textarea name='{$name}' id='{$name}' rows='{$rows}' placeholder='{$placeholder}' class='v-input' style='padding-right:44px;'>{$value}</textarea>
                        <button type='button' class='mic-btn' data-field='{$name}' onclick='toggleMic(this)' title='Voice input' style='position:absolute;right:8px;top:8px;width:32px;height:32px;border-radius:50%;border:1px solid #d1d5db;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;'>
                            <svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='#6b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z'/><path d='M19 10v2a7 7 0 0 1-14 0v-2'/><line x1='12' y1='19' x2='12' y2='23'/><line x1='8' y1='23' x2='16' y2='23'/></svg>
                        </button>
                    </div>
                </div>";
            }
        @endphp

        {{-- Section: Assessment --}}
        <div class="cs-section cs-section--blue">
        <div class="cs-section-title"><span class="cs-section-icon">📋</span> Assessment</div>

        {!! aiTextarea('presenting_complaint', 'Presenting Complaint', $caseSheet->presenting_complaint ?? '', 3, 'Chief complaint in owner\'s words') !!}
        {!! aiTextarea('history', 'History', $caseSheet->history ?? '', 3, 'Relevant medical, dietary, vaccination, or illness history') !!}
        {!! aiTextarea('clinical_examination', 'Clinical Examination', $caseSheet->clinical_examination ?? '', 3, 'General condition, vitals, system-wise findings') !!}

        <div class="exam-grid">
            <div class="v-form-group">
                <label>Temperature (°F)</label>
                <input type="number" step="0.1" name="temperature" value="{{ $caseSheet->temperature ?? '' }}" placeholder="101.5" class="v-input">
            </div>
            <div class="v-form-group">
                <label>Heart Rate (bpm)</label>
                <input type="number" name="heart_rate" value="{{ $caseSheet->heart_rate ?? '' }}" placeholder="80" class="v-input">
            </div>
            <div class="v-form-group">
                <label>Respiratory Rate (bpm)</label>
                <input type="number" name="respiratory_rate" value="{{ $caseSheet->respiratory_rate ?? '' }}" placeholder="20" class="v-input">
            </div>
            <div class="v-form-group">
                <label>CRT (sec)</label>
                <select name="capillary_refill_time" class="v-input">
                    <option value="">—</option>
                    @foreach(['< 1 sec', '1-2 sec', '2-3 sec', '> 3 sec'] as $crt)
                        <option value="{{ $crt }}" {{ ($caseSheet->capillary_refill_time ?? '') === $crt ? 'selected' : '' }}>{{ $crt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="v-form-group">
                <label>Mucous Membrane</label>
                <select name="mucous_membrane" class="v-input">
                    <option value="">—</option>
                    @foreach(['Pink', 'Pale', 'Icteric', 'Cyanotic', 'Congested', 'Muddy'] as $mm)
                        <option value="{{ $mm }}" {{ ($caseSheet->mucous_membrane ?? '') === $mm ? 'selected' : '' }}>{{ $mm }}</option>
                    @endforeach
                </select>
            </div>
            <div class="v-form-group">
                <label>Hydration Status</label>
                <select name="hydration_status" class="v-input">
                    <option value="">—</option>
                    @foreach(['Normal', 'Mild dehydration (5%)', 'Moderate dehydration (7-8%)', 'Severe dehydration (10-12%)'] as $hs)
                        <option value="{{ $hs }}" {{ ($caseSheet->hydration_status ?? '') === $hs ? 'selected' : '' }}>{{ $hs }}</option>
                    @endforeach
                </select>
            </div>
            <div class="v-form-group">
                <label>Peripheral Lymph Nodes</label>
                <select name="lymph_nodes" class="v-input">
                    <option value="">—</option>
                    @foreach(['Normal', 'Enlarged - localised', 'Enlarged - generalised', 'Reactive', 'Not palpable'] as $ln)
                        <option value="{{ $ln }}" {{ ($caseSheet->lymph_nodes ?? '') === $ln ? 'selected' : '' }}>{{ $ln }}</option>
                    @endforeach
                </select>
            </div>
            <div class="v-form-group">
                <label>Body Condition Score</label>
                <select name="body_condition_score" class="v-input">
                    <option value="">—</option>
                    @for($i = 1; $i <= 9; $i++)
                        <option value="{{ $i }}/9" {{ ($caseSheet->body_condition_score ?? '') === "$i/9" ? 'selected' : '' }}>{{ $i }}/9</option>
                    @endfor
                </select>
            </div>
            <div class="v-form-group">
                <label>Pain Score</label>
                <select name="pain_score" class="v-input">
                    <option value="">—</option>
                    @for($i = 0; $i <= 10; $i++)
                        <option value="{{ $i }}/10" {{ ($caseSheet->pain_score ?? '') === "$i/10" ? 'selected' : '' }}>{{ $i }}/10</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="v-form-group">
            <label>Differentials</label>
            <textarea name="differentials" rows="2" placeholder="Possible differential diagnoses" class="v-input">{{ $caseSheet->differentials ?? '' }}</textarea>
        </div>

        {!! aiTextarea('diagnosis', 'Diagnosis', $caseSheet->diagnosis ?? '', 2, 'Confirmed / provisional diagnosis') !!}

        </div>{{-- /Assessment section --}}

        {{-- Section: Drug Treatment --}}
        <div class="cs-section cs-section--green">
        <div class="cs-section-title"><span class="cs-section-icon">💊</span> Drug Treatment</div>

        <h4 style="font-size:14px;font-weight:600;color:#374151;margin:0 0 8px;">Administer Drug (Injectable)</h4>

        <div class="drug-grid-2">
            <div style="position:relative;">
                <label>Generic Drug</label>
                <input type="text" id="drug-generic-search" placeholder="Type to search drugs..." autocomplete="off" class="v-input">
                <input type="hidden" id="drug-generic-select" value="">
                <div id="drug-generic-dropdown" class="search-dropdown"></div>
            </div>
            <div>
                <label>Strength (clinic stock only)</label>
                <select id="drug-strength-select" class="v-input">
                    <option value="">— select generic first —</option>
                </select>
            </div>
        </div>

        <div class="drug-grid-3">
            <div><label>KB Dose Range</label><input type="text" id="drug-dose" readonly placeholder="—" class="v-input"></div>
            <div><label>Frequency</label><input type="text" id="drug-frequency" readonly placeholder="—" class="v-input"></div>
            <div><label>Route</label><input type="text" id="drug-route" readonly placeholder="—" class="v-input"></div>
        </div>

        <div class="drug-grid-3">
            <div><label>Dose (mg/kg)</label><input type="number" step="0.001" id="dose-input" placeholder="0.000" class="v-input"></div>
            <div><label>Calculated mg</label><input type="number" step="0.001" id="calculated-mg" readonly class="v-input"></div>
            <div><label>Volume (ml)</label><input type="number" step="0.001" id="calculated-ml" readonly class="v-input"></div>
        </div>

        <div id="dose-warning"></div>

        <div class="v-form-group" style="margin-top:12px;">
            <label>Administer via Route</label>
            <select id="admin-route-select" class="v-input" style="width:220px;">
                <option value="">— Select Route —</option>
                @foreach($injectionRoutes as $ir)
                    <option value="{{ $ir->route_code }}">{{ $ir->route_code }} — {{ $ir->route_name }}</option>
                @endforeach
            </select>
        </div>

        <button type="button" onclick="addDrugTreatment()" class="v-btn v-btn--outline v-btn--sm">+ Add Drug Treatment</button>

        {{-- Drug Treatment Pills --}}
        <div id="drug-treatment-pills" class="treatment-pills">
            @foreach($appointment->treatments->filter(fn($t) => $t->drug_generic_id) as $treatment)
                <span class="treatment-pill" data-id="{{ $treatment->id }}">
                    <span>
                        <span class="pill-name">{{ optional($treatment->drugGeneric)->name ?? '—' }}</span>
                        @if($treatment->dose_volume_ml)
                            <span class="pill-detail">{{ $treatment->dose_volume_ml }}ml</span>
                        @endif
                        @if($treatment->route)
                            <span class="pill-detail">{{ $treatment->route }}</span>
                        @endif
                    </span>
                    <button type="button" class="pill-delete" onclick="deleteTreatment({{ $treatment->id }}, this)">&times;</button>
                </span>
            @endforeach
        </div>

        </div>{{-- /Drug Treatment section --}}

        {{-- Section: Procedures --}}
        <div class="cs-section cs-section--green">
        <div class="cs-section-title"><span class="cs-section-icon">🔧</span> Procedures</div>

        <div style="display:flex;gap:10px;align-items:flex-end;">
            <div style="flex:1;position:relative;">
                <input type="text" id="procedure-search" placeholder="Type to search procedures..." autocomplete="off" class="v-input">
                <input type="hidden" id="procedure-select" value="">
                <div id="procedure-dropdown" class="search-dropdown"></div>
            </div>
            <button type="button" onclick="addProcedure()" class="v-btn v-btn--outline v-btn--sm">+ Add</button>
        </div>

        {{-- Procedure Treatment Pills --}}
        <div id="procedure-treatment-pills" class="treatment-pills">
            @foreach($appointment->treatments->filter(fn($t) => !$t->drug_generic_id) as $treatment)
                <span class="treatment-pill treatment-pill--procedure" data-id="{{ $treatment->id }}">
                    <span class="pill-name">{{ optional($treatment->priceItem)->name ?? '—' }}</span>
                    <button type="button" class="pill-delete" onclick="deleteTreatment({{ $treatment->id }}, this)">&times;</button>
                </span>
            @endforeach
        </div>

        </div>{{-- /Procedures section --}}

        {{-- Section: Vaccinations (read-only — recording is via Clinical Actions) --}}
        @php $thisApptVaccs = $appointment->pet->vaccinations->where('appointment_id', $appointment->id); @endphp
        @if($thisApptVaccs->count() || $appointment->pet->vaccinations->count())
        <div class="cs-section cs-section--orange">
        <div class="cs-section-title"><span class="cs-section-icon">💉</span> Vaccinations</div>

        @foreach($thisApptVaccs as $vacc)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#fef3c7;border:1px solid #fde68a;border-radius:8px;margin-bottom:6px;">
                <span style="font-weight:600;font-size:13px;color:#92400e;">{{ $vacc->vaccine_name }}</span>
                <span style="font-size:11px;color:#6b7280;">{{ $vacc->dose_number }} · {{ $vacc->brand_name ?? '' }} · {{ $vacc->route }}</span>
                <span style="margin-left:auto;font-size:11px;color:#6b7280;">{{ \Carbon\Carbon::parse($vacc->administered_date)->format('d M Y') }}</span>
                @if($vacc->next_due_date)
                <span style="font-size:10px;color:#2563eb;">Next: {{ \Carbon\Carbon::parse($vacc->next_due_date)->format('d M Y') }}</span>
                @endif
            </div>
        @endforeach

        @php $prevVaccs = $appointment->pet->vaccinations->where('appointment_id', '!=', $appointment->id); @endphp
        @if($prevVaccs->count())
        <details style="margin-top:6px;">
            <summary style="font-size:11px;color:#6b7280;cursor:pointer;">Previous vaccinations ({{ $prevVaccs->count() }})</summary>
            <div style="margin-top:6px;font-size:12px;color:#374151;">
                @foreach($prevVaccs->take(10) as $pv)
                <div style="padding:3px 0;border-bottom:1px solid #f3f4f6;">
                    {{ $pv->vaccine_name }} ({{ $pv->dose_number }}) — {{ \Carbon\Carbon::parse($pv->administered_date)->format('d M Y') }}
                </div>
                @endforeach
            </div>
        </details>
        @endif

        </div>
        @endif

        {{-- Section: Treatment Notes --}}
        <div class="cs-section cs-section--teal">
        <div class="cs-section-title"><span class="cs-section-icon">📝</span> Treatment Notes</div>

        {!! aiTextarea('treatment_given', 'Treatment Given', $caseSheet->treatment_given ?? '', 3, 'Medications, fluids, injections administered') !!}

        <div class="v-form-group">
            <label>Procedures Done</label>
            <textarea name="procedures_done" rows="2" placeholder="Any procedures performed during the visit" class="v-input">{{ $caseSheet->procedures_done ?? '' }}</textarea>
        </div>

        </div>{{-- /Treatment Notes section --}}

        {{-- Section: Lab Orders (read-only summary — ordering is via Clinical Actions) --}}
        @if($appointment->labOrders->isNotEmpty())
        <div class="cs-section cs-section--purple">
        <div class="cs-section-title"><span class="cs-section-icon">🔬</span> Lab Orders</div>
            @foreach($appointment->labOrders as $labOrder)
                <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#faf5ff;border:1px solid #e9d5ff;border-radius:8px;margin-bottom:6px;">
                    <span style="font-weight:600;font-size:12px;color:#7c3aed;">{{ $labOrder->order_number }}</span>
                    <span style="font-size:11px;color:var(--text-muted);">{{ $labOrder->tests->pluck('test_name')->implode(', ') }}</span>
                    <span style="margin-left:auto;display:inline-block;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600;
                        @if($labOrder->status === 'approved') background:#dcfce7;color:#166534;
                        @elseif($labOrder->status === 'results_uploaded') background:#d1fae5;color:#065f46;
                        @elseif($labOrder->status === 'processing') background:#e0e7ff;color:#4338ca;
                        @else background:#fef3c7;color:#92400e;
                        @endif">{{ str_replace('_', ' ', ucfirst($labOrder->status)) }}</span>
                    <a href="{{ route('vet.lab-orders.show', $labOrder) }}" style="font-size:11px;color:#7c3aed;font-weight:600;">View</a>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Section: Follow-up & Advice --}}
        <div class="cs-section cs-section--blue">
        <div class="cs-section-title"><span class="cs-section-icon">📅</span> Follow-up & Advice</div>

        {!! aiTextarea('further_plan', 'Further Treatment Plan', $caseSheet->further_plan ?? '', 2, 'Follow-up plan, investigations, monitoring') !!}
        {!! aiTextarea('advice', 'Advice', $caseSheet->advice ?? '', 2, 'Dietary advice, care instructions, warnings') !!}

        </div>{{-- /Follow-up section --}}

        <div style="display:flex;gap:10px;margin-top:16px;flex-wrap:wrap;">
            <button type="button" onclick="showCaseSheetPreview()" class="v-btn v-btn--outline">Preview Case Sheet</button>
            <button type="submit" class="v-btn v-btn--primary">Save Case Sheet</button>
            <a href="{{ route('vet.certificates.create', ['pet' => $appointment->pet_id, 'appointment_id' => $appointment->id]) }}" class="v-btn v-btn--outline" style="border-color:#f59e0b;color:#f59e0b;">📜 Issue Certificate</a>
            <a href="{{ route('vet.appointments.case', $appointment->id) }}" class="v-btn v-btn--ghost">Cancel</a>
        </div>
    </form>

    </div>
</div>

{{-- RIGHT: AI Clinical Insights --}}
<div class="v-aside">
    <div class="v-card v-card--compact" style="background:var(--bg-soft);">
        <h3 style="margin:0 0 8px;font-size:16px;font-weight:600;color:var(--text-dark);">AI Clinical Insights</h3>

        <p style="font-size:13px;color:var(--text);margin:0 0 12px;">
            AI-generated clinical assistance. Final diagnosis and treatment decisions
            remain the responsibility of the veterinarian.
        </p>

        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
            <button type="button" onclick="getClinicalInsights()" class="v-btn v-btn--outline v-btn--sm">
                Generate AI Clinical Insights
            </button>
            <span style="font-size:11px;color:var(--text-muted);">1 credit per use</span>
        </div>

        <div id="ai-credit-info" style="font-size:12px;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
            <span style="color:#fbbf24;">&#9733;</span>
            <span id="ai-credit-balance" style="font-weight:600;">{{ \App\Models\VetAiCredit::where('vet_id', auth('vet')->id())->value('balance') ?? 0 }}</span>
            <span style="color:var(--text-muted);">credits remaining</span>
            <a href="{{ route('vet.credits.index') }}" style="font-size:11px;color:var(--primary);margin-left:4px;">Buy more</a>
        </div>

        <div id="ai-insights-output">
            Click the button to generate insights.
        </div>
    </div>
</div>

{{-- PREVIEW MODAL --}}
@php
    $csOrg = $appointment->clinic->organisation ?? null;
    $csClinic = $appointment->clinic;
    $csVet = $appointment->vet;
    $csLogoUrl = $csOrg && $csOrg->logo_path ? asset('storage/' . $csOrg->logo_path) : null;
@endphp

<div id="casesheet-preview-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);overflow:auto;z-index:9999;">
    <div>
        {{-- Header with branding --}}
        <div class="cs-preview-header">
            <div>
                @if($csLogoUrl)
                    <img src="{{ $csLogoUrl }}" alt="{{ $csOrg->name ?? '' }}">
                @endif
                <div style="font-weight:700;font-size:14px;color:#1a1a1a;margin-top:3px;">{{ $csOrg->name ?? '' }}</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:17px;font-weight:700;color:#2563eb;">{{ $csClinic->name ?? '' }}</div>
                <div style="font-size:11px;color:#6b7280;">{{ $csClinic->address ?? '' }}, {{ $csClinic->city ?? '' }} {{ $csClinic->pincode ?? '' }}</div>
                @if($csClinic->phone ?? null)
                    <div style="font-size:11px;color:#6b7280;">{{ $csClinic->phone }}</div>
                @endif
            </div>
        </div>

        <div class="cs-preview-body">
            <div class="cs-preview-badge">CASE SHEET</div>

            {{-- Patient info --}}
            <div class="cs-preview-info">
                <div><strong>Patient:</strong> {{ $appointment->pet->name }} ({{ ucfirst($appointment->pet->species) }})</div>
                <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}</div>
                <div><strong>Breed:</strong> {{ $appointment->pet->breed ?? '—' }}</div>
                <div><strong>Weight:</strong> {{ $appointment->weight ? $appointment->weight.' kg' : '—' }}</div>
                <div><strong>Parent:</strong> {{ $appointment->pet->petParent->name ?? '—' }}</div>
                <div><strong>Doctor:</strong> {{ $csVet->name ?? '—' }}</div>
            </div>

            {{-- Dynamic clinical content --}}
            <div id="casesheet-preview-content"></div>

            {{-- Footer --}}
            <div style="margin-top:24px;display:flex;justify-content:space-between;align-items:flex-end;">
                <div style="font-size:11px;color:#94a3b8;">Preview — {{ now()->format('d M Y, h:i A') }}</div>
                <div style="text-align:right;">
                    <div style="border:2px solid #333;display:inline-block;padding:8px 16px;text-align:center;font-size:12px;border-radius:4px;">
                        <div style="font-weight:bold;font-size:13px;">{{ $csVet->name ?? '' }}</div>
                        @if($csVet->degree ?? null)
                            <div style="color:#555;">{{ $csVet->degree }}</div>
                        @endif
                        @if($csVet->registration_number ?? null)
                            <div style="color:#555;">Reg. No: {{ $csVet->registration_number }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div style="margin-top:14px;display:flex;justify-content:space-between;align-items:center;">
                <div style="display:flex;gap:8px;">
                    <button type="button" id="rewriteAllBtn" onclick="rewriteAllWithAI()" class="v-btn v-btn--sm" style="background:#2563eb;color:#fff;">
                        ✨ Rewrite All with AI
                    </button>
                    <button type="button" id="undoRewriteBtn" onclick="undoRewrite()" class="v-btn v-btn--outline v-btn--sm" style="display:none;">
                        ↩ Undo Rewrite
                    </button>
                    <button type="button" id="waShareBtn" onclick="sendCaseSheetWhatsApp()" class="v-btn v-btn--sm" style="background:{{ ($waAlreadySent ?? false) ? '#16a34a' : '#25D366' }};color:#fff;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:4px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        {{ ($waAlreadySent ?? false) ? '↻ Resend via WhatsApp' : 'Send via WhatsApp' }}
                    </button>
                </div>
                <button onclick="closeCaseSheetPreview()" class="v-btn v-btn--outline v-btn--sm">Close Preview</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
{{-- SPEECH-TO-TEXT --}}
<script>
let activeMic = null;
let recognition = null;
let baseText = ''; // text before recording started

function toggleMic(btn) {
    const fieldName = btn.dataset.field;
    const textarea = document.getElementById(fieldName);

    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
        alert('Speech recognition is not supported in this browser. Please use Chrome.');
        return;
    }

    // If already recording this field, stop
    if (activeMic === btn) {
        stopMic();
        return;
    }

    // Stop any existing recording
    if (activeMic) stopMic();

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.lang = 'en-IN';

    // Save the text that existed before we started recording
    baseText = textarea.value.trim();
    if (baseText && !baseText.endsWith('.') && !baseText.endsWith(',')) {
        baseText += '. ';
    } else if (baseText) {
        baseText += ' ';
    }

    let allFinal = ''; // accumulates all finalized chunks

    recognition.onstart = function() {
        btn.classList.add('recording');
        btn.title = 'Listening... click to stop';
        activeMic = btn;
    };

    recognition.onresult = function(event) {
        // Rebuild allFinal from scratch each time to avoid duplication
        allFinal = '';
        let interim = '';

        for (let i = 0; i < event.results.length; i++) {
            if (event.results[i].isFinal) {
                allFinal += event.results[i][0].transcript;
            } else {
                interim += event.results[i][0].transcript;
            }
        }

        // Show: base + all finalized + current interim (gray preview)
        textarea.value = baseText + allFinal + interim;

        // Auto-resize
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 300) + 'px';
    };

    recognition.onerror = function(event) {
        if (event.error !== 'aborted' && event.error !== 'no-speech') {
            console.warn('Speech error:', event.error);
        }
        stopMic();
    };

    recognition.onend = function() {
        // Only keep base + finalized text (drop any interim)
        textarea.value = (baseText + allFinal).trim();
        if (activeMic === btn) stopMic();
    };

    recognition.start();
}

function stopMic() {
    if (recognition) {
        recognition.stop();
        recognition = null;
    }
    if (activeMic) {
        activeMic.classList.remove('recording');
        activeMic.title = 'Voice input';
        activeMic = null;
    }
}

// Stop recording when navigating away
window.addEventListener('beforeunload', stopMic);
</script>

{{-- PREVIEW JS --}}
<script>
function showCaseSheetPreview() {
    const firstSections = [
        ['Presenting Complaint', 'presenting_complaint'],
        ['History', 'history'],
        ['Clinical Examination', 'clinical_examination'],
    ];
    const laterSections = [
        ['Differentials', 'differentials'],
        ['Diagnosis', 'diagnosis'],
    ];
    const afterTreatmentSections = [
        ['Further Treatment Plan', 'further_plan'],
        ['Advice', 'advice'],
    ];

    const vitalFields = [
        ['Temp', 'temperature', '°F'],
        ['HR', 'heart_rate', ' bpm'],
        ['RR', 'respiratory_rate', ' bpm'],
        ['CRT', 'capillary_refill_time', ''],
        ['MM', 'mucous_membrane', ''],
        ['Hydration', 'hydration_status', ''],
        ['PLN', 'lymph_nodes', ''],
        ['BCS', 'body_condition_score', ''],
        ['Pain', 'pain_score', ''],
    ];

    function sectionHtml(label, value) {
        return `<div class="cs-preview-section"><h4>${label}</h4><p>${value}</p></div>`;
    }

    let html = '';

    firstSections.forEach(([label, name]) => {
        const field = document.getElementById(name);
        if (field && field.value.trim()) html += sectionHtml(label, field.value);
    });

    const vitals = vitalFields.map(([label, name, unit]) => {
        const el = document.querySelector(`[name="${name}"]`);
        const val = el ? el.value : '';
        return val ? `${label}: ${val}${unit}` : null;
    }).filter(Boolean).join(' | ');
    if (vitals) html += sectionHtml('Vitals', vitals);

    laterSections.forEach(([label, name]) => {
        const field = document.getElementById(name);
        if (field && field.value.trim()) html += sectionHtml(label, field.value);
    });

    // Drug Treatments (from pills)
    const drugPills = document.querySelectorAll('#drug-treatment-pills .treatment-pill');
    if (drugPills.length > 0) {
        let drugRows = '';
        drugPills.forEach((pill, i) => {
            const name = pill.querySelector('.pill-name')?.textContent?.trim() || '—';
            const details = Array.from(pill.querySelectorAll('.pill-detail')).map(d => d.textContent.trim()).join(' · ');
            drugRows += `<div style="display:flex;align-items:center;gap:10px;padding:6px 0;${i > 0 ? 'border-top:1px solid #e2e8f0;' : ''}">
                <span style="background:#2563eb;color:#fff;font-size:10px;font-weight:700;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">${i+1}</span>
                <span style="font-weight:600;font-size:13px;color:#1e293b;">${name}</span>
                ${details ? `<span style="font-size:12px;color:#64748b;">${details}</span>` : ''}
            </div>`;
        });
        html += `<div class="cs-preview-section"><h4>Drug Treatments Administered</h4>${drugRows}</div>`;
    }

    // Treatment Given (textarea)
    const treatmentField = document.getElementById('treatment_given');
    if (treatmentField && treatmentField.value.trim()) html += sectionHtml('Treatment Given', treatmentField.value);

    // Procedures (from pills)
    const procPills = document.querySelectorAll('#procedure-treatment-pills .treatment-pill');
    if (procPills.length > 0) {
        let procRows = '';
        procPills.forEach((pill, i) => {
            const name = pill.querySelector('.pill-name')?.textContent?.trim() || '—';
            procRows += `<div style="display:flex;align-items:center;gap:10px;padding:6px 0;${i > 0 ? 'border-top:1px solid #e2e8f0;' : ''}">
                <span style="background:#16a34a;color:#fff;font-size:10px;font-weight:700;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">${i+1}</span>
                <span style="font-weight:600;font-size:13px;color:#1e293b;">${name}</span>
            </div>`;
        });
        html += `<div class="cs-preview-section"><h4>Procedures Performed</h4>${procRows}</div>`;
    }

    // Procedures Done (textarea)
    const procDoneField = document.getElementById('procedures_done');
    if (procDoneField && procDoneField.value.trim()) html += sectionHtml('Procedures Done', procDoneField.value);

    // Remaining sections
    afterTreatmentSections.forEach(([label, name]) => {
        const field = document.getElementById(name);
        if (field && field.value.trim()) html += sectionHtml(label, field.value);
    });

    document.getElementById('casesheet-preview-content').innerHTML = html || '<div class="cs-preview-section"><p>No clinical details entered.</p></div>';
    document.getElementById('casesheet-preview-modal').style.display = 'block';
}

function closeCaseSheetPreview() {
    document.getElementById('casesheet-preview-modal').style.display = 'none';
}

// ── Send Case Sheet via WhatsApp ──
function sendCaseSheetWhatsApp() {
    const btn = document.getElementById('waShareBtn');
    btn.disabled = true;
    btn.innerHTML = '<span style="display:inline-flex;align-items:center;gap:6px;">Sending...</span>';

    fetch('/whatsapp/send/case-sheet/{{ $appointment->id }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '✓ Sent via WhatsApp';
            btn.style.background = '#16a34a';
            setTimeout(() => {
                btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:4px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg> Send via WhatsApp';
                btn.style.background = '#25D366';
                btn.disabled = false;
            }, 3000);
        } else {
            alert(data.message || 'Failed to send');
            btn.innerHTML = 'Retry WhatsApp';
            btn.style.background = '#ef4444';
            btn.disabled = false;
        }
    })
    .catch(err => {
        alert('Error sending WhatsApp: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = 'Retry WhatsApp';
    });
}

// ── Rewrite All with AI ──
let originalValues = {};

function rewriteAllWithAI() {
    const fields = [
        'presenting_complaint', 'history', 'clinical_examination',
        'differentials', 'diagnosis', 'treatment_given',
        'procedures_done', 'further_plan', 'advice'
    ];

    // Save originals for undo
    originalValues = {};
    fields.forEach(name => {
        const el = document.getElementById(name);
        if (el) originalValues[name] = el.value;
    });

    const btn = document.getElementById('rewriteAllBtn');
    btn.disabled = true;
    btn.innerHTML = '⏳ Rewriting...';

    // Send all fields to AI for comprehensive rewrite
    const data = {};
    fields.forEach(name => {
        const el = document.getElementById(name);
        if (el && el.value.trim()) data[name] = el.value;
    });

    if (Object.keys(data).length === 0) {
        btn.disabled = false;
        btn.innerHTML = '✨ Rewrite All with AI';
        alert('No fields to rewrite. Enter some text first.');
        return;
    }

    // Rewrite each non-empty field
    let pending = Object.keys(data).length;
    let completed = 0;

    Object.entries(data).forEach(([field, text]) => {
        fetch('{{ url("/vet/ai/refine") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                field: field.replace(/_/g, ' '),
                text: text
            })
        })
        .then(res => res.json())
        .then(result => {
            if (result.refined) {
                document.getElementById(field).value = result.refined;
            }
            completed++;
            if (completed >= pending) {
                btn.disabled = false;
                btn.innerHTML = '✨ Rewrite All with AI';
                document.getElementById('undoRewriteBtn').style.display = 'inline-flex';
                // Refresh preview
                showCaseSheetPreview();
            }
        })
        .catch(() => {
            completed++;
            if (completed >= pending) {
                btn.disabled = false;
                btn.innerHTML = '✨ Rewrite All with AI';
            }
        });
    });
}

function undoRewrite() {
    Object.entries(originalValues).forEach(([name, value]) => {
        const el = document.getElementById(name);
        if (el) el.value = value;
    });
    document.getElementById('undoRewriteBtn').style.display = 'none';
    // Refresh preview
    showCaseSheetPreview();
}
</script>

{{-- AI REWRITE JS --}}
<script>
function refineField(field) {
    const textarea = document.getElementById(field);

    if (!textarea || !textarea.value.trim()) {
        alert('Please enter some text first');
        return;
    }

    fetch('{{ url('/vet/ai/refine') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            field: field.replace('_', ' '),
            text: textarea.value
        })
    })
    .then(res => {
        if (res.status === 402) return res.json().then(d => { throw { credits: true, message: d.error, url: d.purchase_url }; });
        return res.json();
    })
    .then(data => {
        if (data.refined) {
            textarea.value = data.refined;
            updateCreditBalance();
        } else {
            alert('AI could not refine the text');
        }
    })
    .catch(err => {
        if (err && err.credits) { alert(err.message); if (err.url) window.open(err.url, '_blank'); }
        else alert('AI request failed');
    });
}
</script>

<script>
function getClinicalInsights() {
    showAiLoading(document.getElementById('ai-insights-output'), 'Generating AI clinical insights...');

    fetch('{{ url('/vet/ai/clinical-insights') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            presenting_complaint: document.getElementById('presenting_complaint')?.value || '',
            history: document.getElementById('history')?.value || '',
            clinical_examination: document.getElementById('clinical_examination')?.value || '',
            differentials: document.getElementById('differentials')?.value || '',
            diagnosis: document.getElementById('diagnosis')?.value || '',
            treatment_given: document.getElementById('treatment_given')?.value || '',
            procedures_done: document.getElementById('procedures_done')?.value || '',
            further_plan: document.getElementById('further_plan')?.value || '',
            advice: document.getElementById('advice')?.value || '',
            temperature: document.querySelector('[name="temperature"]')?.value || '',
            heart_rate: document.querySelector('[name="heart_rate"]')?.value || '',
            respiratory_rate: document.querySelector('[name="respiratory_rate"]')?.value || '',
            capillary_refill_time: document.querySelector('[name="capillary_refill_time"]')?.value || '',
            mucous_membrane: document.querySelector('[name="mucous_membrane"]')?.value || '',
            hydration_status: document.querySelector('[name="hydration_status"]')?.value || '',
            lymph_nodes: document.querySelector('[name="lymph_nodes"]')?.value || '',
            body_condition_score: document.querySelector('[name="body_condition_score"]')?.value || '',
            pain_score: document.querySelector('[name="pain_score"]')?.value || '',
            species: "{{ $appointment->pet->species }}",
            breed: "{{ $appointment->pet->breed }}",
            gender: "{{ $appointment->pet->gender }}",
            pet_age: "{{ $appointment->calculated_age_at_visit }}",
            body_weight: "{{ $appointment->weight }}",
            treatments: (function() {
                const items = document.querySelectorAll('#drug-treatment-pills .treatment-pill');
                return Array.from(items).map(el => el.textContent.replace('×','').trim()).join('\n') || 'None';
            })(),
            procedures: (function() {
                const items = document.querySelectorAll('#procedure-treatment-pills .treatment-pill');
                return Array.from(items).map(el => el.textContent.replace('×','').trim()).join('\n') || 'None';
            })()
        })
    })
    .then(res => {
        if (res.status === 402) return res.json().then(d => { throw { credits: true, message: d.error, url: d.purchase_url }; });
        return res.json();
    })
    .then(data => {
        if (data.raw) {
            setAiOutput(document.getElementById('ai-insights-output'), data.raw);
            updateCreditBalance();
        } else {
            document.getElementById('ai-insights-output').innerHTML = '<span style="color:var(--text-muted);">AI did not return insights.</span>';
        }
    })
    .catch(err => {
        if (err && err.credits) {
            document.getElementById('ai-insights-output').innerHTML =
                '<span style="color:#dc2626;">' + err.message + '</span><br><a href="' + err.url + '" style="color:var(--primary);font-size:13px;">Purchase AI Credits</a>';
        } else {
            document.getElementById('ai-insights-output').innerHTML = '<span style="color:#dc2626;">Failed to generate AI insights.</span>';
        }
    });
}

function updateCreditBalance() {
    fetch('{{ route("vet.credits.balance") }}')
        .then(r => r.json())
        .then(d => {
            var el = document.getElementById('ai-credit-balance');
            if (el) el.textContent = d.balance;
        })
        .catch(() => {});
}

let selectedInventoryItemId = null;
let selectedGenericId       = null;
let selectedIsMultiUse      = false;

function addDrugTreatment() {
    if (!selectedInventoryItemId) {
        alert('Please select a drug and strength from the clinic stock.');
        return;
    }

    const doseMl = parseFloat(document.getElementById('calculated-ml').value);
    const doseMg = parseFloat(document.getElementById('calculated-mg').value);

    if (!doseMl || doseMl <= 0) {
        alert('Please enter a dose to calculate the volume.');
        return;
    }

    const route = document.getElementById('admin-route-select').value || document.getElementById('drug-route').value;

    if (!route) {
        alert('Please select the administration route.');
        return;
    }

    fetch(`/vet/appointments/{{ $appointment->id }}/treatment/add`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            drug_generic_id:   selectedGenericId,
            inventory_item_id: selectedInventoryItemId,
            dose_mg:           doseMg,
            dose_volume_ml:    doseMl,
            route:             route,
        })
    })
    .then(res => {
        if (!res.ok) {
            return res.text().then(t => { throw new Error(`Server error ${res.status}: ${t.substring(0, 200)}`); });
        }
        return res.json();
    })
    .then(data => {
        const drugName = document.getElementById('drug-generic-search').value;
        const strengthSel = document.getElementById('drug-strength-select');
        const strengthText = strengthSel.options[strengthSel.selectedIndex]?.text?.trim() || '';
        const doseMl = document.getElementById('calculated-ml').value;
        const route = document.getElementById('admin-route-select').value || document.getElementById('drug-route').value;

        const container = document.getElementById('drug-treatment-pills');
        const pill = document.createElement('span');
        pill.className = 'treatment-pill';
        pill.dataset.id = data.id || '';
        let detail = '';
        if (doseMl) detail += ` <span class="pill-detail">${parseFloat(doseMl).toFixed(1)}ml</span>`;
        if (route) detail += ` <span class="pill-detail">${route}</span>`;
        pill.innerHTML = `<span><span class="pill-name">${drugName}</span>${detail}</span><button type="button" class="pill-delete" onclick="deleteTreatment(${data.id}, this)">&times;</button>`;
        container.appendChild(pill);

        // Reset fields
        document.getElementById('drug-generic-search').value = '';
        document.getElementById('drug-generic-select').value = '';
        strengthSel.innerHTML = '<option value="">— select generic first —</option>';
        document.getElementById('drug-dose').value = '';
        document.getElementById('drug-frequency').value = '';
        document.getElementById('drug-route').value = '';
        document.getElementById('dose-input').value = '';
        document.getElementById('calculated-mg').value = '';
        document.getElementById('calculated-ml').value = '';
        document.getElementById('admin-route-select').value = '';
        document.getElementById('dose-warning').style.display = 'none';
        selectedInventoryItemId = null;
        selectedGenericId = null;
    })
    .catch(err => {
        console.error('Drug treatment add failed:', err);
        alert('Failed to add drug treatment. ' + err.message);
    });
}

function addProcedure() {
    const id = document.getElementById('procedure-select').value;
    const name = document.getElementById('procedure-search').value;
    if (!id) { alert('Select a procedure.'); return; }

    fetch(`/vet/appointments/{{ $appointment->id }}/treatment/add`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ price_list_item_id: id })
    })
    .then(res => {
        if (!res.ok) {
            return res.text().then(t => { throw new Error(`Server error ${res.status}: ${t.substring(0, 200)}`); });
        }
        return res.json();
    })
    .then(data => {
        const container = document.getElementById('procedure-treatment-pills');
        const pill = document.createElement('span');
        pill.className = 'treatment-pill treatment-pill--procedure';
        pill.dataset.id = data.id || '';
        pill.innerHTML = `<span class="pill-name">${name}</span><button type="button" class="pill-delete" onclick="deleteTreatment(${data.id}, this)">&times;</button>`;
        container.appendChild(pill);
        document.getElementById('procedure-search').value = '';
        document.getElementById('procedure-select').value = '';
    })
    .catch(err => {
        console.error('Procedure add failed:', err);
        alert('Failed to add procedure. ' + err.message);
    });
}

function deleteTreatment(treatmentId, btn) {
    if (!confirm('Remove this treatment?')) return;

    fetch(`/vet/appointments/{{ $appointment->id }}/treatment/${treatmentId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(() => {
        const pill = btn.closest('.treatment-pill');
        if (pill) pill.remove();
    });
}
</script>

@php
    $drugGenericsJson = $drugGenerics->map(fn($d) => ['id' => $d->id, 'name' => $d->name])->values();
    $procedureItemsJson = $priceListItems->whereIn('item_type', ['service', 'procedure'])->values()->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'price' => $p->price]);
@endphp
<script>
// Drug generic searchable dropdown
const drugGenerics = @json($drugGenericsJson);
const drugSearchInput = document.getElementById('drug-generic-search');
const drugDropdown = document.getElementById('drug-generic-dropdown');
const drugHiddenInput = document.getElementById('drug-generic-select');

drugSearchInput.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    if (q.length < 1) { drugDropdown.style.display = 'none'; return; }
    const matches = drugGenerics.filter(d => d.name.toLowerCase().includes(q)).slice(0, 12);
    if (matches.length === 0) {
        drugDropdown.innerHTML = '<div style="padding:10px 14px;color:var(--text-light);font-size:13px;">No drugs found</div>';
    } else {
        drugDropdown.innerHTML = matches.map(d =>
            '<div class="drug-option" data-id="'+d.id+'" data-name="'+d.name+'" style="padding:8px 14px;cursor:pointer;font-size:14px;border-bottom:1px solid var(--border-light);" onmouseover="this.style.background=\'var(--primary-soft)\'" onmouseout="this.style.background=\'#fff\'">' + d.name + '</div>'
        ).join('');
    }
    drugDropdown.style.display = 'block';
});

drugSearchInput.addEventListener('focus', function() {
    if (this.value.trim().length >= 1) this.dispatchEvent(new Event('input'));
});

drugDropdown.addEventListener('click', function(e) {
    const opt = e.target.closest('.drug-option');
    if (!opt) return;
    drugHiddenInput.value = opt.dataset.id;
    drugSearchInput.value = opt.dataset.name;
    drugDropdown.style.display = 'none';
    onDrugGenericSelected(opt.dataset.id);
});

document.addEventListener('click', function(e) {
    if (!drugSearchInput.contains(e.target) && !drugDropdown.contains(e.target)) drugDropdown.style.display = 'none';
});

// Procedure searchable dropdown
const procedureItems = @json($procedureItemsJson);
const procSearchInput = document.getElementById('procedure-search');
const procDropdown = document.getElementById('procedure-dropdown');
const procHiddenInput = document.getElementById('procedure-select');

procSearchInput.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    if (q.length < 1) { procDropdown.style.display = 'none'; return; }
    const matches = procedureItems.filter(p => p.name.toLowerCase().includes(q)).slice(0, 12);
    if (matches.length === 0) {
        procDropdown.innerHTML = '<div style="padding:10px 14px;color:var(--text-light);font-size:13px;">No procedures found</div>';
    } else {
        procDropdown.innerHTML = matches.map(p =>
            '<div class="proc-option" data-id="'+p.id+'" data-name="'+p.name+'" style="padding:8px 14px;cursor:pointer;font-size:14px;border-bottom:1px solid var(--border-light);display:flex;justify-content:space-between;" onmouseover="this.style.background=\'var(--primary-soft)\'" onmouseout="this.style.background=\'#fff\'">' +
            '<span>' + p.name + '</span><span style="color:var(--text-muted);font-size:13px;">₹' + parseFloat(p.price).toFixed(2) + '</span></div>'
        ).join('');
    }
    procDropdown.style.display = 'block';
});

procSearchInput.addEventListener('focus', function() {
    if (this.value.trim().length >= 1) this.dispatchEvent(new Event('input'));
});

procDropdown.addEventListener('click', function(e) {
    const opt = e.target.closest('.proc-option');
    if (!opt) return;
    procHiddenInput.value = opt.dataset.id;
    procSearchInput.value = opt.dataset.name;
    procDropdown.style.display = 'none';
});

document.addEventListener('click', function(e) {
    if (!procSearchInput.contains(e.target) && !procDropdown.contains(e.target)) procDropdown.style.display = 'none';
});

// Drug generic selection handler
function onDrugGenericSelected(genericId) {
    selectedGenericId       = genericId || null;
    selectedInventoryItemId = null;

    const strengthSelect = document.getElementById('drug-strength-select');
    strengthSelect.innerHTML = '<option value="">— loading... —</option>';

    if (!genericId) {
        strengthSelect.innerHTML = '<option value="">— select generic first —</option>';
        return;
    }

    fetch(`/vet/drug-strengths/${genericId}`)
    .then(res => res.json())
    .then(data => {
        strengthSelect.innerHTML = '<option value="">Select strength...</option>';

        if (data.length === 0) {
            strengthSelect.innerHTML = '<option value="">No KB entries or inventory for this drug</option>';
            return;
        }

        data.forEach(function(item) {
            const option = document.createElement('option');
            option.value = item.inventory_item_id ?? '';
            option.dataset.isMultiUse   = item.is_multi_use ? '1' : '0';
            option.dataset.strength     = item.strength_value;
            option.dataset.strengthUnit = item.strength_unit;
            option.dataset.inStock      = item.in_stock ? '1' : '0';
            const stockLabel = item.in_stock
                ? ' ✓ In stock'
                : (item.source === 'kb' ? ' (KB only — not imported)' : ' ✗ Out of stock');
            option.text = item.strength_value + ' ' + item.strength_unit
                + ' (' + (item.form || 'injection') + ')'
                + (item.name ? ' — ' + item.name : '')
                + stockLabel;
            strengthSelect.appendChild(option);
        });
    });

    fetch(`/vet/drug-dosage/${genericId}?species={{ $appointment->pet->species }}`)
    .then(res => res.json())
    .then(data => {
        if(!data.dosages || data.dosages.length === 0){
            document.getElementById('drug-dose').value =
                'Dosage guidelines not available for {{ ucfirst($appointment->pet->species) }}';
            return;
        }

        const dose = data.dosages[0];
        recommendedMin = parseFloat(dose.dose_min);
        recommendedMax = parseFloat(dose.dose_max);

        document.getElementById('drug-dose').value = dose.dose_min + " - " + dose.dose_max + " " + dose.dose_unit;
        document.getElementById('dose-input').value = dose.dose_min;
        document.getElementById('drug-frequency').value = dose.frequencies || '';
        document.getElementById('drug-route').value = dose.routes || '';
    });
}

document.getElementById('drug-strength-select').addEventListener('change', function(){
    selectedInventoryItemId = this.value || null;
    selectedIsMultiUse = this.options[this.selectedIndex]?.dataset.isMultiUse === '1';
    calculateDose();
});

const weight = {{ $appointment->weight ?? 0 }};

let recommendedMin = null;
let recommendedMax = null;

function calculateDose(){
    const dose = parseFloat(document.getElementById('dose-input').value);
    const sel  = document.getElementById('drug-strength-select');
    const strength = parseFloat(sel.options[sel.selectedIndex]?.dataset.strength || 0);

    if(!dose || !strength || !weight) return;

    const mg = dose * weight;
    const ml = mg / strength;

    document.getElementById('calculated-mg').value = mg.toFixed(2);
    document.getElementById('calculated-ml').value = ml.toFixed(3);

    checkDoseSafety(dose);
}

document.getElementById('dose-input').addEventListener('input', calculateDose);
document.getElementById('drug-strength-select').addEventListener('change', calculateDose);
</script>

<script>
function checkDoseSafety(dose){
    const warningBox = document.getElementById('dose-warning');

    if(recommendedMin === null || recommendedMax === null){
        warningBox.style.display = 'none';
        return;
    }

    if(dose < recommendedMin){
        warningBox.style.display = 'block';
        warningBox.style.background = 'var(--warning-soft)';
        warningBox.style.border = '1px solid var(--warning-border)';
        warningBox.innerText = 'Dose below recommended range';
    } else if(dose > recommendedMax){
        warningBox.style.display = 'block';
        warningBox.style.background = 'var(--danger-soft)';
        warningBox.style.border = '1px solid var(--danger-border)';
        warningBox.innerText = 'Dose exceeds recommended range';
    } else {
        warningBox.style.display = 'block';
        warningBox.style.background = 'var(--success-soft)';
        warningBox.style.border = '1px solid var(--success-border)';
        warningBox.innerText = 'Dose within recommended range';
    }
}

/* ===========================
   Lab Test Ordering
   =========================== */
const labTestPills = [];

document.getElementById('lab-test-search').addEventListener('input', function() {
    const q = this.value.trim();
    const dropdown = document.getElementById('lab-test-dropdown');
    if (q.length < 2) { dropdown.style.display = 'none'; return; }

    fetch(`{{ route('vet.lab-orders.available-tests') }}?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(data => {
            const vetCanSelectLab = data.vet_can_select_lab || false;
            const tests = data.tests || [];

            if (!tests.length) {
                dropdown.innerHTML = `<div style="padding:12px;font-size:13px;color:#9ca3af;">No tests found for "${q}". Type a custom name and click "+ Add Test".</div>`;
            } else {
                dropdown.innerHTML = tests.map(t => {
                    const labs = t.labs || [];
                    const hasInHouse = labs.some(l => l.type === 'in_house');

                    if (vetCanSelectLab && labs.length > 1) {
                        return `<div>
                            <div class="lab-group-header">${t.name} <span class="test-code">${t.code || ''}</span></div>
                            ${labs.map(l => {
                                const unavail = l.type === 'in_house' && !l.available ? ' (Unavailable)' : '';
                                const badgeClass = l.type === 'in_house' ? 'lab-badge--in' : 'lab-badge--ext';
                                const tag = l.type === 'in_house' ? 'IN' : 'EXT';
                                const params = l.parameters && l.parameters.length ? l.parameters.join(', ') : '';
                                return `<div class="lab-option${unavail ? ' opacity:0.5' : ''}" onclick="selectLabTest(${l.id}, '${t.name.replace(/'/g, "\\'")}', '${l.type}', ${l.lab_id || 'null'}, ${l.price || 0})"${unavail ? ' style="opacity:0.5"' : ''} title="${params}">
                                    <div style="flex:1;">
                                        <div><span class="lab-badge ${badgeClass}">${tag}</span> ${l.lab_name}${unavail}</div>
                                        ${params ? `<div style="font-size:10px;color:#9ca3af;margin-top:1px;max-width:400px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${params}</div>` : ''}
                                    </div>
                                    <span class="lab-price">₹${l.price}</span>
                                </div>`;
                            }).join('')}
                        </div>`;
                    } else {
                        const lab = labs[0] || {};
                        const unavail = lab.type === 'in_house' && !lab.available ? ' (Unavailable)' : '';
                        const labInfo = vetCanSelectLab
                            ? `${lab.lab_name || 'Unknown'} · ₹${lab.price || 0}`
                            : (hasInHouse ? 'In-house' : 'External');
                        const params = lab.parameters && lab.parameters.length ? lab.parameters.join(', ') : '';
                        return `<div class="lab-option" onclick="selectLabTest(${lab.id || 0}, '${t.name.replace(/'/g, "\\'")}', '${lab.type || 'in_house'}', ${lab.lab_id || 'null'}, ${lab.price || 0})"${unavail ? ' style="opacity:0.5"' : ''} title="${params}">
                            <div style="flex:1;">
                                <span style="font-weight:600;">${t.name}${unavail}</span>
                                ${params ? `<div style="font-size:10px;color:#9ca3af;margin-top:1px;">${params}</div>` : ''}
                            </div>
                            <span class="lab-price">${labInfo}</span>
                        </div>`;
                    }
                }).join('');
            }
            dropdown.style.display = 'block';
        });
});

function selectLabTest(id, name, type, labId, price) {
    const input = document.getElementById('lab-test-search');

    // Check duplicate
    if (labTestPills.some(t => t.name.toLowerCase() === name.toLowerCase() && t.lab_id == labId)) return;

    // Directly add to pills (skip the "Add Test" click)
    labTestPills.push({
        name,
        catalog_id: type === 'in_house' ? id : null,
        external_test_id: type === 'external' ? id : null,
        type: type || 'in_house',
        lab_id: labId || null,
        price: parseFloat(price) || 0,
    });

    input.value = '';
    input.dataset.testId = '';
    input.dataset.testType = '';
    input.dataset.labId = '';
    input.dataset.price = '';
    document.getElementById('lab-test-dropdown').style.display = 'none';
    renderLabTestPills();
}

function addLabTest() {
    const input = document.getElementById('lab-test-search');
    const name = input.value.trim();
    if (!name) return;

    // Check duplicate
    if (labTestPills.some(t => t.name.toLowerCase() === name.toLowerCase())) return;

    labTestPills.push({
        name,
        catalog_id: input.dataset.testType === 'in_house' ? input.dataset.testId : null,
        external_test_id: input.dataset.testType === 'external' ? input.dataset.testId : null,
        type: input.dataset.testType || 'in_house',
        lab_id: input.dataset.labId || null,
        price: parseFloat(input.dataset.price) || 0,
    });
    input.value = '';
    input.dataset.testId = '';
    input.dataset.testType = '';
    input.dataset.labId = '';
    input.dataset.price = '';
    document.getElementById('lab-test-dropdown').style.display = 'none';
    renderLabTestPills();
}

function removeLabTest(index) {
    labTestPills.splice(index, 1);
    renderLabTestPills();
}

function renderLabTestPills() {
    const container = document.getElementById('lab-test-pills');
    container.innerHTML = labTestPills.map((t, i) => {
        const isExt = t.type === 'external';
        const bg = isExt ? '#fef3c7' : '#faf5ff';
        const border = isExt ? '#fde68a' : '#e9d5ff';
        const color = isExt ? '#92400e' : '#7c3aed';
        const badge = isExt ? '<span style="font-size:9px;background:#f59e0b;color:#fff;padding:1px 5px;border-radius:4px;margin-left:2px;">EXT</span>' : '<span style="font-size:9px;background:#7c3aed;color:#fff;padding:1px 5px;border-radius:4px;margin-left:2px;">IN</span>';
        return `<span style="display:inline-flex;align-items:center;gap:6px;padding:5px 10px 5px 12px;background:${bg};border:1px solid ${border};border-radius:20px;font-size:13px;color:${color};">
            <span style="font-weight:600;">${t.name}</span>${badge}
            <button type="button" onclick="removeLabTest(${i})" style="background:rgba(0,0,0,0.08);border:none;border-radius:50%;width:18px;height:18px;font-size:12px;cursor:pointer;color:#6b7280;display:flex;align-items:center;justify-content:center;">&times;</button>
        </span>`;
    }).join('');
    document.getElementById('lab-order-btn').disabled = labTestPills.length === 0;
}

function submitLabOrder() {
    if (labTestPills.length === 0) return;

    const btn = document.getElementById('lab-order-btn');
    btn.disabled = true;
    btn.textContent = 'Ordering...';

    // Group tests by lab: in-house tests together, each external lab's tests together
    const groups = {};
    labTestPills.forEach(t => {
        const key = t.type === 'external' && t.lab_id ? 'ext_' + t.lab_id : 'in_house';
        if (!groups[key]) groups[key] = { lab_id: t.type === 'external' ? t.lab_id : null, tests: [] };
        groups[key].tests.push({
            name: t.name,
            catalog_id: t.catalog_id || null,
            external_test_id: t.external_test_id || null,
            external_lab_id: t.type === 'external' ? t.lab_id : null,
            type: t.type || 'in_house',
            price: t.price || 0,
        });
    });

    const priority = document.getElementById('lab-priority').value;
    const notes = document.getElementById('lab-notes').value;
    const orderGroups = Object.values(groups);

    // Submit each group as a separate order
    const promises = orderGroups.map(group => {
        return fetch('{{ route("vet.lab-orders.store", $appointment) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                tests: group.tests,
                lab_id: group.lab_id,
                priority: priority,
                notes: notes,
            })
        }).then(r => {
            if (!r.ok) {
                return r.text().then(text => {
                    try { return JSON.parse(text); }
                    catch(e) { return { message: 'Server error ' + r.status + ': ' + text.substring(0, 200) }; }
                });
            }
            return r.json();
        });
    });

    Promise.all(promises)
    .then(results => {
        const errors = results.filter(r => r.errors || r.message);
        if (errors.length > 0) {
            const errMsg = errors.map(e => e.message || JSON.stringify(e.errors)).join('\n');
            alert('Error: ' + errMsg);
            btn.disabled = false;
            btn.textContent = 'Order Lab Tests';
            return;
        }
        const orderNums = results.filter(r => r.success).map(r => r.order.order_number);
        if (orderNums.length > 0) {
            labTestPills.length = 0;
            renderLabTestPills();
            document.getElementById('lab-notes').value = '';
            const msg = orderNums.length === 1
                ? 'Lab order created: ' + orderNums[0]
                : orderNums.length + ' lab orders created: ' + orderNums.join(', ');
            alert(msg);
            location.reload();
        } else {
            alert('Error creating lab orders. Check console.');
            console.error('Lab order responses:', results);
            btn.disabled = false;
            btn.textContent = 'Order Lab Tests';
        }
    })
    .catch(err => {
        console.error('Lab order error:', err);
        alert('Failed to create lab orders: ' + err.message);
        btn.disabled = false;
        btn.textContent = 'Order Lab Tests';
    });
}

// ========== VACCINATION SECTION ==========
const vaccSearchInput = document.getElementById('vaccine-search');
const vaccDropdown = document.getElementById('vaccine-dropdown');
const vaccForm = document.getElementById('vaccine-form');
let vaccineData = [];

vaccSearchInput.addEventListener('input', function() {
    const q = this.value.trim();
    if (q.length < 2) { vaccDropdown.style.display = 'none'; return; }

    fetch(`{{ route('vet.vaccinations.search') }}?q=${encodeURIComponent(q)}`)
    .then(r => r.json())
    .then(data => {
        vaccineData = data;
        vaccDropdown.innerHTML = '';
        if (!data.length) {
            vaccDropdown.innerHTML = '<div style="padding:8px;color:#9ca3af;font-size:12px;">No vaccines found. You can type a custom name.</div>';
            vaccDropdown.style.display = 'block';
            return;
        }
        data.forEach(g => {
            const div = document.createElement('div');
            div.style.cssText = 'padding:8px 10px;cursor:pointer;font-size:13px;border-bottom:1px solid #f3f4f6;';
            div.textContent = g.name;
            div.onmouseover = () => div.style.background = '#f3f4f6';
            div.onmouseout = () => div.style.background = '';
            div.onclick = () => selectVaccine(g);
            vaccDropdown.appendChild(div);
        });
        vaccDropdown.style.display = 'block';
    });
});

function selectVaccine(generic) {
    vaccSearchInput.value = generic.name;
    vaccDropdown.style.display = 'none';
    document.getElementById('vacc-generic-name').value = generic.name;
    document.getElementById('vacc-name-display').value = generic.name;

    // Populate brands
    const brandSelect = document.getElementById('vacc-brand');
    brandSelect.innerHTML = '<option value="">Select brand</option>';
    (generic.brands || []).forEach(b => {
        const opt = document.createElement('option');
        opt.value = b.brand_name;
        opt.textContent = b.brand_name + (b.manufacturer ? ' (' + b.manufacturer + ')' : '');
        opt.dataset.manufacturer = b.manufacturer || '';
        brandSelect.appendChild(opt);
    });

    // Auto-set next due date (21 days for most vaccines)
    const today = new Date();
    today.setDate(today.getDate() + 21);
    document.getElementById('vacc-next-due').value = today.toISOString().split('T')[0];

    vaccForm.style.display = 'block';
}

document.addEventListener('click', function(e) {
    if (!vaccSearchInput.contains(e.target) && !vaccDropdown.contains(e.target)) vaccDropdown.style.display = 'none';
});

function onVaccBrandSelected(select) {
    const brandName = select.value;
    const datalist = document.getElementById('vacc-batch-list');
    datalist.innerHTML = '';
    if (!brandName) return;

    // Fetch batch numbers from clinic inventory
    fetch(`/vet/inventory-batches?brand_name=${encodeURIComponent(brandName)}&clinic_id={{ session('active_clinic_id') }}`)
    .then(r => r.json())
    .then(batches => {
        batches.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b.batch_number;
            opt.label = b.batch_number + (b.expiry_date ? ' (Exp: ' + b.expiry_date + ')' : '') + ' — Qty: ' + b.quantity;
            datalist.appendChild(opt);
        });
        if (batches.length === 1) {
            document.getElementById('vacc-batch').value = batches[0].batch_number;
        }
    })
    .catch(() => {});
}

function addVaccination() {
    const name = document.getElementById('vacc-generic-name').value || vaccSearchInput.value;
    if (!name) return alert('Select a vaccine first');

    const brandSelect = document.getElementById('vacc-brand');
    const selectedOption = brandSelect.options[brandSelect.selectedIndex];
    const data = {
        vaccinations: [{
            pet_id: {{ $appointment->pet_id }},
            appointment_id: {{ $appointment->id }},
            vaccine_name: name,
            brand_name: brandSelect.value || null,
            manufacturer: selectedOption?.dataset?.manufacturer || null,
            batch_number: document.getElementById('vacc-batch').value || null,
            dose_number: document.getElementById('vacc-dose').value,
            administered_date: document.getElementById('vacc-date').value,
            next_due_date: document.getElementById('vacc-next-due').value || null,
            route: document.getElementById('vacc-route').value,
        }]
    };

    fetch('{{ route("vet.vaccinations.store") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const pills = document.getElementById('vaccination-pills');
            const pill = document.createElement('span');
            pill.className = 'treatment-pill';
            pill.style.cssText = 'background:#fef3c7;border-color:#fde68a;color:#92400e;';
            pill.innerHTML = `<span class="pill-name">${name} (${data.vaccinations[0].dose_number}) — ${data.vaccinations[0].administered_date}</span>`;
            pills.appendChild(pill);

            // Reset form
            vaccForm.style.display = 'none';
            vaccSearchInput.value = '';
            document.getElementById('vacc-batch').value = '';
        }
    });
}

function deleteVaccination(id, btn) {
    if (!confirm('Delete this vaccination record?')) return;
    fetch(`/vet/vaccinations/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
    })
    .then(r => r.json())
    .then(() => { btn.closest('.treatment-pill').remove(); });
}
</script>
@endsection
