@extends('layouts.vet')

@section('content')

<div style="max-width:900px;margin:0 auto;">
    <div class="v-card">

        <h2 style="font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 6px;">
            Edit {{ strtoupper($report->type) }} Diagnostic
        </h2>

        <p style="font-size:13px;color:var(--text-muted);margin:0 0 22px;">
            Pet: <strong style="color:var(--text-dark);">{{ $appointment->pet->name }}</strong> |
            Visit: {{ $appointment->scheduled_at->format('d M Y') }}
        </p>

        <h3 class="v-section-title">Existing Diagnostic Files</h3>

        @foreach($report->files as $file)
        <div class="v-card v-card--compact" style="background:var(--bg-soft);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                <strong style="font-size:15px;color:var(--text-dark);">
                    {{ $file->display_name ?: $file->original_filename }}
                </strong>

                @if($file->status === 'human_verified')
                    <span class="v-badge v-badge--green">Verified</span>
                @else
                    <span class="v-badge v-badge--yellow">AI Findings (Not Verified)</span>
                @endif
            </div>

            <div style="margin-bottom:12px;">
                <button type="button"
                        onclick="openFloatingReport('{{ route('vet.diagnostics.files.embed', $file->id) }}')"
                        class="v-btn v-btn--ghost v-btn--sm">
                    View Report
                </button>
            </div>

            <form method="POST" action="{{ route('vet.diagnostics.files.updateSummary', $file->id) }}">
                @csrf
                @method('PUT')

                <div class="v-form-group">
                    <label>
                        AI Findings
                        <span style="font-weight:400;color:var(--text-muted);font-size:12px;">
                            (Editing & saving will mark this as verified)
                        </span>
                    </label>
                    <textarea name="ai_summary" class="v-input" style="min-height:120px;">{{ $file->ai_summary }}</textarea>
                </div>

                <div style="text-align:right;">
                    <button type="submit" class="v-btn v-btn--success v-btn--sm">Save & Verify Findings</button>
                </div>
            </form>
        </div>
        @endforeach

        <hr class="v-divider">

        <h4 style="font-size:15px;font-weight:600;margin:0 0 10px;color:var(--text-dark);">Add More PDFs</h4>
        <input type="file" name="new_files[]" multiple accept="application/pdf" class="v-input" style="border-style:dashed;cursor:pointer;margin-bottom:24px;">

        <div style="display:flex;gap:14px;align-items:center;">
            <button type="submit" class="v-btn v-btn--primary">Save Changes</button>
            <a href="{{ route('vet.appointments.case', $appointment->id) }}" class="v-btn v-btn--ghost">Cancel</a>
        </div>
    </div>
</div>

{{-- Floating Report Viewer --}}
<div id="floating-report" style="display:none;position:fixed;top:120px;left:120px;width:480px;height:520px;background:#fff;border:1px solid var(--border);border-radius:var(--radius-md);box-shadow:var(--shadow-lg);z-index:9999;overflow:hidden;">
    <div id="floating-report-header" style="height:42px;background:var(--bg-soft);border-bottom:1px solid var(--border);cursor:move;display:flex;align-items:center;justify-content:space-between;padding:0 12px;font-size:14px;font-weight:600;">
        Diagnostic Report
        <button type="button" onclick="closeFloatingReport()" class="v-btn v-btn--ghost v-btn--sm">&#x2715;</button>
    </div>
    <iframe id="floating-report-frame" style="width:100%;height:calc(100% - 42px);border:none;"></iframe>
</div>

@endsection

@section('scripts')
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

function openFloatingReport(url) {
    const box = document.getElementById('floating-report');
    const frame = document.getElementById('floating-report-frame');
    frame.src = url + '#toolbar=1&navpanes=0&scrollbar=1&zoom=page-width';
    box.style.display = 'block';
}

function closeFloatingReport() {
    const box = document.getElementById('floating-report');
    const frame = document.getElementById('floating-report-frame');
    box.style.display = 'none';
    frame.src = '';
}

(function () {
    const box = document.getElementById('floating-report');
    const header = document.getElementById('floating-report-header');
    if (!box || !header) return;

    let isDragging = false;
    let offsetX = 0, offsetY = 0;

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
