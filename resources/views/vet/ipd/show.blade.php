@extends('layouts.vet')

@section('page-class')v-page--wide @endsection

@section('head')
<style>
    .ipd-columns { display: flex; gap: 20px; align-items: flex-start; }
    .ipd-main { flex: 1; min-width: 0; }
    .ipd-sidebar { width: 380px; flex-shrink: 0; }
    .ipd-actions { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 18px; }
    .ipd-action-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 16px; }
    .ipd-action-card h3 { font-size: 13px; font-weight: 700; color: var(--primary); margin: 0 0 10px; }

    .patient-banner {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .patient-banner .meta { font-size: 13px; color: var(--text-muted); }
    .patient-banner .meta span { margin-right: 12px; }

    .timeline-item {
        position: relative;
        padding-left: 20px;
        margin-bottom: 16px;
        border-left: 2px solid var(--border);
        padding-bottom: 4px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -5px; top: 6px;
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--primary);
    }
    .timeline-item .t-time { font-size: 11px; color: var(--text-muted); }
    .timeline-item .t-content { font-size: 13px; margin-top: 2px; }
    .timeline-item .t-badge {
        display: inline-block;
        padding: 1px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        background: var(--primary-soft);
        color: var(--primary);
        margin-right: 6px;
    }
    .timeline-item .t-by { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

    .note-card {
        background: var(--bg-soft);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 12px 14px;
        margin-bottom: 10px;
    }
    .note-card .note-type { font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--primary); }
    .note-card .note-body { font-size: 13px; margin-top: 4px; white-space: pre-wrap; }
    .note-card .note-meta { font-size: 11px; color: var(--text-muted); margin-top: 6px; }

    .side-form label { display: block; font-size: 12px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px; margin-top: 10px; }
    .side-form input, .side-form select, .side-form textarea {
        width: 100%; padding: 7px 10px; font-size: 13px;
        border: 1px solid var(--border); border-radius: var(--radius-sm);
        background: #fff; box-sizing: border-box; font-family: var(--font);
    }
    .side-form textarea { resize: vertical; min-height: 60px; }
    .side-form .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .side-form .row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; }

    .collapse-toggle { cursor: pointer; user-select: none; display: flex; justify-content: space-between; align-items: center; }
    .collapse-toggle::after { content: '\25BC'; font-size: 10px; color: var(--text-muted); transition: transform 0.2s; }
    .collapse-toggle.collapsed::after { transform: rotate(-90deg); }
    .collapse-body.hidden { display: none; }

    .discharge-banner {
        background: var(--bg-soft);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 16px 20px;
        margin-bottom: 16px;
    }
    .discharge-banner h4 { margin: 0 0 8px; font-size: 14px; color: var(--text-dark); }
    .discharge-banner p { font-size: 13px; color: var(--text); margin: 0 0 4px; }

    @media (max-width: 900px) {
        .ipd-columns { flex-direction: column; }
        .ipd-sidebar { width: 100%; }
    }
</style>
@endsection

@section('content')

<a href="{{ route('vet.ipd.index') }}" class="v-back">&larr; Back to IPD List</a>

{{-- Patient Banner --}}
<div class="v-card">
    <div class="patient-banner">
        <div>
            <h2 style="font-size:22px;font-weight:700;margin:0 0 4px;color:var(--text-dark);">{{ $admission->pet->name ?? '—' }}</h2>
            <div class="meta">
                <span>{{ ucfirst($admission->pet->species ?? '') }} &middot; {{ $admission->pet->breed ?? '' }}</span>
                <span>Parent: {{ $admission->pet->petParent->name ?? '—' }} &middot; {{ $admission->pet->petParent->phone ?? '' }}</span>
            </div>
            <div class="meta" style="margin-top:6px;">
                <span><strong>Admitted:</strong> {{ $admission->admission_date->format('d M Y, h:i A') }}</span>
                @if($admission->cage_number) <span><strong>Cage:</strong> {{ $admission->cage_number }}</span> @endif
                @if($admission->ward) <span><strong>Ward:</strong> {{ $admission->ward }}</span> @endif
                <span><strong>By:</strong> {{ $admission->admitted_by_name }}</span>
            </div>
        </div>
        <span class="v-badge
            @if($admission->status === 'admitted') v-badge--green
            @elseif($admission->status === 'deceased') v-badge--red
            @elseif($admission->status === 'transferred') v-badge--yellow
            @else v-badge--gray
            @endif">
            {{ ucfirst($admission->status) }}
        </span>
    </div>
