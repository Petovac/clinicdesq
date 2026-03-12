@extends('layouts.vet')

@section('content')
<div
    style="
        max-width:800px;
        margin:40px auto;
        background:#ffffff;
        border-radius:12px;
        padding:28px;
        box-shadow:0 8px 24px rgba(0,0,0,0.06);
        border:1px solid #e5e7eb;
        font-family:system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto;
    "
>
    <h2
        style="
            font-size:22px;
            font-weight:600;
            color:#111827;
            margin-bottom:6px;
        "
    >
        Add {{ ucfirst($type) }} Report
    </h2>

    <p
        style="
            color:#6b7280;
            font-size:14px;
            margin-bottom:18px;
        "
    >
        Pet: <strong style="color:#111827;">{{ $appointment->pet->name }}</strong>
        &nbsp;|&nbsp;
        Visit: {{ $appointment->scheduled_at->format('d M Y') }}
    </p>

    <hr style="border:none;border-top:1px solid #e5e7eb;margin:20px 0;">

    {{-- ===============================
    Existing Files (if any)
    =============================== --}}

    @if(isset($report) && $report && $report->files->count())

    <h4 style="font-size:15px;font-weight:600;margin-bottom:12px;color:#111827;">
        Existing Files & AI Findings
    </h4>

    @foreach($report->files as $file)
        <div
            style="
                border:1px solid #e5e7eb;
                border-radius:10px;
                padding:14px;
                margin-bottom:14px;
                background:#fafafa;
            "
        >
            <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
                <div>
                    <strong>
                        {{ $file->display_name ?: $file->original_filename }}
                    </strong>

                    @if($file->status === 'human_verified')
                        <span style="color:#16a34a;font-size:12px;margin-left:6px;">✔ Verified</span>
                    @else
                        <span style="color:#b45309;font-size:12px;margin-left:6px;">
                            ⚠ Not Verified
                        </span>
                    @endif
                </div>

                <div style="display:flex;gap:10px;">
                    <a href="{{ route('vet.diagnostics.files.view', $file->id) }}"
                    target="_blank"
                    style="font-size:13px;color:#2563eb;text-decoration:none;">
                        👁 View
                    </a>

                    @if($file->status !== 'human_verified')
                        <form method="POST"
                            action="{{ route('vet.diagnostics.files.verify', $file->id) }}">
                            @csrf
                            <button
                                type="submit"
                                style="
                                    font-size:12px;
                                    padding:4px 8px;
                                    border-radius:6px;
                                    border:1px solid #16a34a;
                                    background:#ecfdf5;
                                    color:#16a34a;
                                    cursor:pointer;
                                ">
                                ✔ Mark Verified
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if($file->ai_summary)
                <div style="margin-top:10px;font-size:13px;color:#374151;white-space:pre-wrap;">
                    {!! nl2br(e($file->ai_summary)) !!}
                </div>
            @endif
        </div>
    @endforeach

    <hr style="border:none;border-top:1px solid #e5e7eb;margin:22px 0;">

    @endif

    <form
    onsubmit="return false;"
    style="display:flex;flex-direction:column;gap:14px;"
    enctype="multipart/form-data">



        <!-- File -->
        {{-- ===============================
   Upload Diagnostic Files
=============================== --}}

<h4 style="margin-top:16px;">Upload Diagnostic Files</h4>

<div id="fileBlocks"
     style="display:flex;flex-direction:column;gap:12px;margin-top:10px;">
</div>

<input
    type="file"
    id="reportInput"
    accept=".pdf"
    multiple
    style="
        width:100%;
        margin-top:10px;
        padding:10px;
        border-radius:8px;
        border:1px dashed #d1d5db;
        background:#ffffff;
        cursor:pointer;
    ">

