@extends('layouts.vet')

@section('content')

<div style="max-width:800px;margin:0 auto;">
    <div class="v-card">
        <h2 style="font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 6px;">
            Add {{ ucfirst($type) }} Report
        </h2>

        <p style="color:var(--text-muted);font-size:14px;margin:0 0 18px;">
            Pet: <strong style="color:var(--text-dark);">{{ $appointment->pet->name }}</strong>
            &nbsp;|&nbsp;
            Visit: {{ $appointment->scheduled_at->format('d M Y') }}
        </p>

        <hr class="v-divider">

        {{-- Existing Files --}}
        @if(isset($report) && $report && $report->files->count())

        <h4 style="font-size:15px;font-weight:600;margin:0 0 12px;color:var(--text-dark);">
            Existing Files & AI Findings
        </h4>

        @foreach($report->files as $file)
            <div class="v-card v-card--compact" style="background:var(--bg-soft);">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
                    <div>
                        <strong>{{ $file->display_name ?: $file->original_filename }}</strong>
                        @if($file->status === 'human_verified')
                            <span class="v-badge v-badge--green" style="margin-left:6px;">Verified</span>
                        @else
                            <span class="v-badge v-badge--yellow" style="margin-left:6px;">Not Verified</span>
                        @endif
                    </div>

                    <div style="display:flex;gap:10px;align-items:center;">
                        <a href="{{ route('vet.diagnostics.files.view', $file->id) }}"
                           target="_blank" class="v-link" style="font-size:13px;">
                            View
                        </a>

                        @if($file->status !== 'human_verified')
                            <form method="POST" action="{{ route('vet.diagnostics.files.verify', $file->id) }}">
                                @csrf
                                <button type="submit" class="v-btn v-btn--success v-btn--sm">Mark Verified</button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($file->ai_summary)
                    <div style="margin-top:10px;font-size:13px;color:var(--text);white-space:pre-wrap;">
                        {!! nl2br(e($file->ai_summary)) !!}
                    </div>
                @endif
            </div>
        @endforeach

        <hr class="v-divider">
        @endif

        <form onsubmit="return false;" style="display:flex;flex-direction:column;gap:14px;" enctype="multipart/form-data">

            <h4 style="margin:0;font-size:15px;font-weight:600;color:var(--text-dark);">Upload Diagnostic Files</h4>

            <div id="fileBlocks" style="display:flex;flex-direction:column;gap:12px;"></div>

            <input
                type="file"
                id="reportInput"
                accept=".pdf"
                multiple
                class="v-input"
                style="border-style:dashed;cursor:pointer;">

            <button type="button" id="saveReportsBtn" class="v-btn v-btn--primary v-btn--block">
                Save All Reports
            </button>
        </form>

        {{-- PDF Modal --}}
        <div id="pdfModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;">
            <div style="width:80%;height:85%;background:#fff;border-radius:var(--radius-md);overflow:hidden;position:relative;">
                <button onclick="closePdfModal()" class="v-btn v-btn--danger v-btn--sm" style="position:absolute;top:10px;right:10px;z-index:10;">
                    Close
                </button>
                <iframe id="pdfViewer" style="width:100%;height:100%;border:none;"></iframe>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    function getFileKey(file) {
        return `${file.name}_${file.size}_${file.lastModified}`;
    }

    let selectedFiles = [];
    let extractionState = {};

    const fileInput = document.getElementById('reportInput');
    const container = document.getElementById('fileBlocks');
    const saveBtn = document.getElementById('saveReportsBtn');

    if (!fileInput || !container) {
        console.error('File input or container missing');
        return;
    }

    fileInput.addEventListener('change', function () {
        for (const file of this.files) {
            if (!selectedFiles.some(f => getFileKey(f) === getFileKey(file))) {
                selectedFiles.push(file);
            }
        }
        this.value = '';
        renderFileList();
    });

    function renderFileList() {
        container.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const fileKey = getFileKey(file);

            const card = document.createElement('div');
            card.className = 'v-card v-card--compact';
            card.style.background = 'var(--bg-soft)';
            card.style.marginBottom = '0';

            card.innerHTML = `
                <div style="font-weight:600;margin-bottom:6px;">
                    Diagnostic File ${index + 1}
                    <span style="font-weight:400;color:var(--text-muted);font-size:12px;">
                        (${file.name})
                    </span>
                </div>

                <div class="v-form-group" style="margin-bottom:10px;">
                    <label style="font-size:13px;">Report Name</label>
                    <input type="text" placeholder="e.g. CBC, LFT, Tick Panel" class="v-input">
                </div>

                <div style="display:flex;gap:10px;margin-bottom:8px;">
                    <button type="button" class="v-btn v-btn--outline v-btn--sm view-btn">View</button>
                    <button type="button" class="v-btn v-btn--outline v-btn--sm extract-btn">Extract</button>
                    <button type="button" class="v-btn v-btn--danger v-btn--sm remove-btn">Remove</button>
                </div>

                <textarea placeholder="AI findings will appear here" class="v-input" style="min-height:120px;"></textarea>
            `;

            const nameInput = card.querySelector('input');
            const textarea = card.querySelector('textarea');

            card.querySelector('.view-btn').onclick = () => viewPdf(file);
            card.querySelector('.extract-btn').onclick = () =>
                extractSingleFile(file, textarea);

            card.querySelector('.remove-btn').onclick = () => {
                selectedFiles.splice(index, 1);
                delete extractionState[fileKey];
                card.remove();
            };

            extractionState[fileKey] = {
                file,
                display_name: '',
                ai_summary: ''
            };

            nameInput.oninput = () => {
                extractionState[fileKey].display_name = nameInput.value;
            };

            textarea.oninput = () => {
                extractionState[fileKey].ai_summary = textarea.value;
            };

            container.appendChild(card);
        });
    }

    async function extractSingleFile(file, textarea) {
        const formData = new FormData();
        formData.append('report_files[]', file);
        formData.append('type', '{{ $type }}');

        textarea.value = 'Extracting...';

        try {
            const response = await fetch(
                "{{ route('vet.diagnostics.extract', $appointment->id) }}",
                {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                }
            );

            const data = await response.json();

            if (!response.ok || !data.files?.length) {
                throw new Error();
            }

            const summary = data.files[0].summary ?? '';
            textarea.value = summary;

            const fileKey = getFileKey(file);
            extractionState[fileKey].ai_summary = summary;

        } catch (e) {
            textarea.value = 'Extraction failed';
        }
    }

    if (saveBtn) {
        saveBtn.addEventListener('click', async () => {

            if (!Object.keys(extractionState).length) {
                alert('No extracted reports to save');
                return;
            }

            const payload = new FormData();

            Object.values(extractionState).forEach((item, index) => {
                payload.append(`reports[${index}][file]`, item.file);
                payload.append(`reports[${index}][display_name]`, item.display_name);
                payload.append(`reports[${index}][ai_summary]`, item.ai_summary);
            });

            payload.append('type', '{{ $type }}');
            payload.append('appointment_id', '{{ $appointment->id }}');

            try {
                const response = await fetch(
                    "{{ route('vet.diagnostics.store', $appointment->id) }}",
                    {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: payload
                    }
                );

                if (!response.ok) throw new Error();

                alert('Reports saved successfully');
                window.location.reload();

            } catch (err) {
                alert('Error saving reports');
                console.error(err);
            }
        });
    }

    renderFileList();
});

function viewPdf(file) {
    const modal = document.getElementById('pdfModal');
    const viewer = document.getElementById('pdfViewer');
    viewer.src = URL.createObjectURL(file);
    modal.style.display = 'flex';
}

function closePdfModal() {
    document.getElementById('pdfViewer').src = '';
    document.getElementById('pdfModal').style.display = 'none';
}
</script>
@endsection
