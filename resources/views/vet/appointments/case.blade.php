@extends('layouts.vet')

@section('content')

@php
    $readOnly = $readOnly ?? false;
    $petHistory = $petHistory ?? collect();
@endphp

<style>
/* ===========================
   DESIGN SYSTEM
=========================== */
:root {
    --primary: #2563eb;
    --primary-soft: #eff6ff;
    --primary-dark: #1d4ed8;
    --success: #16a34a;
    --success-soft: #f0fdf4;
    --danger: #dc2626;
    --warning: #f59e0b;
    --warning-soft: #fffbeb;
    --cyan: #0891b2;
    --cyan-soft: #ecfeff;

    --bg-page: #f1f5f9;
    --bg-card: #ffffff;
    --bg-soft: #f8fafc;

    --text-dark: #0f172a;
    --text-normal: #334155;
    --text-muted: #64748b;
    --text-light: #94a3b8;

    --border: #e2e8f0;
    --border-light: #f1f5f9;
    --radius-xl: 16px;
    --radius-lg: 12px;
    --radius-md: 8px;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.04);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.06);
    --shadow-lg: 0 12px 40px rgba(0,0,0,0.08);
}

body { background: var(--bg-page); }

/* ===========================
   LAYOUT
=========================== */
/* Override parent layout — this page has its own full-width 2-column layout */
.v-page { max-width: 100% !important; }

.case-view-layout { width: 100%; }

.casesheet-wrapper {
    display: flex;
    gap: 24px;
    align-items: flex-start;
    width: 100%;
}

.casesheet-left { flex: 1 1 0; min-width: 0; }
.casesheet-right {
    flex: 0 0 300px;
    position: sticky;
    top: 80px;
}

@media (max-width: 1100px) {
    .casesheet-wrapper { flex-direction: column; }
    .casesheet-right { flex: auto; position: relative; top: auto; width: 100%; }
}

/* ===========================
   CARDS
=========================== */
.case-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    padding: 28px 32px;
    margin-bottom: 20px;
}

/* ===========================
   TOP HEADER BAR
=========================== */
.case-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 20px;
}

.case-header-left h1 {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0 0 4px;
}

.case-header-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.meta-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.02em;
}

.meta-chip.date {
    background: var(--bg-soft);
    color: var(--text-normal);
    border: 1px solid var(--border);
}