</div>

{{-- Admission Info --}}
@if($admission->admission_reason || $admission->tentative_diagnosis)
<div class="v-card v-card--compact">
    <h3 class="v-section-title">Admission Details</h3>
    @if($admission->admission_reason)
        <p style="font-size:13px;margin:0 0 6px;"><strong>Reason:</strong> {{ $admission->admission_reason }}</p>
    @endif
    @if($admission->tentative_diagnosis)
        <p style="font-size:13px;margin:0 0 6px;"><strong>Tentative Diagnosis:</strong> {{ $admission->tentative_diagnosis }}</p>
    @endif
    @if($admission->appointment_id)
        <p style="font-size:13px;margin-top:6px;">
            <a href="{{ route('vet.appointments.case', $admission->appointment_id) }}" class="v-link">View OPD Case &rarr;</a>
        </p>
    @endif
</div>
@endif

{{-- Discharge Banner --}}
@if($admission->status !== 'admitted')
<div class="discharge-banner">
    <h4>Discharged {{ $admission->discharged_at ? $admission->discharged_at->format('d M Y, h:i A') : '' }}
        @if($admission->discharged_by_name) &mdash; by {{ $admission->discharged_by_name }} @endif
    </h4>
    @if($admission->discharge_notes) <p><strong>Notes:</strong> {{ $admission->discharge_notes }}</p> @endif
    @if($admission->discharge_summary) <p style="margin-top:6px;white-space:pre-wrap;">{{ $admission->discharge_summary }}</p> @endif
</div>
@endif