<button
    type="button"
    id="saveReportsBtn"
    style="
        margin-top:18px;
        padding:12px;
        font-size:15px;
        font-weight:600;
        border-radius:10px;
        border:none;
        background:#2563eb;
        color:#fff;
        cursor:pointer;
        width:100%;
    ">
    💾 Save All Reports
</button>

<!-- 🔽 PASTE MODAL CODE HERE -->
<div id="pdfModal"
     style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(0,0,0,0.6);
        z-index:9999;
        align-items:center;
        justify-content:center;
     ">
    <div style="
        width:80%;
        height:85%;
        background:#fff;
        border-radius:10px;
        overflow:hidden;
        position:relative;
    ">
        <button onclick="closePdfModal()"
            style="
                position:absolute;
                top:10px;
                right:10px;
                z-index:10;
                padding:6px 10px;
                border:none;
                border-radius:6px;
                background:#ef4444;
                color:#fff;
                cursor:pointer;
            ">
            ✕ Close
        </button>

        <iframe id="pdfViewer"
            style="width:100%;height:100%;border:none;">
        </iframe>
    </div>
</div>
<!-- 🔼 END MODAL -->

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ===============================
       HELPERS
    =============================== */
    function getFileKey(file) {
        return `${file.name}_${file.size}_${file.lastModified}`;
    }

    /* ===============================
       STATE
    =============================== */
    let selectedFiles = [];
    let extractionState = {};      // fileKey -> extraction data

    const fileInput = document.getElementById('reportInput');
    const container = document.getElementById('fileBlocks');
    const clinicalTextarea = document.getElementById('clinical_summary');
    const saveBtn = document.getElementById('saveReportsBtn');

    if (!fileInput || !container) {
        console.error('File input or container missing');
        return;
    }

    /* ===============================
       FILE SELECTION (APPEND MODE)
    =============================== */
    fileInput.addEventListener('change', function () {
        for (const file of this.files) {
            if (!selectedFiles.some(f => getFileKey(f) === getFileKey(file))) {
                selectedFiles.push(file);
            }
        }
        this.value = ''; // allow reselecting same file
        renderFileList();
    });

    /* ===============================
       FILE LIST RENDER
    =============================== */
    function renderFileList() {
    container.innerHTML = '';

    selectedFiles.forEach((file, index) => {
        const fileKey = getFileKey(file);

        const card = document.createElement('div');
        card.style.border = '1px solid #e5e7eb';
        card.style.borderRadius = '10px';
        card.style.padding = '12px';
        card.style.marginBottom = '12px';
        card.style.background = '#f9fafb';

        card.innerHTML = `
            <div style="font-weight:600;margin-bottom:6px;">
                Diagnostic File ${index + 1}
                <span style="font-weight:400;color:#6b7280;font-size:12px;">
                    (${file.name})
                </span>
            </div>

            <label style="font-size:13px;">Report Name</label>
            <input type="text"
                placeholder="e.g. CBC, LFT, Tick Panel"
                style="width:100%;padding:8px;margin:4px 0 10px 0;border:1px solid #d1d5db;border-radius:6px;"
            >

            <div style="display:flex;gap:10px;margin-bottom:8px;">
                <button type="button" class="view-btn">View</button>
                <button type="button" class="extract-btn">Extract</button>
                <button type="button" class="remove-btn">Remove</button>
            </div>

            <textarea
                placeholder="AI findings will appear here"
                style="width:100%;min-height:120px;padding:8px;border:1px solid #d1d5db;border-radius:6px;"
            ></textarea>
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

    /* ===============================
       EXTRACTION (IDEMPOTENT)
    =============================== */
        async function extractSingleFile(file, textarea) {
        const formData = new FormData();
        formData.append('report_files[]', file);
        formData.append('type', '{{ $type }}');

        textarea.value = 'Extracting…';

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
            textarea.value = '❌ Extraction failed';
        }
    }

    /* ===============================
       SAVE ALL REPORTS
    =============================== */
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

/* ===============================
   PDF VIEWER
=============================== */
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