.meta-chip.status-scheduled { background: #dbeafe; color: #1e40af; }
.meta-chip.status-completed { background: #dcfce7; color: #166534; }
.meta-chip.status-cancelled { background: #fee2e2; color: #991b1b; }

.meta-chip.prognosis { background: var(--primary-soft); color: #1e40af; }
.meta-chip.followup { background: var(--warning-soft); color: #92400e; }

/* ===========================
   BUTTONS
=========================== */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 600;
    border-radius: var(--radius-md);
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
    box-shadow: none;
}

.btn:hover { transform: translateY(-1px); }

.btn-success { background: var(--success); color: #fff; }
.btn-success:hover { box-shadow: 0 4px 12px rgba(22,163,74,0.3); }

.btn-primary { background: var(--primary); color: #fff; }
.btn-primary:hover { box-shadow: 0 4px 12px rgba(37,99,235,0.3); }

.btn-ghost {
    background: var(--bg-soft);
    color: var(--text-normal);
    border: 1px solid var(--border);
}
.btn-ghost:hover { background: #f1f5f9; }

.btn-sm { padding: 6px 12px; font-size: 12px; }

/* ===========================
   INFO CARDS (PET PARENT + PET)
=========================== */
.info-row {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.info-panel {
    flex: 1;
    min-width: 180px;
    background: var(--bg-soft);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 18px 22px;
}

.info-panel-wide { flex: 2; min-width: 280px; }

.info-panel h4 {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-light);
    margin: 0 0 12px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 8px 20px;
}

.info-item { font-size: 13px; line-height: 1.5; color: var(--text-normal); }
.info-item strong { color: var(--text-dark); font-weight: 600; font-size: 12px; }
.info-item span { display: block; margin-top: 1px; }

/* ===========================
   CLINICAL ACTIONS GRID
=========================== */
.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 10px;
}

.action-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: var(--bg-soft);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    text-decoration: none;
    color: var(--text-normal);
    font-size: 13px;
    font-weight: 500;
    transition: all 0.15s ease;
}

.action-card:hover {
    background: var(--primary-soft);
    border-color: #bfdbfe;
    color: var(--primary);
}

.action-card .action-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.action-icon.blue { background: #dbeafe; }
.action-icon.green { background: #dcfce7; }
.action-icon.purple { background: #ede9fe; }
.action-icon.orange { background: #ffedd5; }
.action-icon.red { background: #fee2e2; }

.action-card.disabled {
    opacity: 0.5;
    pointer-events: none;
    color: var(--text-muted);
}

/* ===========================
   SECTION HEADINGS
=========================== */
.section-title {
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-light);
    margin: 0 0 14px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--border-light);
}

.section-divider {
    border: none;
    border-top: 1px solid var(--border-light);
    margin: 24px 0;
}

/* ===========================
   CASE SHEET DISPLAY
=========================== */
.cs-fields { display: flex; flex-direction: column; gap: 12px; }

.cs-field {
    padding: 10px 14px;
    background: var(--bg-soft);
    border-radius: var(--radius-md);
    border-left: 3px solid var(--primary);
}

.cs-field-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-light);
    margin-bottom: 3px;
}

.cs-field-value {
    font-size: 14px;
    line-height: 1.6;
    color: var(--text-normal);
}

/* ===========================
   TREATMENT BLOCKS
=========================== */
.treatment-block {
    border-radius: var(--radius-lg);
    padding: 14px 18px;
    margin-bottom: 10px;
}

.treatment-block.drugs { background: #f0fdf4; border: 1px solid #bbf7d0; }
.treatment-block.procedures { background: #eff6ff; border: 1px solid #bfdbfe; }

.treatment-block h5 {
    font-size: 13px;
    font-weight: 700;
    margin: 0 0 8px;
}

.treatment-block.drugs h5 { color: #166534; }
.treatment-block.procedures h5 { color: #1e40af; }

.treatment-block ul { padding-left: 18px; margin: 0; }
.treatment-block li { font-size: 13px; line-height: 1.6; margin-bottom: 3px; color: var(--text-normal); }

/* ===========================
   PET HISTORY TIMELINE
=========================== */
.history-timeline {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.history-entry {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 12px 16px;
    background: var(--bg-soft);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    transition: all 0.15s;
}

.history-entry:hover {
    border-color: #bfdbfe;
    background: #fafbff;
}

.history-date {
    flex-shrink: 0;
    text-align: center;
    min-width: 50px;
}

.history-date .day { font-size: 20px; font-weight: 700; color: var(--text-dark); line-height: 1; }
.history-date .month { font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; }

.history-body { flex: 1; min-width: 0; }
.history-body .diagnosis { font-size: 13px; color: var(--text-normal); margin-top: 2px; }
.history-body .history-link {
    font-size: 12px;
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    margin-top: 4px;
    display: inline-block;
}

/* ===========================
   DIAGNOSTIC REPORTS
=========================== */
.diag-card {
    background: var(--bg-soft);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 16px 18px;
}

.diag-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
}

.diag-type-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    background: #e0e7ff;
    color: #3730a3;
}

.diag-actions { display: flex; gap: 8px; align-items: center; }
.diag-actions a, .diag-actions button {
    font-size: 12px;
    padding: 0;
    background: none;
    border: none;
    cursor: pointer;
    font-weight: 500;
    box-shadow: none;
}
.diag-actions a { color: var(--primary); text-decoration: none; }
.diag-actions a:hover { text-decoration: underline; }
.diag-actions .del-btn { color: var(--danger); }

.diag-meta { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

.diag-file-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid var(--border-light);
    font-size: 13px;
}

.diag-file-row:last-child { border-bottom: none; }

.file-status {
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 4px;
}

.file-status.verified { background: #dcfce7; color: #166534; }
.file-status.unverified { background: #fef3c7; color: #92400e; }

.ai-summary-box {
    margin-top: 8px;
    padding: 10px 14px;
    border-radius: var(--radius-md);
    font-size: 13px;
    line-height: 1.6;
}

.ai-summary-box.verified { background: var(--cyan-soft); border: 1px solid #67e8f9; }
.ai-summary-box.unverified { background: #fff7ed; border: 1px solid #fed7aa; }

.ai-summary-box h6 {
    font-size: 12px;
    font-weight: 700;
    margin: 0 0 6px;
}

.ai-summary-box.verified h6 { color: #0f766e; }
.ai-summary-box.unverified h6 { color: #9a3412; }

.ai-summary-box ul { padding-left: 18px; margin: 0; }
.ai-summary-box li { margin-bottom: 2px; }

/* ===========================
   RIGHT PANEL (AI + RX)
=========================== */
.ai-panel {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 22px;
    box-shadow: var(--shadow-md);
    margin-bottom: 16px;
}

.ai-panel h3 {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0 0 6px;
}

.ai-panel .subtitle {
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 12px;
    line-height: 1.5;
}

#senior-vet-ai {
    background: var(--bg-soft);
    border: 1px dashed #c7d2fe;
    border-radius: var(--radius-md);
    padding: 14px;
    font-size: 13px;
    line-height: 1.7;
    white-space: pre-wrap;
    color: var(--text-normal);
    min-height: 60px;
}

.rx-panel {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 22px;
    box-shadow: var(--shadow-sm);
}

.rx-panel h3 {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0 0 10px;
}

.rx-item {
    padding: 8px 0;
    border-bottom: 1px solid var(--border-light);
    font-size: 13px;
    color: var(--text-normal);
}

.rx-item:last-child { border-bottom: none; }
.rx-item strong { color: var(--text-dark); }

/* ===========================
   COMBINED FINDINGS BANNER
=========================== */
.findings-banner {
    padding: 14px 18px;
    background: var(--cyan-soft);
    border: 1px solid #67e8f9;
    border-radius: var(--radius-lg);
    margin-bottom: 14px;
}

.findings-banner h5 {
    font-size: 13px;
    font-weight: 700;
    color: #0f766e;
    margin: 0 0 6px;
}

.findings-banner ul { padding-left: 18px; margin: 0; }
.findings-banner li { font-size: 13px; line-height: 1.6; color: #134e4a; }
.findings-banner .note { font-size: 11px; color: #155e75; margin-top: 6px; }

/* ===========================
   MODAL
=========================== */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.5);
    backdrop-filter: blur(4px);
    z-index: 9998;
    align-items: center;
    justify-content: center;
}

.modal-box {
    background: #fff;
    border-radius: var(--radius-xl);
    padding: 28px;
    width: 480px;
    max-width: 90vw;
    box-shadow: 0 25px 60px rgba(0,0,0,.2);
    position: relative;
}

.modal-close {
    position: absolute;
    top: 14px;
    right: 16px;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: var(--text-muted);
    padding: 0;
    box-shadow: none;
    line-height: 1;
}

.modal-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0 0 4px;
}

.modal-subtitle {
    font-size: 13px;
    color: var(--text-muted);
    margin-bottom: 18px;
}

.form-group { margin-bottom: 16px; }

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-normal);
    margin-bottom: 5px;
}

.form-input {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
}
</style>

<div class="case-view-layout">
<div class="casesheet-wrapper">

{{-- ============================= --}}
{{-- LEFT COLUMN --}}
{{-- ============================= --}}
<div class="casesheet-left">

    {{-- HEADER --}}
    <div class="case-card">
        <div class="case-header">
            <div class="case-header-left">
                <h1>{{ $appointment->pet->name }} — Case</h1>
                <div class="case-header-meta">
                    <span class="meta-chip date">
                        {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A') }}
                    </span>
                    @php $statusClass = match($appointment->status) {
                        'completed' => 'status-completed',
                        'cancelled' => 'status-cancelled',
                        default => 'status-scheduled',
                    }; @endphp
                    <span class="meta-chip {{ $statusClass }}">
                        {{ ucfirst($appointment->status) }}
                    </span>

                    @if($appointment->caseSheet && $appointment->caseSheet->prognosis)
                        <span class="meta-chip prognosis">
                            Prognosis: {{ ucfirst($appointment->caseSheet->prognosis) }}
                        </span>
                    @endif
                    @if($appointment->caseSheet && $appointment->caseSheet->followup_date)
                        <span class="meta-chip followup">
                            Follow-up: {{ $appointment->caseSheet->followup_date->format('d M Y') }}
                        </span>
                    @endif
                </div>
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                @if(!$readOnly)
                    <button type="button" class="btn btn-ghost btn-sm"
                            onclick="document.getElementById('followup-modal').style.display='flex'">
                        Prognosis / Follow-up
                    </button>
                @endif

                @if(!$readOnly && $appointment->status !== 'completed')
                    <form method="POST" action="{{ route('vet.clinic.appointments.complete', $appointment->id) }}" style="margin:0;">
                        @csrf
                        <button class="btn btn-success btn-sm" type="submit">Mark Completed</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- PET PARENT + PET DETAILS --}}
        <div class="info-row">
            @if(!$readOnly)
            <div class="info-panel">
                <h4>Pet Parent</h4>
                <div class="info-item"><strong>Name</strong><span>{{ $appointment->pet->petParent->name }}</span></div>
                <div class="info-item" style="margin-top:6px;"><strong>Mobile</strong><span>{{ $appointment->pet->petParent->phone }}</span></div>
            </div>
            @endif

            <div class="info-panel info-panel-wide">
                <h4>Pet Details</h4>
                <div class="info-grid">
                    <div class="info-item"><strong>Name</strong><span>{{ $appointment->pet->name }}</span></div>
                    <div class="info-item"><strong>Species</strong><span>{{ ucfirst($appointment->pet->species) }}</span></div>
                    <div class="info-item"><strong>Breed</strong><span>{{ $appointment->pet->breed ?? '-' }}</span></div>
                    <div class="info-item"><strong>Gender</strong><span>{{ ucfirst($appointment->pet->gender ?? '-') }}</span></div>
                    <div class="info-item"><strong>Age</strong><span>{{ $appointment->calculated_age_at_visit ?? '-' }}</span></div>
                    <div class="info-item"><strong>Weight</strong><span>{{ $appointment->weight ? $appointment->weight.' kg' : '-' }}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- CLINICAL ACTIONS --}}
    @if(!$readOnly)
    <div class="case-card">
        <div class="section-title">Clinical Actions</div>
        <div class="actions-grid">
            <a href="{{ route('vet.casesheet.edit', $appointment->id) }}" class="action-card">
                <div class="action-icon blue">{{ $appointment->caseSheet ? '✏️' : '📝' }}</div>
                {{ $appointment->caseSheet ? 'Edit Case Sheet' : 'Add Case Sheet' }}
            </a>

            <a href="javascript:void(0)" onclick="document.getElementById('lab-order-modal').style.display='flex'" class="action-card">
                <div class="action-icon green">🔬</div>
                Order Lab Tests
                @php $pendingLabOrders = \App\Models\LabOrder::where('appointment_id', $appointment->id)->whereNotIn('status', ['approved','cancelled'])->count(); @endphp
                @if($pendingLabOrders > 0)
                <span style="background:#ef4444;color:#fff;padding:1px 6px;border-radius:10px;font-size:10px;position:absolute;top:8px;right:8px;">{{ $pendingLabOrders }}</span>
                @endif
            </a>

            <a href="javascript:void(0)" onclick="document.getElementById('vaccination-modal').style.display='flex'" class="action-card">
                <div class="action-icon teal" style="background:#f0fdfa;color:#0d9488;">💉</div>
                Record Vaccination
            </a>

            <a href="{{ route('vet.diagnostics.create', ['appointment' => $appointment->id, 'type' => 'lab']) }}" class="action-card">
                <div class="action-icon green">🧪</div>
                Upload Lab Report
            </a>

            <a href="{{ route('vet.diagnostics.create', ['appointment' => $appointment->id, 'type' => 'radiology']) }}" class="action-card">
                <div class="action-icon purple">📷</div>
                Add Radiology / Imaging
            </a>

            <a href="{{ $appointment->prescription
                    ? route('vet.prescription.edit', $appointment->id)
                    : route('vet.prescription.create', $appointment->id) }}" class="action-card">
                <div class="action-icon orange">💊</div>
                {{ $appointment->prescription ? 'Edit Prescription' : 'Add Prescription' }}
            </a>

            @php
                $hasActiveIpd = \App\Models\IpdAdmission::where('pet_id', $appointment->pet_id)
                    ->where('clinic_id', $appointment->clinic_id)
                    ->where('status', 'admitted')
                    ->exists();
            @endphp
            @if($hasActiveIpd)
                <div class="action-card disabled">
                    <div class="action-icon red">🏥</div>
                    Already in IPD
                </div>
            @else
                <a href="{{ route('vet.ipd.admitFromCase', $appointment->id) }}" class="action-card">
                    <div class="action-icon red">🏥</div>
                    Admit to IPD
                </a>
            @endif
        </div>
    </div>
    @endif

    {{-- PET HISTORY --}}
    @if(!$readOnly && isset($petHistory))
    <div class="case-card">
        <div class="section-title">Pet History</div>

        @php
            $hasOpdHistory = $petHistory->isNotEmpty();
            $hasIpdHistory = isset($ipdHistory) && $ipdHistory->isNotEmpty();
        @endphp

        @if(!$hasOpdHistory && !$hasIpdHistory)
            <p style="color:var(--text-muted);font-size:13px;">No previous visits for this pet.</p>
        @else
            <div class="history-timeline">
                {{-- OPD Visits --}}
                @foreach($petHistory as $past)
                    <div class="history-entry">
                        <div class="history-date">
                            <div class="day">{{ \Carbon\Carbon::parse($past->scheduled_at)->format('d') }}</div>
                            <div class="month">{{ \Carbon\Carbon::parse($past->scheduled_at)->format('M') }}</div>
                        </div>
                        <div class="history-body">
                            <span style="font-size:11px;background:#dbeafe;color:#1d4ed8;padding:1px 6px;border-radius:4px;font-weight:600;">OPD</span>
                            <span style="font-size:12px;color:var(--text-muted);">
                                {{ \Carbon\Carbon::parse($past->scheduled_at)->format('Y') }}
                                — {{ ucfirst(str_replace('_', ' ', $past->status)) }}
                            </span>
                            @if($past->caseSheet)
                                <div class="diagnosis">
                                    <strong>Diagnosis:</strong> {{ $past->caseSheet->diagnosis ?? '—' }}
                                </div>
                            @endif
                            @if($past->prescription)
                                <span style="font-size:12px;color:var(--success);">Rx given</span>
                            @endif
                            @if($past->treatments->count())
                                <span style="font-size:11px;color:#6b7280;">{{ $past->treatments->count() }} treatment(s)</span>
                            @endif
                            <a href="#" onclick="openHistoryModal({{ $past->id }}); return false;" class="history-link">
                                View Details &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach

                {{-- IPD Admissions --}}
                @if($hasIpdHistory)
                @foreach($ipdHistory as $ipd)
                    <div class="history-entry">
                        <div class="history-date">
                            <div class="day">{{ \Carbon\Carbon::parse($ipd->admission_date)->format('d') }}</div>
                            <div class="month">{{ \Carbon\Carbon::parse($ipd->admission_date)->format('M') }}</div>
                        </div>
                        <div class="history-body">
                            <span style="font-size:11px;background:#fef3c7;color:#92400e;padding:1px 6px;border-radius:4px;font-weight:600;">IPD</span>
                            <span style="font-size:12px;color:var(--text-muted);">
                                {{ \Carbon\Carbon::parse($ipd->admission_date)->format('Y') }}
                                — {{ ucfirst(str_replace('_', ' ', $ipd->status)) }}
                                @if($ipd->discharged_at) · Discharged {{ \Carbon\Carbon::parse($ipd->discharged_at)->format('d M') }} @endif
                            </span>
                            @if($ipd->tentative_diagnosis)
                                <div class="diagnosis"><strong>Diagnosis:</strong> {{ $ipd->tentative_diagnosis }}</div>
                            @endif
                            @if($ipd->admission_reason)
                                <div style="font-size:12px;color:var(--text-muted);">Reason: {{ Str::limit($ipd->admission_reason, 60) }}</div>
                            @endif
                            <div style="font-size:11px;color:#6b7280;">
                                {{ $ipd->treatments->count() }} treatment(s)
                                · {{ $ipd->notes->count() }} note(s)
                            </div>
                            <a href="#" onclick="openIpdModal({{ $ipd->id }}); return false;" class="history-link">View Details &rarr;</a>
                        </div>
                    </div>
                @endforeach
                @endif
            </div>
        @endif
    </div>
    @endif

    {{-- CASE SHEET --}}
    @if($appointment->caseSheet)
    <div class="case-card">
        <div class="section-title">Case Sheet</div>

        <div style="display:flex;gap:16px;margin-bottom:16px;font-size:13px;color:var(--text-muted);">
            <span><strong style="color:var(--text-normal);">Clinic:</strong> {{ optional($appointment->clinic)->name ?? '-' }}</span>
            <span><strong style="color:var(--text-normal);">Doctor:</strong> {{ optional($appointment->vet)->name ?? '-' }}</span>
        </div>

        <div class="cs-fields">
            @foreach([
                'Presenting Complaint' => $appointment->caseSheet->presenting_complaint,
                'History' => $appointment->caseSheet->history,
                'Clinical Examination' => $appointment->caseSheet->clinical_examination,
            ] as $label => $value)
                @if($value)
                    <div class="cs-field">
                        <div class="cs-field-label">{{ $label }}</div>
                        <div class="cs-field-value">{{ $value }}</div>
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
                <div class="cs-field">
                    <div class="cs-field-label">Vitals</div>
                    <div class="cs-field-value" style="font-size:13px;">
                        {{ $vitals->map(fn($v, $k) => "$k: $v")->implode(' | ') }}
                    </div>
                </div>
            @endif

            @foreach([
                'Differentials' => $appointment->caseSheet->differentials,
                'Diagnosis' => $appointment->caseSheet->diagnosis,
                'Treatment Given' => $appointment->caseSheet->treatment_given,
                'Procedures Done' => $appointment->caseSheet->procedures_done,
                'Further Plan' => $appointment->caseSheet->further_plan,
                'Advice' => $appointment->caseSheet->advice,
            ] as $label => $value)
                @if($value)
                    <div class="cs-field">
                        <div class="cs-field-label">{{ $label }}</div>
                        <div class="cs-field-value">{{ $value }}</div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- TREATMENTS --}}
    @if($appointment->treatments && $appointment->treatments->count())
    <div class="case-card">
        <div class="section-title">Treatments</div>

        @php
            $drugTreatments = $appointment->treatments->whereNotNull('drug_generic_id');
            $procTreatments = $appointment->treatments->whereNull('drug_generic_id');
        @endphp

        @if($drugTreatments->count())
            <div class="treatment-block drugs">
                <h5>Injectable Drugs Administered</h5>
                <ul>
                    @foreach($drugTreatments as $t)
                        <li>
                            {{ optional($t->drugGeneric)->name ?? 'Unknown Drug' }}
                            @if($t->dose_mg) — {{ $t->dose_mg }} mg @endif
                            @if($t->dose_volume_ml) ({{ $t->dose_volume_ml }} ml) @endif
                            @if($t->route) &middot; {{ $t->route }} @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($procTreatments->count())
            <div class="treatment-block procedures">
                <h5>Procedures Performed</h5>
                <ul>
                    @foreach($procTreatments as $t)
                        <li>{{ optional($t->priceItem)->name ?? 'Unknown Procedure' }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    @endif

    {{-- PRESCRIPTION (LEFT COLUMN) --}}
    @if($appointment->prescription && $appointment->prescription->items->count())
    <div class="case-card">
        <div class="section-title">Prescription</div>

        @if($appointment->prescription->notes)
            <p style="font-size:13px;color:var(--text-muted);margin-bottom:12px;">
                <strong style="color:var(--text-normal);">Diagnosis / Notes:</strong> {{ $appointment->prescription->notes }}
            </p>
        @endif

        <div style="display:flex;flex-direction:column;gap:6px;">
            @foreach($appointment->prescription->items as $item)
                <div style="display:flex;align-items:flex-start;gap:12px;padding:10px 14px;background:var(--bg-soft);border:1px solid var(--border);border-radius:var(--radius-md);">
                    <div style="flex-shrink:0;width:28px;height:28px;background:#fff7ed;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:14px;">💊</div>
                    <div style="flex:1;min-width:0;">
                        <strong style="font-size:14px;color:var(--text-dark);">{{ $item->medicine }}</strong>
                        <div style="font-size:13px;color:var(--text-muted);margin-top:2px;">
                            @if($item->dosage){{ $item->dosage }}@endif
                            @if($item->frequency) · {{ $item->frequency }}@endif
                            @if($item->duration) · {{ $item->duration }}@endif
                            @if($item->instructions) — {{ $item->instructions }}@endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- LAB ORDERS --}}
    @if($appointment->labOrders->isNotEmpty())
    <div class="case-card">
        <div class="section-title">Lab Test Orders</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($appointment->labOrders as $labOrder)
                <div style="padding:10px 14px;background:#faf5ff;border:1px solid #e9d5ff;border-radius:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-weight:700;font-size:13px;color:#7c3aed;">{{ $labOrder->order_number }}</span>
                            @if($labOrder->priority === 'urgent')
                                <span style="background:#fee2e2;color:#991b1b;padding:1px 6px;border-radius:8px;font-size:9px;font-weight:700;">URGENT</span>
                            @endif
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600;
                                @if($labOrder->status === 'approved') background:#dcfce7;color:#166534;
                                @elseif($labOrder->status === 'results_uploaded') background:#d1fae5;color:#065f46;
                                @elseif($labOrder->status === 'processing') background:#e0e7ff;color:#4338ca;
                                @elseif($labOrder->status === 'retest_requested') background:#fee2e2;color:#991b1b;
                                @else background:#fef3c7;color:#92400e;
                                @endif">{{ str_replace('_', ' ', ucfirst($labOrder->status)) }}</span>
                            <a href="{{ route('vet.lab-orders.show', $labOrder) }}" style="font-size:11px;color:#7c3aed;font-weight:600;text-decoration:none;">View &rarr;</a>
                        </div>
                    </div>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        @foreach($labOrder->tests as $test)
                            <span style="background:#ede9fe;color:#6d28d9;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">{{ $test->test_name }}</span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- DIAGNOSTICS --}}
    <div class="case-card">
        <div class="section-title">Diagnostics</div>

        @php
            use App\Models\DiagnosticFile;
            $verifiedFindings = DiagnosticFile::verifiedSummariesForAppointment($appointment->id);
        @endphp

        @if($verifiedFindings)
            <div class="findings-banner">
                <h5>Combined Verified Diagnostic Findings</h5>
                <ul>
                    @foreach(explode("\n", $verifiedFindings) as $line)
                        <li>{{ ltrim($line, '- ') }}</li>
                    @endforeach
                </ul>
                <p class="note">Based only on human-verified diagnostic files.</p>
            </div>
        @endif

        @if($appointment->diagnosticReports->isEmpty())
            <p style="color:var(--text-muted);font-size:13px;">No lab or radiology reports added for this visit.</p>
        @else
            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach($appointment->diagnosticReports as $report)
                    <div class="diag-card">
                        <div class="diag-header">
                            <div>
                                <span class="diag-type-badge">{{ strtoupper($report->type) }}</span>
                                @if($report->title)
                                    <strong style="font-size:14px;color:var(--text-dark);margin-left:8px;">{{ $report->title }}</strong>
                                @endif
                                <div class="diag-meta">
                                    @if($report->report_date) {{ $report->report_date->format('d M Y') }} @endif
                                    @if($report->lab_or_center) &middot; {{ $report->lab_or_center }} @endif
                                </div>
                            </div>

                            @if($appointment->status !== 'completed')
                                <div class="diag-actions">
                                    <a href="{{ route('vet.diagnostics.edit', $report->id) }}">Edit</a>
                                    <form method="POST" action="{{ route('vet.diagnostics.destroy', $report->id) }}"
                                          style="display:inline;margin:0;" onsubmit="return confirm('Delete this diagnostic report?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="del-btn">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        @if($report->files->isNotEmpty())
                            <div style="margin-top:12px;">
                                @foreach($report->files as $file)
                                    <div class="diag-file-row">
                                        <span style="flex:1;">{{ $file->display_name ?: $file->original_filename }}</span>

                                        <span class="file-status {{ $file->status === 'human_verified' ? 'verified' : 'unverified' }}">
                                            {{ $file->status === 'human_verified' ? 'Verified' : 'Unverified' }}
                                        </span>

                                        <button type="button"
                                                onclick="openFloatingReport('{{ route('vet.diagnostics.files.embed', $file->id) }}')"
                                                style="background:none;border:none;padding:0;font-size:12px;cursor:pointer;color:var(--primary);font-weight:600;box-shadow:none;">
                                            View
                                        </button>
                                        <a href="{{ route('vet.diagnostics.download', $file->id) }}"
                                           style="font-size:12px;color:var(--primary);text-decoration:none;font-weight:600;">Download</a>
                                    </div>

                                    @if($file->ai_summary)
                                        <div class="ai-summary-box {{ $file->status === 'human_verified' ? 'verified' : 'unverified' }}">
                                            <h6>{{ $file->status === 'human_verified' ? 'Findings (Verified)' : 'AI Findings (Not Verified)' }}</h6>
                                            <ul>
                                                @foreach(preg_split('/\r\n|\r|\n|•/', $file->ai_summary) as $line)
                                                    @if(trim($line))
                                                        <li>{{ trim($line) }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                            @if($file->status !== 'human_verified')
                                                <p style="margin-top:6px;font-size:11px;color:#9a3412;font-style:italic;">
                                                    AI-generated findings — requires clinician verification.
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>{{-- end .casesheet-left --}}


{{-- ============================= --}}
{{-- RIGHT COLUMN --}}
{{-- ============================= --}}
<div class="casesheet-right">

    {{-- AI PANEL --}}
    <div class="ai-panel">
        <h3>Senior Vet Clinical Decision Support (AI)</h3>
        <p class="subtitle">
            AI-assisted guidance to support clinical reasoning and learning.
            This is <strong>not</strong> a diagnosis, prescription, or medical record.
        </p>

        <div id="senior-vet-ai">No guidance generated yet.</div>

        @if(!$readOnly)
            <button class="btn btn-primary" style="margin-top:12px;width:100%;"
                    onclick="generateSeniorVetSupport({{ $appointment->id }})">
                Get Senior Vet Guidance
            </button>
        @endif
    </div>

    {{-- PRESCRIPTION --}}
    @if($appointment->prescription)
        <div class="rx-panel">
            <h3>Prescription</h3>
            @if($appointment->prescription->notes)
                <p style="font-size:13px;color:var(--text-muted);margin-bottom:10px;">
                    {{ $appointment->prescription->notes }}
                </p>
            @endif
            @foreach($appointment->prescription->items as $item)
                <div class="rx-item" style="display:flex;align-items:flex-start;gap:10px;padding:8px 12px;background:var(--bg-soft);border:1px solid var(--border);border-radius:var(--radius-md);margin-bottom:6px;">
                    <div style="flex:1;min-width:0;">
                        <strong style="font-size:14px;">{{ $item->medicine }}</strong>
                        <div style="font-size:13px;color:var(--text-muted);margin-top:2px;">
                            @if($item->dosage){{ $item->dosage }}@endif
                            @if($item->frequency) · {{ $item->frequency }}@endif
                            @if($item->duration) · {{ $item->duration }}@endif
                            @if($item->instructions) — {{ $item->instructions }}@endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>{{-- end .casesheet-right --}}

</div>{{-- end .casesheet-wrapper --}}
</div>

{{-- ============================= --}}
{{-- MODALS --}}
{{-- ============================= --}}

{{-- PROGNOSIS & FOLLOW-UP MODAL --}}
<div id="followup-modal" class="modal-overlay">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="document.getElementById('followup-modal').style.display='none'">&times;</button>

        <div class="modal-title">Prognosis &amp; Follow-up</div>
        <p class="modal-subtitle">Set the clinical prognosis and schedule a follow-up visit.</p>

        <div class="form-group">
            <label class="form-label">Prognosis</label>
            <select id="fu-prognosis" class="form-input">
                <option value="">-- Select --</option>
                <option value="good" {{ optional($appointment->caseSheet)->prognosis === 'good' ? 'selected' : '' }}>Good</option>
                <option value="guarded" {{ optional($appointment->caseSheet)->prognosis === 'guarded' ? 'selected' : '' }}>Guarded</option>
                <option value="poor" {{ optional($appointment->caseSheet)->prognosis === 'poor' ? 'selected' : '' }}>Poor</option>
                <option value="grave" {{ optional($appointment->caseSheet)->prognosis === 'grave' ? 'selected' : '' }}>Grave</option>
                <option value="hopeless" {{ optional($appointment->caseSheet)->prognosis === 'hopeless' ? 'selected' : '' }}>Hopeless</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Follow-up Date</label>
            <input type="date" id="fu-date" class="form-input"
                   value="{{ optional($appointment->caseSheet)->followup_date ? $appointment->caseSheet->followup_date->format('Y-m-d') : '' }}">
        </div>

        <div class="form-group">
            <label class="form-label">Reason for Follow-up</label>
            <textarea id="fu-reason" rows="3" class="form-input" style="resize:vertical;"
                      placeholder="e.g. Recheck wound, Blood test review...">{{ optional($appointment->caseSheet)->followup_reason ?? '' }}</textarea>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:6px;">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('followup-modal').style.display='none'">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="saveFollowup()">Save</button>
        </div>
    </div>
</div>

{{-- HISTORY MODAL --}}
<div id="history-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.5);backdrop-filter:blur(4px);z-index:9999;">
    <div style="background:#fff;width:800px;max-width:95%;margin:40px auto;padding:28px;border-radius:16px;max-height:85vh;overflow:auto;box-shadow:0 25px 60px rgba(0,0,0,.2);">
        <h3 style="font-size:18px;font-weight:700;color:var(--text-dark);margin:0 0 12px;">Past Case Details</h3>
        <hr style="border:none;border-top:1px solid var(--border);margin:0 0 16px;">
        <div id="history-modal-content">Loading...</div>
        <div style="text-align:right;margin-top:16px;">
            <button class="btn btn-ghost" onclick="closeHistoryModal()">Close</button>
        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- FLOATING REPORT VIEWER --}}
{{-- ============================= --}}
<div id="floating-report" style="display:none;position:fixed;top:120px;left:120px;width:480px;height:520px;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 25px 60px rgba(0,0,0,0.35);z-index:9999;overflow:hidden;">
    <div id="floating-report-header" style="height:42px;background:var(--bg-soft);border-bottom:1px solid var(--border);cursor:move;display:flex;align-items:center;justify-content:space-between;padding:0 14px;font-size:14px;font-weight:600;">
        Diagnostic Report
        <button type="button" onclick="closeFloatingReport()" style="background:#fff;border:1px solid var(--border);color:var(--text-dark);font-size:14px;line-height:1;width:28px;height:28px;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;box-shadow:none;">✕</button>
    </div>
    <iframe id="floating-report-frame" style="width:100%;height:calc(100% - 42px);border:none;"></iframe>