{{-- Action Forms Row (full width, 3-column grid) --}}
@if($admission->isAdmitted())
<div class="ipd-actions">
    {{-- Record Vitals --}}
    <div class="ipd-action-card">
        <h3>Record Vitals</h3>
        <form class="side-form" onsubmit="submitVitals(event)">
            <input type="hidden" name="recorded_at" value="{{ now()->format('Y-m-d\TH:i') }}">
            <div class="row-3">
                <div><label>Temp (&deg;F)</label><input type="number" name="temperature" step="0.1" placeholder="101.5"></div>
                <div><label>HR (bpm)</label><input type="number" name="heart_rate" placeholder="120"></div>
                <div><label>RR (/min)</label><input type="number" name="respiratory_rate" placeholder="24"></div>
            </div>
            <div class="row-3">
                <div><label>Weight (kg)</label><input type="number" name="weight" step="0.01" placeholder="12.5"></div>
                <div><label>SpO2 (%)</label><input type="number" name="spo2" placeholder="98"></div>
                <div><label>Pain (0-10)</label><input type="number" name="pain_score" min="0" max="10" placeholder="3"></div>
            </div>
            <div class="row-2">
                <div><label>BP (Sys/Dia)</label><div style="display:flex;gap:4px;"><input type="number" name="blood_pressure_systolic" placeholder="120"><input type="number" name="blood_pressure_diastolic" placeholder="80"></div></div>
                <div><label>MM / CRT</label><div style="display:flex;gap:4px;"><select name="mucous_membrane" style="flex:1;"><option value="">MM</option><option value="pink">Pink</option><option value="pale">Pale</option><option value="cyanotic">Cyanotic</option><option value="icteric">Icteric</option></select><input type="number" name="crt" step="0.1" placeholder="CRT" style="width:60px;"></div></div>
            </div>
            <textarea name="notes" placeholder="Notes..." rows="1" style="margin-top:6px;min-height:32px;"></textarea>
            <button type="submit" class="v-btn v-btn--primary v-btn--sm" style="margin-top:8px;width:100%;">Save Vitals</button>
        </form>
    </div>

    {{-- Add Treatment --}}
    <div class="ipd-action-card">
        <h3>Add Treatment</h3>
        <form class="side-form" onsubmit="submitTreatment(event)" id="ipd-treatment-form">
            <select name="treatment_type" id="ipd-type-select" required onchange="onTreatmentTypeChange(this.value)" style="margin-bottom:6px;">
                <option value="injection">Injection</option>
                <option value="medication">Medication</option>
                <option value="procedure">Procedure / Fluid Therapy</option>
            </select>

            <div id="ipd-drug-section">
                <div style="position:relative;">
                    <input type="text" id="ipd-drug-search" placeholder="Search drug..." autocomplete="off">
                    <input type="hidden" name="drug_generic_id" id="ipd-drug-generic-id">
                    <div id="ipd-drug-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 8px 20px rgba(0,0,0,0.1);max-height:180px;overflow-y:auto;z-index:10;"></div>
                </div>
                <select name="inventory_item_id" id="ipd-strength-select" style="margin-top:4px;">
                    <option value="">— select drug first —</option>
                </select>
                <div id="ipd-kb-info" style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:6px;padding:6px 8px;margin:6px 0;font-size:10px;color:#0369a1;display:none;">
                    KB: <strong><span id="ipd-kb-dose">—</span></strong> · <span id="ipd-kb-freq">—</span> · <span id="ipd-kb-route">—</span>
                </div>
                <select name="route" id="ipd-route-select" style="margin-top:4px;">
                    <option value="">— Route —</option>
                    @foreach($injectionRoutes as $ir)
                        <option value="{{ $ir->route_code }}">{{ $ir->route_code }} — {{ $ir->route_name }}</option>
                    @endforeach
                </select>
                <div class="row-2" style="margin-top:4px;">
                    <div><label>Wt (kg)</label><input type="number" id="ipd-pet-weight" value="{{ $admission->pet->weight ?? '' }}" step="0.1"></div>
                    <div><label>Dose (mg/kg)</label><input type="number" name="dose_mg_kg" id="ipd-dose-mgkg" step="0.001" placeholder="Auto"></div>
                </div>
                <div class="row-2">
                    <div><label>Calc mg</label><input type="number" name="dose_mg" id="ipd-calc-mg" step="0.01" readonly style="background:#f9fafb;"></div>
                    <div><label>Vol (ml)</label><input type="number" name="dose_volume_ml" id="ipd-calc-ml" step="0.001" readonly style="background:#f0fdf4;font-weight:700;"></div>
                </div>
                <div id="ipd-dose-warning" style="display:none;padding:4px 8px;border-radius:4px;font-size:10px;font-weight:600;margin-top:2px;"></div>
            </div>

            <div id="ipd-procedure-section" style="display:none;">
                <input type="text" name="procedure_name" placeholder="Procedure / Fluid therapy name" style="margin-top:4px;">
                <textarea name="procedure_description" placeholder="Details..." rows="2" style="margin-top:4px;"></textarea>
            </div>

            <input type="hidden" name="administered_at" value="{{ now()->format('Y-m-d\TH:i') }}">
            <textarea name="notes" placeholder="Notes..." rows="1" style="margin-top:6px;min-height:32px;"></textarea>
            <button type="submit" class="v-btn v-btn--primary v-btn--sm" style="margin-top:8px;width:100%;">Save Treatment</button>
        </form>
    </div>

    {{-- Add Note --}}
    <div class="ipd-action-card">
        <h3>Add Note</h3>
        <form class="side-form" onsubmit="submitNote(event)">
            <select name="note_type" required style="margin-bottom:6px;">
                <option value="progress">Progress</option>
                <option value="observation">Observation</option>
                <option value="plan">Plan</option>
                <option value="complication">Complication</option>
                <option value="communication">Owner Communication</option>
            </select>
            <textarea name="content" required placeholder="Write clinical note..." rows="4"></textarea>
            <button type="submit" class="v-btn v-btn--primary v-btn--sm" style="margin-top:8px;width:100%;">Save Note</button>
        </form>
    </div>
