@extends('layouts.vet')

@section('content')

@php
    $readOnly = $readOnly ?? false;
@endphp

@php
    $petHistory = $petHistory ?? collect();
@endphp

<style>

/* ===========================
   DESIGN SYSTEM (CLINICAL UI)
=========================== */
:root {
    --primary: #2563eb;
    --primary-soft: #eff6ff;
    --success: #16a34a;
    --danger: #dc2626;
    --warning: #f59e0b;

    --bg-page: #f4f6f9;
    --bg-card: #ffffff;
    --bg-soft: #f9fafb;

    --text-dark: #111827;
    --text-normal: #374151;
    --text-muted: #6b7280;

    --border: #e5e7eb;
    --radius-lg: 14px;
    --radius-md: 10px;
}

/* PAGE */
body {
    background: var(--bg-page);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI",
                 Roboto, Helvetica, Arial, sans-serif;
    color: var(--text-normal);
}

/* ===========================
   MAIN CASE CARD
=========================== */
.card {
    max-width: 1000px;
    margin: 40px auto;
    padding: 36px 40px;
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    box-shadow: 0 20px 50px rgba(0,0,0,0.06);
}

/* HEADINGS */
h2 {
    font-size: 26px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 28px;
    color: var(--text-dark);
}

h3 {
    font-size: 18px;
    font-weight: 600;
    margin: 28px 0 14px;
    color: var(--primary);
}

/* TEXT */
p {
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 6px;
}

strong {
    color: var(--text-dark);
}

/* SEPARATORS */
hr {
    border: none;
    border-top: 1px solid var(--border);
    margin: 26px 0;
}

/* ===========================
   INFO BLOCKS
=========================== */
.section,
.info-box {
    background: var(--bg-soft);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 18px 20px;
    margin-bottom: 20px;
}

/* LISTS (CASE SHEET, RX, DIAGNOSTICS) */
ul {
    padding-left: 18px;
    margin-top: 8px;
}

li {
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 6px;
}

/* ===========================
   ACTION LINKS
=========================== */
.action-link {
    color: var(--primary);
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
}

.action-link:hover {
    text-decoration: underline;
}

/* ===========================
   BUTTONS
=========================== */
button {
    padding: 10px 18px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    background: var(--primary);
    color: #fff;
    transition: 0.2s ease;
}

button:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(37,99,235,0.3);
}

button.complete {
    background: var(--success);
}

button.complete:hover {
    box-shadow: 0 6px 14px rgba(22,163,74,0.35);
}

/* ===========================
   CASE HISTORY CARDS
=========================== */
.history-card {
    background: var(--bg-soft);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 14px 16px;
}

.history-card strong {
    font-size: 14px;
}

.history-card span {
    font-size: 13px;
    color: var(--text-muted);
}

/* ===========================
   DIAGNOSTIC REPORTS
=========================== */
.diagnostic-card {
    background: var(--bg-soft);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 16px;
}

.diagnostic-meta {
    font-size: 13px;
    color: var(--text-muted);
    margin-top: 4px;
}

/* ===========================
   AI PANEL (RIGHT COLUMN)
=========================== */
.casesheet-right {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 20px 22px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.casesheet-right h3 {
    margin-top: 0;
}

#senior-vet-ai {
    background: #f8fafc;
    border: 1px dashed #c7d2fe;
    border-radius: 10px;
    padding: 14px;
    font-size: 14px;
    line-height: 1.6;
    white-space: pre-wrap;
    color: #1e293b;
}

/* ===========================
   MODAL (PAST HISTORY)
=========================== */
#history-modal > div {
    box-shadow: 0 20px 50px rgba(0,0,0,0.25);
}

/* ===========================
   RESPONSIVE SAFETY
=========================== */
@media (max-width: 1024px) {
    .card {
        padding: 26px;
    }

    .casesheet-right {
        margin-top: 20px;
    }
}

/* ================================
   FIX: TWO COLUMN CASE + AI LAYOUT
================================ */