</div>

{{-- ============================= --}}
{{-- SCRIPTS --}}
{{-- ============================= --}}

<script>
// Drag floating report
(function () {
    const el = document.getElementById('floating-report');
    const hd = document.getElementById('floating-report-header');
    if (!el || !hd) return;
    let dragging = false, ox = 0, oy = 0;
    hd.addEventListener('mousedown', e => { dragging = true; ox = e.clientX - el.offsetLeft; oy = e.clientY - el.offsetTop; el.style.zIndex = 10000; document.body.style.userSelect = 'none'; });
    document.addEventListener('mousemove', e => { if (!dragging) return; el.style.left = (e.clientX - ox) + 'px'; el.style.top = (e.clientY - oy) + 'px'; });
    document.addEventListener('mouseup', () => { dragging = false; document.body.style.userSelect = ''; });
})();

function openFloatingReport(url) {
    const box = document.getElementById('floating-report');
    const frame = document.getElementById('floating-report-frame');
    if (!box || !frame) return;
    frame.src = url;
    box.style.display = 'block';
}

function closeFloatingReport() {
    document.getElementById('floating-report').style.display = 'none';
    document.getElementById('floating-report-frame').src = '';
}

function openHistoryModal(id) {
    document.getElementById('history-modal').style.display = 'block';
    document.getElementById('history-modal-content').innerHTML = 'Loading...';
    fetch(`/vet/appointments/${id}/history-view`)
        .then(r => r.text())
        .then(html => { document.getElementById('history-modal-content').innerHTML = html; })
        .catch(() => { document.getElementById('history-modal-content').innerHTML = 'Failed to load history.'; });
}