</div>
@endif

<div class="ipd-columns">
    {{-- LEFT: Main Content --}}
    <div class="ipd-main">

        {{-- Vitals --}}
        <div class="v-card v-card--compact">
            <h3 class="v-section-title">Vitals History</h3>
            @if($admission->vitals->isEmpty())
                <p style="font-size:13px;color:var(--text-muted);">No vitals recorded yet.</p>
            @else
                <div style="overflow-x:auto;">
                <table class="v-table">
                    <thead>
                        <tr>
                            <th>Time</th><th>Temp</th><th>HR</th><th>RR</th><th>Wt</th>
                            <th>SpO2</th><th>BP</th><th>MM</th><th>CRT</th><th>Pain</th><th>By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admission->vitals as $v)
                        <tr>
                            <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($v->recorded_at)->format('d/m H:i') }}</td>
                            <td>{{ $v->temperature ?? '—' }}</td>
                            <td>{{ $v->heart_rate ?? '—' }}</td>
                            <td>{{ $v->respiratory_rate ?? '—' }}</td>
                            <td>{{ $v->weight ?? '—' }}</td>
                            <td>{{ $v->spo2 ? $v->spo2.'%' : '—' }}</td>
                            <td>{{ $v->blood_pressure_systolic ? $v->blood_pressure_systolic.'/'.$v->blood_pressure_diastolic : '—' }}</td>
                            <td>{{ $v->mucous_membrane ?? '—' }}</td>
                            <td>{{ $v->crt ?? '—' }}</td>
                            <td>{{ $v->pain_score !== null ? $v->pain_score.'/10' : '—' }}</td>
                            <td style="font-size:11px;">{{ $v->recorded_by_name ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @endif
        </div>

        {{-- Treatments --}}
        <div class="v-card v-card--compact">
            <h3 class="v-section-title">Treatment Timeline</h3>
            @if($admission->treatments->isEmpty())
                <p style="font-size:13px;color:var(--text-muted);">No treatments recorded yet.</p>
            @else
                @foreach($admission->treatments as $tx)
                <div class="timeline-item">
                    <div class="t-time">{{ \Carbon\Carbon::parse($tx->administered_at)->format('d M Y, h:i A') }}</div>
                    <div class="t-content">
                        <span class="t-badge">{{ ucfirst($tx->treatment_type) }}</span>
                        @if($tx->drugGeneric) {{ $tx->drugGeneric->name }} @endif
                        @if($tx->priceItem) {{ $tx->priceItem->name }} @endif
                        @if($tx->route) <span style="color:var(--text-muted);">({{ $tx->route }})</span> @endif
                        @if($tx->dose_mg) &middot; {{ $tx->dose_mg }}mg @endif
                        @if($tx->dose_volume_ml) &middot; {{ $tx->dose_volume_ml }}ml @endif
                    </div>
                    @if($tx->notes) <div style="font-size:12px;color:var(--text);margin-top:2px;">{{ $tx->notes }}</div> @endif
                    <div class="t-by">By: {{ $tx->treated_by_type === 'vet' ? (\App\Models\Vet::find($tx->treated_by_id)?->name ?? '—') : (\App\Models\User::find($tx->treated_by_id)?->name ?? '—') }}</div>
                </div>
                @endforeach
            @endif
        </div>

        {{-- Notes --}}
        <div class="v-card v-card--compact">
            <h3 class="v-section-title">Clinical Notes</h3>
            @if($admission->notes->isEmpty())
                <p style="font-size:13px;color:var(--text-muted);">No notes yet.</p>
            @else
                @foreach($admission->notes as $note)
                <div class="note-card">
                    <div class="note-type">{{ ucfirst($note->note_type) }}</div>
                    <div class="note-body">{{ $note->content }}</div>
                    <div class="note-meta">
                        {{ $note->noted_by_type === 'vet' ? (\App\Models\Vet::find($note->noted_by_id)?->name ?? '—') : (\App\Models\User::find($note->noted_by_id)?->name ?? '—') }}
                        &middot; {{ $note->created_at->format('d M, h:i A') }}
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- RIGHT: Sidebar --}}
    @if($admission->isAdmitted())
    <div class="ipd-sidebar">

        {{-- Quick Stats --}}
        <div class="v-card v-card--compact">
            <h3 class="v-section-title">Stay Summary</h3>
            @php
                $days = (int) $admission->admission_date->diffInDays(now());
                $hours = (int) $admission->admission_date->diffInHours(now()) % 24;
            @endphp
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;">
                <div style="background:var(--bg-soft);padding:10px;border-radius:6px;text-align:center;">
                    <div style="font-size:20px;font-weight:700;color:var(--primary);">{{ $days }}d {{ $hours }}h</div>
                    <div style="font-size:10px;color:var(--text-muted);">Duration</div>
                </div>
                <div style="background:var(--bg-soft);padding:10px;border-radius:6px;text-align:center;">
                    <div style="font-size:20px;font-weight:700;">{{ $admission->treatments->count() }}</div>
                    <div style="font-size:10px;color:var(--text-muted);">Treatments</div>
                </div>
                <div style="background:var(--bg-soft);padding:10px;border-radius:6px;text-align:center;">
                    <div style="font-size:20px;font-weight:700;">{{ $admission->vitals->count() }}</div>
                    <div style="font-size:10px;color:var(--text-muted);">Vitals Checks</div>
                </div>
                <div style="background:var(--bg-soft);padding:10px;border-radius:6px;text-align:center;">
                    <div style="font-size:20px;font-weight:700;">{{ $admission->notes->count() }}</div>
                    <div style="font-size:10px;color:var(--text-muted);">Notes</div>
                </div>
            </div>
            @if($admission->vitals->last())
            <div style="margin-top:10px;padding:10px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;font-size:12px;">
                <strong>Last Vitals</strong> ({{ $admission->vitals->last()->recorded_at ? \Carbon\Carbon::parse($admission->vitals->last()->recorded_at)->diffForHumans() : '' }})<br>
                Temp: {{ $admission->vitals->last()->temperature ?? '—' }}&deg;F ·
                HR: {{ $admission->vitals->last()->heart_rate ?? '—' }} ·
                RR: {{ $admission->vitals->last()->respiratory_rate ?? '—' }}
            </div>
            @endif
        </div>

        {{-- Discharge --}}
        <div class="v-card v-card--compact" style="border:1px solid #fca5a5;">
            <h3 class="v-section-title" style="color:var(--danger);">Discharge Patient</h3>
            <form class="side-form" action="{{ route('vet.ipd.discharge', $admission->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to discharge this patient?')">
                @csrf
                <label>Discharge Notes</label>
                <textarea name="discharge_notes" placeholder="Reason for discharge, instructions..."></textarea>

                <label>Discharge Summary</label>
                <textarea name="discharge_summary" rows="3" placeholder="Full discharge summary..."></textarea>

                <button type="submit" class="v-btn v-btn--danger v-btn--sm" style="margin-top:10px;width:100%;">Discharge Patient</button>
            </form>
        </div>

    </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
