@extends('layouts.vet')

@section('content')

<style>

/* ===== Diagnostic Edit UX ===== */

.diagnostic-file-card {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 18px;
    background: #fafafa;
}

.diagnostic-file-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.diagnostic-file-title {
    font-weight: 600;
    font-size: 15px;
    color: #111827;
}

.diagnostic-status {
    font-size: 12px;
    font-weight: 600;
}

.diagnostic-status.not-verified {
    color: #b45309;
}

.diagnostic-status.verified {
    color: #15803d;
}

.diagnostic-actions {
    margin-bottom: 12px;
}

.diagnostic-actions button {
    background: none;
    border: none;
    color: #2563eb;
    font-size: 13px;
    cursor: pointer;
    padding: 0;
}

.diagnostic-findings {
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 12px;
}

.diagnostic-findings label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 6px;
    color: #374151;
}

.diagnostic-findings textarea {
    width: 100%;
    min-height: 120px;
    padding: 10px;
    font-size: 13px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    resize: vertical;
}

.diagnostic-findings .hint {
    font-size: 12px;
    color: #6b7280;
    font-weight: 400;
}

.diagnostic-findings-actions {
    margin-top: 10px;
    display: flex;
    justify-content: flex-end;
}

.diagnostic-findings-actions button {
    background: #16a34a;
    color: #ffffff;
    border: none;
    padding: 8px 14px;
    font-size: 13px;
    border-radius: 6px;
    cursor: pointer;
}
</style>

<div class="card"
     style="
        max-width:900px;
        margin:32px auto;
        padding:24px 28px;
        background:#ffffff;
        border-radius:12px;
        box-shadow:0 8px 24px rgba(0,0,0,0.06);
        border:1px solid #e5e7eb;
     ">

    <h2 style="
        margin-bottom:6px;
        font-size:22px;
        font-weight:600;
        color:#111827;
    ">
        Edit {{ strtoupper($report->type) }} Diagnostic
    </h2>

    <p class="muted" style="
        margin-bottom:22px;
        font-size:13px;
        color:#6b7280;
    ">
        Pet: <strong style="color:#111827;">{{ $appointment->pet->name }}</strong> |
        Visit: {{ $appointment->scheduled_at->format('d M Y') }}
    </p>

    <h3>Existing Diagnostic Files</h3>

@foreach($report->files as $file)
<div class="diagnostic-file-card">

    {{-- HEADER --}}
    <div class="diagnostic-file-header">
        <div class="diagnostic-file-title">
            {{ $file->display_name ?: $file->original_filename }}
        </div>

        <div class="diagnostic-status {{ $file->status === 'human_verified' ? 'verified' : 'not-verified' }}">
            {{ $file->status === 'human_verified'
                ? '✔ Verified'
                : '⚠ AI Findings (Not Verified)' }}
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="diagnostic-actions">
        <button
            type="button"
            onclick="openFloatingReport('{{ route('vet.diagnostics.files.embed', $file->id) }}')">
            👁 View Report
        </button>
    </div>

    {{-- FINDINGS --}}
    <form method="POST"
          action="{{ route('vet.diagnostics.files.updateSummary', $file->id) }}">
        @csrf
        @method('PUT')

        <div class="diagnostic-findings">
            <label>
                AI Findings
                <span class="hint">
                    (Editing & saving will mark this as verified)
                </span>
            </label>

            <textarea name="ai_summary">{{ $file->ai_summary }}</textarea>

            <div class="diagnostic-findings-actions">
                <button type="submit">
                    ✔ Save & Verify Findings
                </button>
            </div>
        </div>
    </form>

</div>
@endforeach

        <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">

        {{-- Upload --}}
        <h4 style="font-size:15px;font-weight:600;margin-bottom:10px;color:#111827;">
            Add More PDFs
        </h4>
        <input type="file"
               name="new_files[]"
               multiple
               accept="application/pdf"
               style="margin-bottom:24px;font-size:14px;">

        {{-- Actions --}}
        <div style="display:flex;gap:14px;align-items:center;">
            <button type="submit"
                    style="
                        background:#2563eb;
                        color:#ffffff;
                        padding:10px 18px;
                        border-radius:8px;
                        border:none;
                        font-size:14px;
                        font-weight:500;
                        cursor:pointer;
                    ">
                Save Changes
            </button>

            <a href="{{ route('vet.appointments.case', $appointment->id) }}"
               style="
                    font-size:14px;
                    color:#6b7280;
                    text-decoration:none;
               ">
                Cancel
            </a>
        </div>
    </form>
</div>

<!-- FLOATING REPORT VIEWER -->
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
        <button type="button" onclick="closeFloatingReport()">✕</button>
    </div>

    <iframe id="floating-report-frame"
            style="width:100%;height:calc(100% - 42px);border:none;">
    </iframe>
</div>

<script>
function deleteDiagnosticFile(fileId) {
    if (!confirm('Delete this file?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/vet/diagnostics/files/${fileId}`;

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';

    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'DELETE';

    form.appendChild(csrf);
    form.appendChild(method);

    document.body.appendChild(form);
    form.submit();
}
</script>

<script>
function toggleEdit(fileId) {
    const view = document.getElementById('view-' + fileId);
    const edit = document.getElementById('edit-' + fileId);

    if (view.style.display === 'none') {
        view.style.display = 'block';
        edit.style.display = 'none';
    } else {
        view.style.display = 'none';
        edit.style.display = 'block';
    }
}
</script>

<script>
function openFloatingReport(url) {
    const box = document.getElementById('floating-report');
    const frame = document.getElementById('floating-report-frame');

    // Force embedded PDF viewer with controls
    frame.src = url + '#toolbar=1&navpanes=0&scrollbar=1&zoom=page-width';

    box.style.display = 'block';
}

function closeFloatingReport() {
    const box = document.getElementById('floating-report');
    const frame = document.getElementById('floating-report-frame');

    box.style.display = 'none';
    frame.src = '';
}
</script>

<script>
(function () {
    const box = document.getElementById('floating-report');
    const header = document.getElementById('floating-report-header');

    if (!box || !header) return;

    let isDragging = false;
    let offsetX = 0;
    let offsetY = 0;

    header.addEventListener('mousedown', function (e) {
        isDragging = true;

        offsetX = e.clientX - box.offsetLeft;
        offsetY = e.clientY - box.offsetTop;

        box.style.zIndex = 10000;
        document.body.style.userSelect = 'none';
    });

    document.addEventListener('mousemove', function (e) {
        if (!isDragging) return;

        box.style.left = (e.clientX - offsetX) + 'px';
        box.style.top  = (e.clientY - offsetY) + 'px';
    });

    document.addEventListener('mouseup', function () {
        isDragging = false;
        document.body.style.userSelect = '';
    });
})();
</script>

@endsection