function closeHistoryModal() {
    document.getElementById('history-modal').style.display = 'none';
}

function openIpdModal(id) {
    document.getElementById('history-modal').style.display = 'block';
    document.getElementById('history-modal-content').innerHTML = 'Loading IPD details...';
    fetch(`/vet/ipd/${id}/history-view`)
        .then(r => {
            if (!r.ok) throw new Error('Not found');
            return r.text();
        })
        .then(html => { document.getElementById('history-modal-content').innerHTML = html; })
        .catch(() => { document.getElementById('history-modal-content').innerHTML = 'Failed to load IPD details.'; });
}

function saveFollowup() {
    const payload = {
        prognosis: document.getElementById('fu-prognosis').value || null,
        followup_date: document.getElementById('fu-date').value || null,
        followup_reason: document.getElementById('fu-reason').value || null,
    };
    fetch('/vet/appointments/{{ $appointment->id }}/followup', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { document.getElementById('followup-modal').style.display = 'none'; location.reload(); }
        else { alert(data.message || 'Failed to save.'); }
    })
    .catch(() => alert('Network error. Please try again.'));
}

window.aiContext = {
    pet: {
        name: "{{ ucfirst($appointment->pet->name) }}",
        species: "{{ ucfirst($appointment->pet->species) }}",
        breed: "{{ ucfirst($appointment->pet->breed ?? '') }}",
        gender: "{{ ucfirst($appointment->pet->gender ?? '') }}",
        age: "{{ $appointment->calculated_age_at_visit ?? '' }}",
        weight: "{{ $appointment->weight ?? '' }}"
    },
    appointment: {
        date: "{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A') }}",
        status: "{{ ucfirst($appointment->status) }}"
    }
};