/* Outer layout */
.case-view-layout {
    width: 100%;
}

/* Flex wrapper */
.casesheet-wrapper {
    display: flex;
    gap: 32px;
    align-items: flex-start;

    max-width: 1400px;
    margin: 40px auto;
    padding: 0 24px;
}

/* LEFT: Case Sheet (BIG) */
.casesheet-left {
    flex: 0 0 72%;
}

.casesheet-left .card {
    max-width: 100%;
    margin: 0;
}

/* RIGHT: AI Panel (SMALL) */
.casesheet-right {
    flex: 0 0 28%;
    position: sticky;
    top: 90px; /* stays visible while scrolling */

    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}

/* AI text box */
#senior-vet-ai {
    background: #f8fafc;
    border: 1px dashed #c7d2fe;
    border-radius: 10px;
    padding: 14px;
    font-size: 14px;
    line-height: 1.6;
    margin-top: 10px;
}

/* ================================
   MOBILE: STACK CLEANLY
================================ */
@media (max-width: 1024px) {
    .casesheet-wrapper {
        flex-direction: column;
    }

    .casesheet-left,
    .casesheet-right {
        flex: 100%;
    }

    .casesheet-right {
        position: relative;
        top: auto;
    }
}
.casesheet-wrapper {
    margin-left: calc((100vw - 1400px) / 2 - 40px);
    margin-right: auto;
}

</style>

