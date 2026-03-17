@extends('clinic.layout')

@section('content')
<div class="container-fluid">

    <a href="{{ route('clinic.ipd.index') }}" class="text-decoration-none text-muted small">&larr; Back to IPD List</a>

    {{-- Patient Banner --}}
    <div class="card mt-2 mb-3">
        <div class="card-body d-flex justify-content-between align-items-start">
            <div>
                <h4 class="fw-bold mb-1">{{ $admission->pet->name ?? '—' }}</h4>
                <p class="text-muted small mb-1">
                    {{ ucfirst($admission->pet->species ?? '') }} &middot; {{ $admission->pet->breed ?? '' }}
                    &middot; Parent: {{ $admission->pet->petParent->name ?? '—' }} &middot; {{ $admission->pet->petParent->phone ?? '' }}
                </p>
                <p class="text-muted small mb-0">
                    <strong>Admitted:</strong> {{ $admission->admission_date->format('d M Y, h:i A') }}
                    @if($admission->cage_number) &middot; <strong>Cage:</strong> {{ $admission->cage_number }} @endif
                    @if($admission->ward) &middot; <strong>Ward:</strong> {{ $admission->ward }} @endif
                    &middot; <strong>By:</strong> {{ $admission->admitted_by_name }}
                </p>
            </div>
            @php
                $badgeClass = match($admission->status) {
                    'admitted' => 'bg-success',
                    'discharged' => 'bg-secondary',
                    'deceased' => 'bg-danger',
                    default => 'bg-warning text-dark',
                };
            @endphp
            <span class="badge {{ $badgeClass }} fs-6">{{ ucfirst($admission->status) }}</span>
        </div>
    </div>

    {{-- Admission Details --}}
    @if($admission->admission_reason || $admission->tentative_diagnosis)
    <div class="card mb-3">
        <div class="card-body">
            <h6 class="fw-bold text-primary mb-2">Admission Details</h6>
            @if($admission->admission_reason)
                <p class="small mb-1"><strong>Reason:</strong> {{ $admission->admission_reason }}</p>
            @endif
            @if($admission->tentative_diagnosis)
                <p class="small mb-0"><strong>Tentative Diagnosis:</strong> {{ $admission->tentative_diagnosis }}</p>
            @endif
        </div>
    </div>
    @endif

    {{-- Discharge Info --}}
    @if($admission->status !== 'admitted')
    <div class="card mb-3 border-secondary">
        <div class="card-body">
            <h6 class="fw-bold">
                Discharged {{ $admission->discharged_at ? $admission->discharged_at->format('d M Y, h:i A') : '' }}
                @if($admission->discharged_by_name) — by {{ $admission->discharged_by_name }} @endif
            </h6>
            @if($admission->discharge_notes) <p class="small mb-1">{{ $admission->discharge_notes }}</p> @endif
            @if($admission->discharge_summary) <p class="small mb-0" style="white-space:pre-wrap;">{{ $admission->discharge_summary }}</p> @endif
        </div>
    </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        {{-- LEFT: Main Content --}}
        <div class="col-lg-8">

            {{-- Vitals --}}
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold text-primary">Vitals History</h6>
                    @if($admission->vitals->isEmpty())
                        <p class="text-muted small">No vitals recorded yet.</p>
                    @else
                        <div class="table-responsive">
                        <table class="table table-sm table-bordered small">
                            <thead class="table-light">
                                <tr>
                                    <th>Time</th><th>Temp</th><th>HR</th><th>RR</th><th>Wt</th><th>SpO2</th><th>BP</th><th>MM</th><th>CRT</th><th>Pain</th><th>By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admission->vitals as $v)
                                <tr>
                                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($v->recorded_at)->format('d/m H:i') }}</td>
                                    <td>{{ $v->temperature ?? '—' }}</td>
                                    <td>{{ $v->heart_rate ?? '—' }}</td>
                                    <td>{{ $v->respiratory_rate ?? '—' }}</td>
                                    <td>{{ $v->weight ?? '—' }}</td>
                                    <td>{{ $v->spo2 ? $v->spo2.'%' : '—' }}</td>
                                    <td>{{ $v->blood_pressure_systolic ? $v->blood_pressure_systolic.'/'.$v->blood_pressure_diastolic : '—' }}</td>
                                    <td>{{ $v->mucous_membrane ?? '—' }}</td>
                                    <td>{{ $v->crt ?? '—' }}</td>
                                    <td>{{ $v->pain_score !== null ? $v->pain_score.'/10' : '—' }}</td>
                                    <td>{{ $v->recorded_by_name ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Treatments --}}
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold text-primary">Treatments</h6>
                    @if($admission->treatments->isEmpty())
                        <p class="text-muted small">No treatments recorded yet.</p>
                    @else
                        <div class="table-responsive">
                        <table class="table table-sm small">
                            <thead class="table-light">
                                <tr><th>Time</th><th>Type</th><th>Drug / Procedure</th><th>Route</th><th>Dose</th><th>Notes</th><th>By</th></tr>
                            </thead>
                            <tbody>
                                @foreach($admission->treatments as $tx)
                                <tr>
                                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($tx->administered_at)->format('d/m H:i') }}</td>
                                    <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ ucfirst($tx->treatment_type) }}</span></td>
                                    <td>{{ $tx->drugGeneric->name ?? ($tx->priceItem->name ?? '—') }}</td>
                                    <td>{{ $tx->route ?? '—' }}</td>
                                    <td>
                                        @if($tx->dose_mg) {{ $tx->dose_mg }}mg @endif
                                        @if($tx->dose_volume_ml) {{ $tx->dose_volume_ml }}ml @endif
                                        @if(!$tx->dose_mg && !$tx->dose_volume_ml) — @endif
                                    </td>
                                    <td>{{ $tx->notes ?? '—' }}</td>
                                    <td style="font-size:11px;">{{ $tx->treated_by_type === 'vet' ? (\App\Models\Vet::find($tx->treated_by_id)?->name ?? '—') : (\App\Models\User::find($tx->treated_by_id)?->name ?? '—') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Notes --}}
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold text-primary">Clinical Notes</h6>
                    @if($admission->notes->isEmpty())
                        <p class="text-muted small">No notes yet.</p>
                    @else
                        @foreach($admission->notes as $note)
                        <div class="border rounded p-2 mb-2 bg-light">
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-info bg-opacity-10 text-info small">{{ ucfirst($note->note_type) }}</span>
                                <span class="text-muted" style="font-size:11px;">
                                    {{ $note->noted_by_type === 'vet' ? (\App\Models\Vet::find($note->noted_by_id)?->name ?? '—') : (\App\Models\User::find($note->noted_by_id)?->name ?? '—') }}
                                    &middot; {{ $note->created_at->format('d M, h:i A') }}
                                </span>
                            </div>
                            <p class="small mb-0 mt-1" style="white-space:pre-wrap;">{{ $note->content }}</p>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>

        {{-- RIGHT: Actions --}}
        @if($admission->isAdmitted())
        <div class="col-lg-4">

            {{-- Add Vitals --}}
            <div class="card mb-3">
                <div class="card-header fw-bold small">Record Vitals</div>
                <div class="card-body">
                    <form id="vitals-form" class="small">
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Recorded At</label>
                            <input type="datetime-local" class="form-control form-control-sm" name="recorded_at" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-4"><label class="form-label">Temp</label><input type="number" class="form-control form-control-sm" name="temperature" step="0.1"></div>
                            <div class="col-4"><label class="form-label">HR</label><input type="number" class="form-control form-control-sm" name="heart_rate"></div>
                            <div class="col-4"><label class="form-label">RR</label><input type="number" class="form-control form-control-sm" name="respiratory_rate"></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-4"><label class="form-label">Wt (kg)</label><input type="number" class="form-control form-control-sm" name="weight" step="0.01"></div>
                            <div class="col-4"><label class="form-label">SpO2</label><input type="number" class="form-control form-control-sm" name="spo2"></div>
                            <div class="col-4"><label class="form-label">Pain</label><input type="number" class="form-control form-control-sm" name="pain_score" min="0" max="10"></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6"><label class="form-label">BP Sys</label><input type="number" class="form-control form-control-sm" name="blood_pressure_systolic"></div>
                            <div class="col-6"><label class="form-label">BP Dia</label><input type="number" class="form-control form-control-sm" name="blood_pressure_diastolic"></div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label">MM</label>
                                <select class="form-select form-select-sm" name="mucous_membrane">
                                    <option value="">—</option>
                                    <option value="pink">Pink</option>
                                    <option value="pale">Pale</option>
                                    <option value="cyanotic">Cyanotic</option>
                                    <option value="icteric">Icteric</option>
                                    <option value="hyperemic">Hyperemic</option>
                                </select>
                            </div>
                            <div class="col-6"><label class="form-label">CRT</label><input type="number" class="form-control form-control-sm" name="crt" step="0.1"></div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control form-control-sm" name="notes" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Save Vitals</button>
                    </form>
                </div>
            </div>

            {{-- Add Note --}}
            <div class="card mb-3">
                <div class="card-header fw-bold small">Add Note</div>
                <div class="card-body">
                    <form id="note-form" class="small">
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Type</label>
                            <select class="form-select form-select-sm" name="note_type" required>
                                <option value="progress">Progress</option>
                                <option value="handover">Handover</option>
                                <option value="observation">Observation</option>
                                <option value="plan">Plan</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Content</label>
                            <textarea class="form-control form-control-sm" name="content" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Save Note</button>
                    </form>
                </div>
            </div>

            {{-- Discharge --}}
            @if(auth()->user()->hasPermission('ipd.manage'))
            <div class="card mb-3 border-danger">
                <div class="card-header fw-bold small text-danger">Discharge Patient</div>
                <div class="card-body">
                    <form action="{{ route('clinic.ipd.discharge', $admission->id) }}" method="POST" class="small" onsubmit="return confirm('Discharge this patient?')">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Discharge Notes</label>
                            <textarea class="form-control form-control-sm" name="discharge_notes" rows="2"></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Discharge Summary</label>
                            <textarea class="form-control form-control-sm" name="discharge_summary" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm w-100">Discharge</button>
                    </form>
                </div>
            </div>
            @endif

        </div>
        @endif
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
const ADM_ID = {{ $admission->id }};

function formData(form) {
    const fd = new FormData(form);
    const obj = {};
    fd.forEach((v, k) => { if (v !== '') obj[k] = v; });
    return obj;
}

document.getElementById('vitals-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    fetch(`/clinic/ipd/${ADM_ID}/vitals`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(formData(this))
    }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
        else alert(res.message || 'Error');
    }).catch(() => alert('Error saving vitals'));
});

document.getElementById('note-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    fetch(`/clinic/ipd/${ADM_ID}/notes`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify(formData(this))
    }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
        else alert(res.message || 'Error');
    }).catch(() => alert('Error saving note'));
});
</script>
@endsection