async function generateSeniorVetSupport(appointmentId) {
    const box = document.getElementById('senior-vet-ai');
    if (!box) return;
    box.innerText = 'Senior vet reviewing the case...';
    try {
        const res = await fetch(`/vet/ai/senior-support/${appointmentId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        });
        if (res.status === 402) {
            const d = await res.json();
            box.innerHTML = '<span style="color:#dc2626;">' + d.error + '</span><br><a href="' + d.purchase_url + '" style="color:#2563eb;">Purchase AI Credits</a>';
            return;
        }
        if (!res.ok) throw new Error('Request failed');
        const data = await res.json();
        box.innerText = data.guidance || 'No guidance generated.';
        if (data.credits_remaining !== undefined) updateCreditBadge(data.credits_remaining);
    } catch (e) {
        box.innerText = 'Failed to generate senior vet guidance.';
    }
}

function updateCreditBadge(balance) {
    const badges = document.querySelectorAll('.ai-credit-balance');
    badges.forEach(b => b.textContent = balance);
}
</script>

{{-- ============================================
     LAB ORDER MODAL
     ============================================ --}}
@if(!$readOnly)
<div id="lab-order-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;justify-content:center;align-items:flex-start;padding-top:60px;overflow-y:auto;">
<div style="background:#fff;border-radius:14px;padding:24px;max-width:700px;width:95%;max-height:80vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="margin:0;font-size:18px;font-weight:700;">Order Lab Tests</h3>
        <button onclick="document.getElementById('lab-order-modal').style.display='none'" style="background:none;border:none;font-size:24px;cursor:pointer;color:#6b7280;">&times;</button>
    </div>

    {{-- Existing lab orders for this appointment --}}
    @php $existingOrders = \App\Models\LabOrder::where('appointment_id', $appointment->id)->with('tests')->latest()->get(); @endphp
    @if($existingOrders->count())
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px;margin-bottom:14px;">
        <div style="font-size:12px;font-weight:600;color:#1e40af;margin-bottom:6px;">Existing Orders ({{ $existingOrders->count() }})</div>
        @foreach($existingOrders as $order)
        <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;padding:4px 0;{{ !$loop->last ? 'border-bottom:1px solid #dbeafe;' : '' }}">
            <span><strong>{{ $order->order_number }}</strong> — {{ $order->tests->pluck('test_name')->implode(', ') }}</span>
            <span style="background:{{ $order->status === 'approved' ? '#dcfce7' : '#fef3c7' }};color:{{ $order->status === 'approved' ? '#166534' : '#92400e' }};padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Search tests --}}
    <div style="margin-bottom:12px;">
        <input type="text" id="modal-lab-search" placeholder="Search tests (e.g., CBC, LFT, Thyroid)..." style="width:100%;padding:12px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" autocomplete="off">
        <div id="modal-lab-dropdown" style="display:none;position:absolute;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 10px 30px rgba(0,0,0,0.12);max-height:300px;overflow-y:auto;z-index:10;width:calc(100% - 48px);margin-top:2px;"></div>
    </div>

    {{-- Selected tests pills --}}
    <div id="modal-lab-pills" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px;min-height:20px;"></div>

    {{-- Priority & Notes --}}
    <div style="display:flex;gap:10px;margin-bottom:14px;">
        <select id="modal-lab-priority" style="padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
            <option value="routine">Routine</option>
            <option value="urgent">Urgent</option>
            <option value="stat">STAT</option>
        </select>
        <input type="text" id="modal-lab-notes" placeholder="Notes for lab (optional)" style="flex:1;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
    </div>

    <button type="button" onclick="submitModalLabOrder()" id="modal-lab-order-btn" disabled style="background:#7c3aed;color:#fff;border:none;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;width:100%;">
        Order Lab Tests
    </button>
</div>
</div>

<script>
const modalLabPills = [];

document.getElementById('modal-lab-search').addEventListener('input', function() {
    const q = this.value.trim();
    const dropdown = document.getElementById('modal-lab-dropdown');
    if (q.length < 2) { dropdown.style.display = 'none'; return; }

    fetch(`{{ route('vet.lab-orders.available-tests') }}?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(data => {
            const tests = data.tests || [];
            const vetCanSelectLab = data.vet_can_select_lab || false;
            if (!tests.length) {
                dropdown.innerHTML = '<div style="padding:12px;font-size:13px;color:#9ca3af;">No tests found.</div>';
            } else {
                dropdown.innerHTML = tests.map(t => {
                    const labs = t.labs || [];
                    return `<div style="border-bottom:1px solid #f3f4f6;">
                        <div style="padding:8px 12px;font-weight:600;font-size:13px;color:#374151;background:#f9fafb;">${t.name} <span style="color:#9ca3af;font-size:11px;">${t.code}</span></div>
                        ${labs.map(l => {
                            const badge = l.type === 'in_house'
                                ? '<span style="background:#7c3aed;color:#fff;padding:1px 5px;border-radius:4px;font-size:9px;font-weight:600;">IN</span>'
                                : '<span style="background:#f59e0b;color:#fff;padding:1px 5px;border-radius:4px;font-size:9px;font-weight:600;">EXT</span>';
                            const params = l.parameters && l.parameters.length ? `<div style="font-size:10px;color:#9ca3af;margin-top:1px;">${l.parameters.join(', ')}</div>` : '';
                            return `<div onclick="addModalLabTest(${l.id}, '${t.name.replace(/'/g,"\\'")}', '${t.code}', '${l.type}', ${l.lab_id || 'null'}, ${l.price || 0}, '${(l.lab_name||'').replace(/'/g,"\\'")}')"
                                style="padding:8px 12px 8px 24px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;transition:background .1s;"
                                onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background=''">
                                <div>${badge} ${l.lab_name}${params}</div>
                                <span style="font-weight:600;color:#111;font-size:13px;">₹${l.price}</span>
                            </div>`;
                        }).join('')}
                    </div>`;
                }).join('');
            }
            dropdown.style.display = 'block';
        });
});