<div class="case-view-layout">
    <div class="casesheet-wrapper">
        <!-- existing left + right columns -->

    <!-- LEFT: CASE DATA -->
    <div class="casesheet-left">
        <div class="card">

    <h2>Case Details</h2>

    {{-- Mark complete (ONLY for active editable appointments) --}}
    @if(!$readOnly && $appointment->status !== 'completed')
        <form method="POST"
              action="{{ route('vet.clinic.appointments.complete', $appointment->id) }}"
              style="margin-bottom:14px;">
            @csrf
            <button class="complete">
                Mark Appointment as Completed
            </button>
        </form>
    @endif

    <p>
        <strong>Appointment Date:</strong>
        {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A') }}
    </p>

    <p>
        <strong>Status:</strong> {{ ucfirst($appointment->status) }}
    </p>

    <hr>

    {{-- Pet Parent (ONLY FOR ACTIVE APPOINTMENTS) --}}
    @if(!$readOnly)
        <h3>Pet Parent</h3>
        <p><strong>Name:</strong> {{ $appointment->pet->petParent->name }}</p>
        <p><strong>Mobile:</strong> {{ $appointment->pet->petParent->phone }}</p>

        <hr>
    @endif

    {{-- Pet --}}
    <h3>Pet Details</h3>

    <p><strong>Name:</strong> {{ $appointment->pet->name }}</p>
    <p><strong>Species:</strong> {{ ucfirst($appointment->pet->species) }}</p>
    <p><strong>Breed:</strong> {{ $appointment->pet->breed ?? '-' }}</p>
    <p><strong>Gender:</strong> {{ ucfirst($appointment->pet->gender ?? '-') }}</p>
    <p>
        <strong>Age:</strong>
        {{ $appointment->calculated_age_at_visit ?? '-' }}
    </p>
    <p><strong>Weight:</strong>
        {{ $appointment->weight ? $appointment->weight.' kg' : '-' }}
    </p>

    

    <hr>

            {{-- Pet History (ONLY for active scheduled cases) --}}
        @if(!$readOnly && isset($petHistory))
            

            <h3 style="color:#2563eb;">Pet History</h3>

            @if($petHistory->isEmpty())
                <p style="color:#6b7280;font-size:14px;">
                    No previous visits for this pet.
                </p>
            @else
                <div style="display:flex;flex-direction:column;gap:16px;">
                    @foreach($petHistory as $past)
                        <div style="
                            background:#f9fafb;
                            border:1px solid #e5e7eb;
                            border-radius:8px;
                            padding:14px 16px;
                        ">
                            <strong>
                                {{ \Carbon\Carbon::parse($past->scheduled_at)->format('d M Y') }}
                            </strong>
                            <span style="color:#6b7280;font-size:13px;">
                                — {{ ucfirst($past->status) }}
                            </span>

                            @if($past->caseSheet)
                                <p style="margin-top:6px;font-size:14px;">
                                    <strong>Diagnosis:</strong>
                                    {{ $past->caseSheet->diagnosis ?? '—' }}
                                </p>
                            @endif

                            @if($past->prescription)
                                <p style="font-size:13px;color:#374151;">
                                    Prescription given
                                </p>
                            @endif

                            <a href="#"
                            onclick="openHistoryModal({{ $past->id }}); return false;"
                            style="color:#2563eb;font-size:13px;">
                                View Details →
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    

    {{-- Clinical Actions (ONLY FOR ACTIVE APPOINTMENTS) --}}
    @if(!$readOnly)
        <h3>Clinical Actions</h3>

        <ul>
            <li>
                <a class="action-link"
                   href="{{ route('vet.casesheet.edit', $appointment->id) }}">
                    📝 {{ $appointment->caseSheet ? 'Edit Case Sheet' : 'Add Case Sheet' }}
                </a>
            </li>

            <li>
                <a class="action-link"
                href="{{ route('vet.diagnostics.create', ['appointment' => $appointment->id, 'type' => 'lab']) }}">
                    ➕ Add Lab Report
                </a>
            </li>

            <li>
                <a class="action-link"
                href="{{ route('vet.diagnostics.create', ['appointment' => $appointment->id, 'type' => 'radiology']) }}">
                    ➕ Add Radiology / Imaging
                </a>
            </li>

            <li>
                <a class="action-link"
                   href="{{ $appointment->prescription
                        ? route('vet.prescription.edit', $appointment->id)
                        : route('vet.prescription.create', $appointment->id) }}">
                    {{ $appointment->prescription ? '✏️ Edit Prescription' : '➕ Add Prescription' }}
                </a>
            </li>

        </ul>

        <hr>
    @endif

    {{-- Case Sheet (ALWAYS READ-ONLY VIEW) --}}
    @if($appointment->caseSheet)
        <h3>Case Sheet</h3>

        <p>
            <strong>Clinic:</strong> {{ optional($appointment->clinic)->name ?? '-' }} <br>
            <strong>Doctor:</strong> {{ optional($appointment->vet)->name ?? '-' }}
        </p>

        <ul>
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
            ] as $label => $value)
                @if($value)
                    <li>
                        <strong>{{ $label }}:</strong> {{ $value }}
                    </li>
                @endif
            @endforeach
        </ul>

        <hr>
    @endif

    {{-- ===================== --}}
{{-- DIAGNOSTICS (READ-ONLY / EDITABLE UNTIL COMPLETED) --}}
{{-- ===================== --}}

<h3>Diagnostics</h3>

@php
    use App\Models\DiagnosticFile;

    $verifiedFindings =
        DiagnosticFile::verifiedSummariesForAppointment($appointment->id);
@endphp

@if($verifiedFindings)
    <div style="
        margin-bottom:16px;
        padding:14px 16px;
        background:#ecfeff;
        border:1px solid #67e8f9;
        border-radius:8px;
    ">
        <strong style="color:#0f766e;">
            🧪 Combined Verified Diagnostic Findings
        </strong>

        <ul style="margin-top:8px;padding-left:18px;">
            @foreach(explode("\n", $verifiedFindings) as $line)
                <li>{{ ltrim($line, '- ') }}</li>
            @endforeach
        </ul>

        <p style="font-size:12px;color:#155e75;margin-top:6px;">
            Based only on human-verified diagnostic files.
        </p>
    </div>
@endif

@if($appointment->diagnosticReports->isEmpty())
    <p style="color:#6b7280;font-size:14px;">
        No lab or radiology reports added for this visit.
    </p>