const ADMISSION_ID = {{ $admission->id }};

function toggleCollapse(el) {
    el.classList.toggle('collapsed');
    const body = el.nextElementSibling;
    body.classList.toggle('hidden');
}

function formData(form) {
    const fd = new FormData(form);
    const obj = {};
    fd.forEach((v, k) => { if (v !== '') obj[k] = v; });
    return obj;
}

function submitVitals(e) {
    e.preventDefault();
    const data = formData(e.target);
    fetch(`/vet/ipd/${ADMISSION_ID}/vitals`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(data)
    }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
        else alert(res.message || 'Error saving vitals');
    }).catch(() => alert('Error saving vitals'));
}

// ===== Treatment Type Switching =====
function onTreatmentTypeChange(type) {
    document.getElementById('ipd-drug-section').style.display = (type === 'injection' || type === 'medication') ? '' : 'none';
    document.getElementById('ipd-procedure-section').style.display = type === 'procedure' ? '' : 'none';
}

// ===== IPD Drug Search & Dose Calculation (mirrors case sheet) =====
let ipdSelectedStrength = null;
let ipdRecommendedMin = null;
let ipdRecommendedMax = null;
const petSpecies = '{{ $admission->pet->species ?? "dog" }}';

document.getElementById('ipd-drug-search')?.addEventListener('input', function() {
    const q = this.value.trim();
    const dd = document.getElementById('ipd-drug-dropdown');
    if (q.length < 2) { dd.style.display = 'none'; return; }

    fetch(`/vet/drug-search?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(drugs => {
            if (!drugs.length) {
                dd.innerHTML = '<div style="padding:10px;color:#9ca3af;font-size:12px;">No drugs found</div>';
            } else {
                dd.innerHTML = drugs.map(d =>
                    `<div onclick="selectIpdDrug(${d.generic_id})" style="padding:8px 12px;cursor:pointer;font-size:12px;border-bottom:1px solid #f3f4f6;"
                        onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background=''">
                        <strong>${d.generic_name}</strong>
                        ${d.dose_info ? '<span style="color:#6b7280;font-size:11px;"> · ' + d.dose_info + '</span>' : ''}
                        ${d.in_inventory ? '<span style="color:#16a34a;font-size:10px;"> ✓ In stock</span>' : ''}
                    </div>`
                ).join('');
            }
            dd.style.display = 'block';
        });
});

function selectIpdDrug(genericId) {
    // Get the drug data fresh
    fetch(`/vet/drug-search?q=`)
        .catch(() => {});

    document.getElementById('ipd-drug-dropdown').style.display = 'none';

    // Fetch dosage from KB (same as case sheet)
    fetch(`/vet/drug-dosage/${genericId}?species=${petSpecies}`)
        .then(r => r.json())
        .then(data => {
            const kbInfo = document.getElementById('ipd-kb-info');
            if (data.dosages && data.dosages.length > 0) {
                const d = data.dosages[0];
                ipdRecommendedMin = parseFloat(d.dose_min);
                ipdRecommendedMax = parseFloat(d.dose_max);

                document.getElementById('ipd-kb-dose').textContent = d.dose_min + '-' + d.dose_max + ' ' + (d.dose_unit || 'mg/kg');
                document.getElementById('ipd-kb-freq').textContent = d.frequencies || '—';
                document.getElementById('ipd-kb-route').textContent = d.routes || '—';

                // Auto-fill dose with dose_min
                document.getElementById('ipd-dose-mgkg').value = d.dose_min;
                calcIpdDose();
            } else {
                document.getElementById('ipd-kb-dose').textContent = 'No dosage data';
                document.getElementById('ipd-kb-freq').textContent = '—';
                document.getElementById('ipd-kb-route').textContent = '—';
                ipdRecommendedMin = null;
                ipdRecommendedMax = null;
            }
            kbInfo.style.display = 'block';
        });

    // Fetch drug name and set it
    fetch(`/vet/drug-search?q=id:${genericId}`)
        .catch(() => {});

    document.getElementById('ipd-drug-generic-id').value = genericId;

    // Load strengths from inventory
    fetch(`/vet/drug-search?q=`)
        .catch(() => {});

    // Use the existing drug search to get strengths
    fetch(`/vet/drug-search?q=${encodeURIComponent(document.getElementById('ipd-drug-search').value)}`)
        .then(r => r.json())
        .then(drugs => {
            const drug = drugs.find(d => d.generic_id == genericId);
            if (drug) {
                document.getElementById('ipd-drug-search').value = drug.generic_name;
                const sel = document.getElementById('ipd-strength-select');
                sel.innerHTML = '<option value="">— Select strength —</option>';
                if (drug.strengths && drug.strengths.length) {
                    drug.strengths.forEach(s => {
                        sel.innerHTML += `<option value="${s.inventory_item_id}" data-strength="${s.strength_value}" data-unit="${s.strength_unit}">${s.brand_name} ${s.strength_value}${s.strength_unit} (${s.form})</option>`;
                    });
                    // Auto-select first if only one
                    if (drug.strengths.length === 1) {
                        sel.selectedIndex = 1;
                        ipdSelectedStrength = parseFloat(drug.strengths[0].strength_value);
                        calcIpdDose();
                    }
                }
            }
        });
}

document.getElementById('ipd-strength-select')?.addEventListener('change', function() {
    const opt = this.selectedOptions[0];
    ipdSelectedStrength = opt?.dataset?.strength ? parseFloat(opt.dataset.strength) : null;
    calcIpdDose();
});

function calcIpdDose() {
    const weight = parseFloat(document.getElementById('ipd-pet-weight')?.value) || 0;
    const mgkg = parseFloat(document.getElementById('ipd-dose-mgkg')?.value) || 0;

    if (!weight || !mgkg) return;

    const totalMg = mgkg * weight;
    document.getElementById('ipd-calc-mg').value = totalMg.toFixed(2);

    if (ipdSelectedStrength && ipdSelectedStrength > 0) {
        const vol = totalMg / ipdSelectedStrength;
        document.getElementById('ipd-calc-ml').value = vol.toFixed(3);
    }

    // Dose safety check
    const warn = document.getElementById('ipd-dose-warning');
    if (ipdRecommendedMin !== null && ipdRecommendedMax !== null) {
        if (mgkg < ipdRecommendedMin * 0.8) {
            warn.style.display = 'block';
            warn.style.background = '#fef3c7';
            warn.style.color = '#92400e';
            warn.textContent = 'Below recommended range (' + ipdRecommendedMin + '-' + ipdRecommendedMax + ' mg/kg)';
        } else if (mgkg > ipdRecommendedMax * 1.2) {
            warn.style.display = 'block';
            warn.style.background = '#fee2e2';
            warn.style.color = '#991b1b';
            warn.textContent = 'ABOVE recommended range (' + ipdRecommendedMin + '-' + ipdRecommendedMax + ' mg/kg)';
        } else {
            warn.style.display = 'none';
        }
    }
}

document.getElementById('ipd-dose-mgkg')?.addEventListener('input', calcIpdDose);
document.getElementById('ipd-pet-weight')?.addEventListener('input', calcIpdDose);

function submitTreatment(e) {
    e.preventDefault();
    const data = formData(e.target);
    fetch(`/vet/ipd/${ADMISSION_ID}/treatments`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(data)
    }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
        else alert(res.message || 'Error saving treatment');
    }).catch(() => alert('Error saving treatment'));
}

function submitNote(e) {
    e.preventDefault();
    const data = formData(e.target);
    fetch(`/vet/ipd/${ADMISSION_ID}/notes`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(data)
    }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
        else alert(res.message || 'Error saving note');
    }).catch(() => alert('Error saving note'));
}
</script>
@endsection