function addModalLabTest(id, name, code, type, labId, price, labName) {
    if (modalLabPills.some(t => t.code === code && t.lab_id == labId)) return;
    modalLabPills.push({ id, name, code, type, lab_id: labId, price, lab_name: labName });
    document.getElementById('modal-lab-search').value = '';
    document.getElementById('modal-lab-dropdown').style.display = 'none';
    renderModalPills();
}

function removeModalLabTest(i) {
    modalLabPills.splice(i, 1);
    renderModalPills();
}

function renderModalPills() {
    const c = document.getElementById('modal-lab-pills');
    c.innerHTML = modalLabPills.map((t, i) => {
        const isExt = t.type === 'external';
        const bg = isExt ? '#fef3c7' : '#faf5ff';
        const border = isExt ? '#fde68a' : '#e9d5ff';
        const color = isExt ? '#92400e' : '#7c3aed';
        const badge = isExt ? `<span style="font-size:9px;background:#f59e0b;color:#fff;padding:1px 5px;border-radius:4px;">EXT · ${t.lab_name}</span>` : '<span style="font-size:9px;background:#7c3aed;color:#fff;padding:1px 5px;border-radius:4px;">IN</span>';
        return `<span style="display:inline-flex;align-items:center;gap:6px;padding:6px 10px 6px 12px;background:${bg};border:1px solid ${border};border-radius:20px;font-size:13px;color:${color};">
            <strong>${t.name}</strong> ${badge} ₹${t.price}
            <button type="button" onclick="removeModalLabTest(${i})" style="background:rgba(0,0,0,0.08);border:none;border-radius:50%;width:18px;height:18px;font-size:12px;cursor:pointer;color:#6b7280;display:flex;align-items:center;justify-content:center;">&times;</button>
        </span>`;
    }).join('');
    document.getElementById('modal-lab-order-btn').disabled = modalLabPills.length === 0;
}

