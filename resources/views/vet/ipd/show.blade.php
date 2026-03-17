@extends('layouts.vet')

@section('page-class')v-page--wide @endsection

@section('head')
<style>
    .ipd-columns { display: flex; gap: 20px; align-items: flex-start; }
    .ipd-main { flex: 1; min-width: 0; }
    .ipd-sidebar { width: 340px; flex-shrink: 0; }

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

        {{-- Add Vitals --}}
        <div class="v-card v-card--compact">
            <h3 class="v-section-title collapse-toggle" onclick="toggleCollapse(this)">Record Vitals</h3>
            <div class="collapse-body">
                <form class="side-form" onsubmit="submitVitals(event)">
                    <label>Recorded At</label>
                    <input type="datetime-local" name="recorded_at" value="{{ now()->format('Y-m-d\TH:i') }}" required>

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
                        <div><label>BP Sys</label><input type="number" name="blood_pressure_systolic" placeholder="120"></div>
                        <div><label>BP Dia</label><input type="number" name="blood_pressure_diastolic" placeholder="80"></div>
                    </div>

                    <div class="row-2">
                        <div>
                            <label>Mucous Membrane</label>
                            <select name="mucous_membrane">
                                <option value="">—</option>
                                <option value="pink">Pink</option>
                                <option value="pale">Pale</option>
                                <option value="cyanotic">Cyanotic</option>
                                <option value="icteric">Icteric</option>
                                <option value="hyperemic">Hyperemic</option>
                                <option value="muddy">Muddy</option>
                            </select>
                        </div>
                        <div><label>CRT (sec)</label><input type="number" name="crt" step="0.1" placeholder="2.0"></div>
                    </div>

                    <label>Notes</label>
                    <textarea name="notes" placeholder="Optional observations..."></textarea>

                    <button type="submit" class="v-btn v-btn--primary v-btn--sm" style="margin-top:12px;">Save Vitals</button>
                </form>
            </div>
        </div>

        {{-- Add Treatment --}}
        <div class="v-card v-card--compact">
            <h3 class="v-section-title collapse-toggle" onclick="toggleCollapse(this)">Add Treatment</h3>
            <div class="collapse-body">
                <form class="side-form" onsubmit="submitTreatment(event)">
                    <label>Type</label>
                    <select name="treatment_type" required>
                        <option value="injection">Injection</option>
                        <option value="procedure">Procedure</option>
                        <option value="medication">Medication</option>
                        <option value="fluid">Fluid Therapy</option>
                    </select>

                    <label>Drug / Generic</label>
                    <select name="drug_generic_id">
                        <option value="">— Select Drug —</option>
                        @foreach($drugGenerics as $dg)
                            <option value="{{ $dg->id }}">{{ $dg->name }}</option>
                        @endforeach
                    </select>

                    <label>Route</label>
                    <select name="route">
                        <option value="">— Select Route —</option>
                        @foreach($injectionRoutes as $ir)
                            <option value="{{ $ir->route_code }}">{{ $ir->route_code }} — {{ $ir->route_name }}</option>
                        @endforeach
                    </select>

                    <div class="row-2">
                        <div><label>Dose (mg)</label><input type="number" name="dose_mg" step="0.01" placeholder="10"></div>
                        <div><label>Volume (ml)</label><input type="number" name="dose_volume_ml" step="0.01" placeholder="2.5"></div>
                    </div>

                    <label>Administered At</label>
                    <input type="datetime-local" name="administered_at" value="{{ now()->format('Y-m-d\TH:i') }}" required>

                    <label>Notes</label>
                    <textarea name="notes" placeholder="Treatment details..."></textarea>

                    <button type="submit" class="v-btn v-btn--primary v-btn--sm" style="margin-top:12px;">Save Treatment</button>
                </form>
            </div>
        </div>

        {{-- Add Note --}}
        <div class="v-card v-card--compact">
            <h3 class="v-section-title collapse-toggle" onclick="toggleCollapse(this)">Add Note</h3>
            <div class="collapse-body">
                <form class="side-form" onsubmit="submitNote(event)">
                    <label>Note Type</label>
                    <select name="note_type" required>
                        <option value="progress">Progress</option>
                        <option value="handover">Handover</option>
                        <option value="observation">Observation</option>
                        <option value="plan">Plan</option>
                    </select>

                    <label>Content</label>
                    <textarea name="content" rows="4" placeholder="Enter clinical note..." required></textarea>

                    <button type="submit" class="v-btn v-btn--primary v-btn--sm" style="margin-top:12px;">Save Note</button>
                </form>
            </div>
        </div>

        {{-- Discharge --}}
        <div class="v-card v-card--compact">
            <h3 class="v-section-title" style="color:var(--danger);">Discharge Patient</h3>
            <form class="side-form" action="{{ route('vet.ipd.discharge', $admission->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to discharge this patient?')">
                @csrf
                <label>Discharge Notes</label>
                <textarea name="discharge_notes" placeholder="Reason for discharge, instructions..."></textarea>

                <label>Discharge Summary</label>
                <textarea name="discharge_summary" rows="4" placeholder="Full discharge summary..."></textarea>

                <button type="submit" class="v-btn v-btn--danger v-btn--sm" style="margin-top:12px;">Discharge</button>
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