@else
    <div style="display:flex;flex-direction:column;gap:14px;margin-top:12px;">
        @foreach($appointment->diagnosticReports as $report)
            <div style="
                background:#f9fafb;
                border:1px solid #e5e7eb;
                border-radius:8px;
                padding:16px;
            ">

                {{-- EDIT / DELETE --}}
                @if($appointment->status !== 'completed')
                    <div style="text-align:right;margin-bottom:6px;">
                        <a href="{{ route('vet.diagnostics.edit', $report->id) }}" class="action-link">
                            ✏️ Edit
                        </a>

                        <form method="POST"
                              action="{{ route('vet.diagnostics.destroy', $report->id) }}"
                              style="display:inline;"
                              onsubmit="return confirm('Delete this diagnostic report?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="background:none;border:none;color:#dc2626;font-size:13px;cursor:pointer;">
                                🗑 Delete
                            </button>
                        </form>
                    </div>
                @endif

                {{-- REPORT HEADER --}}
                <strong>
                    {{ strtoupper($report->type) }}
                    @if($report->title) — {{ $report->title }} @endif
                </strong>

                <div style="font-size:13px;color:#374151;margin-top:4px;">
                    @if($report->report_date)
                        Date: {{ $report->report_date->format('d M Y') }}
                    @endif
                    @if($report->lab_or_center)
                        | Source: {{ $report->lab_or_center }}
                    @endif
                </div>

                {{-- REPORT NOTES --}}

                @php
                    $verifiedFindings = $report->files
                        ->where('status', 'human_verified')
                        ->pluck('ai_summary')
                        ->filter()
                        ->implode("\n");
                @endphp

                <!-- @if($verifiedFindings)
                    <div style="margin-top:10px;">
                        <strong>Diagnostic Summary:</strong>
                        <ul style="margin-top:6px;padding-left:18px;">
                            @foreach(preg_split('/\r\n|\r|\n|•/', $verifiedFindings) as $line)
                                @if(trim($line))
                                    <li>{{ trim($line) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif -->

                {{-- FILES --}}
                @if($report->files->isNotEmpty())
                    <div style="margin-top:10px;">
                        <strong style="font-size:13px;">Attached Reports:</strong>
                        <ul style="margin-top:6px;padding-left:18px;">
                            @foreach($report->files as $file)
                                <li style="font-size:13px;display:flex;flex-direction:column;gap:6px;">
                                    <div style="display:flex;gap:10px;align-items:center;">
                                       {{ $file->display_name ?: $file->original_filename }}

                                        @if($file->status === 'human_verified')
                                            <span style="color:#16a34a;font-size:12px;">✔ Verified</span>
                                        @else
                                            <span style="color:#dc2626;font-size:12px;">⚠ Not Verified</span>
                                        @endif

                                        <button
                                            type="button"
                                            onclick="openFloatingReport('{{ route('vet.diagnostics.files.embed', $file->id) }}')"
                                            style="
                                                background:none;
                                                border:none;
                                                padding:0;
                                                font-size:13px;
                                                cursor:pointer;
                                                color:#2563eb;
                                            ">
                                            👁 View
                                        </button>
                                        <a href="{{ route('vet.diagnostics.download', $file->id) }}"
                                           class="action-link">⬇ Download</a>
                                    </div>

                                    @if($file->ai_summary)
                                        <div style="
                                            margin-left:16px;
                                            margin-top:6px;
                                            padding:10px 12px;
                                            border-radius:8px;
                                            background: {{ $file->status === 'human_verified' ? '#ecfeff' : '#fff7ed' }};
                                            border: 1px solid {{ $file->status === 'human_verified' ? '#67e8f9' : '#fed7aa' }};
                                            font-size:13px;
                                        ">
                                            <strong style="
                                                color: {{ $file->status === 'human_verified' ? '#0f766e' : '#9a3412' }};
                                            ">
                                                {{ $file->status === 'human_verified'
                                                    ? '✔ Findings (Verified)'
                                                    : '⚠ AI Findings (Not Verified)' }}
                                            </strong>

                                            <ul style="margin-top:6px;padding-left:18px;">
                                                @foreach(preg_split('/\r\n|\r|\n|•/', $file->ai_summary) as $line)
                                                    @if(trim($line))
                                                        <li>{{ trim($line) }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>

                                            @if($file->status !== 'human_verified')
                                                <p style="
                                                    margin-top:6px;
                                                    font-size:12px;
                                                    color:#9a3412;
                                                    font-style:italic;
                                                ">
                                                    These findings are AI-generated and require clinician verification.
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>
        @endforeach
    </div>
@endif

    <div id="history-modal"
     style="display:none; position:fixed; inset:0;
            background:rgba(0,0,0,0.6);
            z-index:9999;">

    <div style="
        background:#ffffff;
        width:800px;
        max-width:95%;
        margin:40px auto;
        padding:24px;
        border-radius:10px;
        max-height:85vh;
        overflow:auto;
    ">
        <h3>Past Case Details</h3>
        <hr>

        <div id="history-modal-content">
            Loading...
        </div>

        <div style="text-align:right;margin-top:16px;">
            <button onclick="closeHistoryModal()">Close</button>
        </div>
    </div>
</div>

</div> {{-- end .card --}}
</div> {{-- end .casesheet-left --}}

<!-- RIGHT COLUMN -->
<div class="casesheet-right">

<h3>🧠 Senior Vet Clinical Decision Support (AI)</h3>

<p style="font-size:13px;color:#6b7280;">
    AI-assisted guidance to support clinical reasoning and learning.
    This is <strong>not</strong> a diagnosis, prescription, or medical record.
</p>

<div id="senior-vet-ai"
    style="
        margin-top:10px;
        padding:14px;
        background:#f9fafb;
        border:1px solid #e5e7eb;
        border-radius:8px;
        font-size:14px;
        white-space:pre-wrap;
    ">
    No guidance generated yet.
</div>

@if(!$readOnly)
    <button
        style="margin-top:10px;"
        onclick="generateSeniorVetSupport({{ $appointment->id }})">
        Get Senior Vet Guidance
    </button>
@endif

{{-- Prescription (READ-ONLY VIEW) --}}
@if($appointment->prescription)
    <h3>Prescription</h3>

    <p><strong>Notes:</strong> {{ $appointment->prescription->notes ?? '-' }}</p>

    <ul>
        @foreach($appointment->prescription->items as $item)
            <li>
                {{ $item->medicine }}
                — {{ $item->dosage }},
                {{ $item->frequency }},
                {{ $item->duration }}
            </li>
        @endforeach
    </ul>
@endif

</div> <!-- end .casesheet-right -->
</div> <!-- end .casesheet-wrapper -->
</div>
</div>

<!-- ================= FLOATING REPORT WINDOW ================= -->
<div id="floating-report"
     style="
        display:none;
        position:fixed;
        top:120px;
        left:120px;
        width:480px;
        height:520px;
        background:#ffffff;
        border:1px solid #e5e7eb;
        border-radius:10px;
        box-shadow:0 25px 60px rgba(0,0,0,0.35);
        z-index:9999;
        overflow:hidden;
     ">

    <!-- HEADER (DRAG HANDLE) -->
    <div id="floating-report-header"
         style="
            height:42px;
            background:#f9fafb;
            border-bottom:1px solid #e5e7eb;
            cursor:move;
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:0 12px;
            font-size:14px;
            font-weight:600;
         ">
        Diagnostic Report
        <button
        type="button"
        onclick="closeFloatingReport()"
        style="
            background:#ffffff;
            border:1px solid #e5e7eb;
            color:#111827;
            font-size:16px;
            line-height:1;
            width:28px;
            height:28px;
            border-radius:6px;
            cursor:pointer;
            display:flex;
            align-items:center;
            justify-content:center;
            z-index:10001;
            ">
            ✕
        </button>
    </div>

    <!-- CONTENT -->
    <iframe id="floating-report-frame"
            style="width:100%;height:calc(100% - 42px);border:none;">
    </iframe>

</div>

<script>
(function () {
    const floatingReport = document.getElementById('floating-report');
    const floatingHeader = document.getElementById('floating-report-header');

    if (!floatingReport || !floatingHeader) {
        console.warn('Floating report elements not found');
        return;
    }

    let isDragging = false;
    let offsetX = 0;
    let offsetY = 0;

    floatingHeader.addEventListener('mousedown', (e) => {
        isDragging = true;
        offsetX = e.clientX - floatingReport.offsetLeft;
        offsetY = e.clientY - floatingReport.offsetTop;

        // bring to front
        floatingReport.style.zIndex = 10000;
        document.body.style.userSelect = 'none';
    });

    document.addEventListener('mousemove', (e) => {
        if (!isDragging) return;

        floatingReport.style.left = (e.clientX - offsetX) + 'px';
        floatingReport.style.top  = (e.clientY - offsetY) + 'px';
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
        document.body.style.userSelect = '';
    });
})();
</script>

@endsection

<script>
function openHistoryModal(appointmentId) {
    document.getElementById('history-modal').style.display = 'block';
    document.getElementById('history-modal-content').innerHTML = 'Loading...';

    fetch(`/vet/appointments/${appointmentId}/history-view`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('history-modal-content').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('history-modal-content').innerHTML =
                'Failed to load history.';
        });
}

function closeHistoryModal() {
    document.getElementById('history-modal').style.display = 'none';
}
</script>

<script>
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
</script>

<script>
async function generateClinicalInsights() {
    const payload = {
        presenting_complaint: document.querySelector('[name="presenting_complaint"]')?.value || '',
        history: document.querySelector('[name="history"]')?.value || '',
        clinical_examination: document.querySelector('[name="clinical_examination"]')?.value || '',
        differentials: document.querySelector('[name="differentials"]')?.value || '',
        diagnosis: document.querySelector('[name="diagnosis"]')?.value || '',
        treatment_given: document.querySelector('[name="treatment_given"]')?.value || '',
        body_weight: document.querySelector('[name="weight"]')?.value || ''
    };

    const insightsBox = document.getElementById('ai-insights-content');
    insightsBox.innerHTML = '⏳ Analyzing case...';

    try {
        const res = await fetch('/vet/ai/clinical-insights', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();
        insightsBox.innerHTML = data.raw || 'No insights generated.';
    } catch (e) {
        insightsBox.innerHTML = '⚠️ Failed to generate insights.';
    }
}
</script>

<button onclick="generateClinicalInsights()">
    Generate AI Clinical Insights
</button>

<script>
async function generateSeniorVetSupport(appointmentId) {
    const box = document.getElementById('senior-vet-ai');

    if (!box) return;

    box.innerText = '🧠 Senior vet reviewing the case...';

    try {
        const res = await fetch(
            `/vet/ai/senior-support/${appointmentId}`,
            {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }
        );

        if (!res.ok) {
            throw new Error('Request failed');
        }

        const data = await res.json();
        box.innerText = data.guidance || 'No guidance generated.';
    } catch (e) {
        box.innerText = '⚠️ Failed to generate senior vet guidance.';
    }
}
</script>


<script>
function openFloatingReport(url) {
    const box = document.getElementById('floating-report');
    const frame = document.getElementById('floating-report-frame');

    if (!box || !frame) {
        alert('Floating report not found');
        return;
    }

    frame.src = url;
    box.style.display = 'block';
}

function closeFloatingReport() {
    const box = document.getElementById('floating-report');
    const frame = document.getElementById('floating-report-frame');

    box.style.display = 'none';
    frame.src = '';
}
</script>