function submitModalLabOrder() {
    if (modalLabPills.length === 0) return;
    const btn = document.getElementById('modal-lab-order-btn');
    btn.disabled = true;
    btn.textContent = 'Ordering...';

    // Group by lab
    const groups = {};
    modalLabPills.forEach(t => {
        const key = t.type === 'external' && t.lab_id ? 'ext_' + t.lab_id : 'in_house';
        if (!groups[key]) groups[key] = { lab_id: t.type === 'external' ? t.lab_id : null, tests: [] };
        groups[key].tests.push({
            name: t.name, catalog_id: t.type === 'in_house' ? t.id : null,
            external_test_id: t.type === 'external' ? t.id : null,
            external_lab_id: t.type === 'external' ? t.lab_id : null,
            type: t.type, price: t.price,
        });
    });

    const promises = Object.values(groups).map(group =>
        fetch('{{ route("vet.lab-orders.store", $appointment) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({
                tests: group.tests, lab_id: group.lab_id,
                priority: document.getElementById('modal-lab-priority').value,
                notes: document.getElementById('modal-lab-notes').value,
            })
        }).then(r => r.ok ? r.json() : r.text().then(t => ({ message: 'Error ' + r.status })))
    );

    Promise.all(promises).then(results => {
        const ok = results.filter(r => r.success);
        if (ok.length) {
            modalLabPills.length = 0;
            renderModalPills();
            alert(ok.length === 1 ? 'Lab order created: ' + ok[0].order.order_number : ok.length + ' lab orders created');
            location.reload();
        } else {
            alert('Error: ' + (results[0]?.message || 'Unknown error'));
            btn.disabled = false;
            btn.textContent = 'Order Lab Tests';
        }
    }).catch(e => {
        alert('Failed: ' + e.message);
        btn.disabled = false;
        btn.textContent = 'Order Lab Tests';
    });
}
</script>

{{-- ============================================
     VACCINATION MODAL
     ============================================ --}}
<div id="vaccination-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;justify-content:center;align-items:flex-start;padding-top:60px;">
<div style="background:#fff;border-radius:14px;padding:24px;max-width:600px;width:95%;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="margin:0;font-size:18px;font-weight:700;">Record Vaccination</h3>
        <button onclick="document.getElementById('vaccination-modal').style.display='none'" style="background:none;border:none;font-size:24px;cursor:pointer;color:#6b7280;">&times;</button>
    </div>

    <form method="POST" action="{{ route('vet.vaccination.store', $appointment) }}">
        @csrf
        <div style="margin-bottom:12px;">
            <label style="font-size:12px;font-weight:600;color:#374151;">Search Vaccine *</label>
            <input type="text" id="vacc-search" placeholder="Search vaccine (e.g., Rabies, DHPP, Tricat)..." style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;" autocomplete="off">
            <input type="hidden" name="vaccine_generic_id" id="vacc-generic-id">
            <div id="vacc-dropdown" style="display:none;position:absolute;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 8px 20px rgba(0,0,0,0.1);max-height:200px;overflow-y:auto;z-index:10;"></div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
            <div>
                <label style="font-size:12px;font-weight:600;color:#374151;">Brand</label>
                <select name="brand_id" id="vacc-brand" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                    <option value="">Select brand</option>
                </select>
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:#374151;">Dose #</label>
                <select name="dose_number" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                    <option value="1st">1st</option>
                    <option value="2nd">2nd</option>
                    <option value="3rd">3rd</option>
                    <option value="Booster">Booster</option>
                    <option value="Annual">Annual</option>
                </select>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:12px;">
            <div>
                <label style="font-size:12px;font-weight:600;color:#374151;">Batch #</label>
                <input type="text" name="batch_number" placeholder="From vial" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:#374151;">Route</label>
                <select name="route" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                    <option value="SC">SC (Subcutaneous)</option>
                    <option value="IM">IM (Intramuscular)</option>
                    <option value="IN">IN (Intranasal)</option>
                    <option value="Oral">Oral</option>
                </select>
            </div>
            <div>
                <label style="font-size:12px;font-weight:600;color:#374151;">Date</label>
                <input type="date" name="administered_date" value="{{ date('Y-m-d') }}" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <label style="font-size:12px;font-weight:600;color:#374151;">Next Due Date</label>
            <input type="date" name="next_due_date" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
        </div>

        <button type="submit" style="background:#0d9488;color:#fff;border:none;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;width:100%;">
            Record Vaccination
        </button>
    </form>
</div>
</div>

<script>
// Vaccine search
document.getElementById('vacc-search').addEventListener('input', function() {
    const q = this.value.trim();
    const dd = document.getElementById('vacc-dropdown');
    if (q.length < 2) { dd.style.display = 'none'; return; }

    fetch(`/vet/search-vaccines?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(vaccines => {
            if (!vaccines.length) { dd.innerHTML = '<div style="padding:10px;color:#9ca3af;font-size:13px;">No vaccines found</div>'; }
            else {
                dd.innerHTML = vaccines.map(v =>
                    `<div onclick="selectVaccine(${v.id}, '${v.name.replace(/'/g,"\\'")}')" style="padding:8px 12px;cursor:pointer;font-size:13px;border-bottom:1px solid #f3f4f6;" onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background=''">
                        <strong>${v.name}</strong>
                        <div style="font-size:11px;color:#6b7280;">${v.species || ''}</div>
                    </div>`
                ).join('');
            }
            dd.style.display = 'block';
        });
});

function selectVaccine(id, name) {
    document.getElementById('vacc-search').value = name;
    document.getElementById('vacc-generic-id').value = id;
    document.getElementById('vacc-dropdown').style.display = 'none';

    // Load brands for this vaccine
    fetch(`/vet/search-vaccine-brands?generic_id=${id}`)
        .then(r => r.json())
        .then(brands => {
            const sel = document.getElementById('vacc-brand');
            sel.innerHTML = '<option value="">Select brand</option>';
            brands.forEach(b => {
                sel.innerHTML += `<option value="${b.id}">${b.brand_name} (${b.manufacturer || ''})</option>`;
            });
        });
}
</script>
@endif

@endsection
