@extends('layouts.vet')

@section('content')

@php
    $prescription = $prescription ?? null;
@endphp

<style>
    /* ===== Card Layout ===== */
    .card {
        max-width: 900px;
        margin: 30px auto;
        padding: 28px 32px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 26px rgba(0, 0, 0, 0.08);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1f2937;
    }

    h2 {
        text-align: center;
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 14px;
        color: #111827;
    }

    h4 {
        margin-top: 12px;
        margin-bottom: 8px;
        font-size: 16px;
        font-weight: 600;
        color: #2563eb;
    }

    p {
        font-size: 14px;
        margin-bottom: 6px;
        color: #374151;
    }

    p strong {
        color: #111827;
        font-weight: 600;
    }

    label {
        display: block;
        margin-top: 14px;
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
    }

    hr {
        margin: 20px 0;
        border: none;
        border-top: 1px solid #e5e7eb;
    }

    /* ===== Inputs ===== */
    textarea,
    input[type="text"],
    input:not([type]),
    #medicine,
    #dosage,
    #frequency,
    #duration,
    #instructions {
        width: 100%;
        padding: 10px 12px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        outline: none;
        margin-bottom: 10px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    textarea:focus,
    input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }

    /* ===== Medicine Entry Grid ===== */
    #medicine,
    #dosage,
    #frequency,
    #duration,
    #instructions {
        display: inline-block;
        width: 19%;
        margin-right: 1%;
    }

    #instructions {
        margin-right: 0;
    }

    /* ===== Buttons ===== */
    button {
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 500;
        color: #ffffff;
        background-color: #2563eb;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }

    button:hover {
        background-color: #1e40af;
    }

    button:active {
        transform: scale(0.97);
    }

    button + button {
        margin-left: 10px;
    }

    /* ===== Medicine List ===== */
    #medicine-list {
        list-style: none;
        padding-left: 0;
        margin-top: 10px;
    }

    #medicine-list li {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 8px;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    #medicine-list li strong {
        color: #111827;
    }

    #medicine-list li button {
        background: #dc2626;
        padding: 6px 10px;
        font-size: 12px;
        border-radius: 6px;
    }

    #medicine-list li button:hover {
        background: #b91c1c;
    }

    #no-meds {
        font-size: 14px;
        color: #6b7280;
        font-style: italic;
    }

    /* ===== Preview Modal ===== */
    #preview-modal {
        backdrop-filter: blur(2px);
    }

    #preview-modal > div {
        background: #ffffff;
        width: 700px;
        max-width: 95%;
        margin: 40px auto;
        padding: 26px 30px;
        border-radius: 10px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.25);
        font-family: "Times New Roman", serif;
        color: #000;
    }

    #preview-modal h2 {
        text-align: center;
        font-size: 22px;
        margin-bottom: 10px;
        color: #000;
    }

    #preview-modal h4 {
        color: #000;
        font-size: 16px;
        margin-top: 14px;
    }

    #preview-modal p,
    #preview-modal td,
    #preview-modal th {
        font-size: 14px;
        color: #000;
    }

    #preview-modal table {
        border-collapse: collapse;
        margin-top: 8px;
    }

    #preview-modal th {
        background: #f3f4f6;
        font-weight: 600;
    }

    #preview-modal th,
    #preview-modal td {
        border: 1px solid #000;
        padding: 6px;
        text-align: left;
    }

    #preview-modal button {
        background: #374151;
    }

    #preview-modal button:hover {
        background: #111827;
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        #medicine,
        #dosage,
        #frequency,
        #duration,
        #instructions {
            width: 100%;
            margin-right: 0;
        }

        button {
            width: 100%;
            margin-top: 10px;
        }

        button + button {
            margin-left: 0;
        }
    }

/* ===============================
   PAGE WRAPPER
================================ */
.casesheet-wrapper {
    display: flex;
    gap: 32px;
    align-items: flex-start;

    max-width: 1400px;
    margin: 0 auto;
    padding: 0 24px 40px;
}

/* ===============================
   LEFT: PRESCRIPTION
================================ */
.casesheet-left {
    flex: 0 0 880px;
}

.casesheet-left .card {
    margin-top: 24px;
}

/* ===============================
   RIGHT: AI PANEL
================================ */
.casesheet-right {
    flex: 1;
    min-width: 360px;

    position: sticky;
    top: 88px;

    height: calc(100vh - 120px);
    overflow-y: auto;

    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 26px rgba(0,0,0,0.08);
    padding: 20px 22px;
}

/* AI panel header */
.casesheet-right h3 {
    margin: 0 0 8px;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

/* AI info text */
.casesheet-right p {
    font-size: 13px;
    line-height: 1.5;
    color: #6b7280;
    margin-bottom: 12px;
}

/* AI output box */
#ai-prescription-box {
    margin-top: 12px;
    padding: 14px 16px;

    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;

    font-size: 14px;
    line-height: 1.55;
    color: #111827;

    min-height: 160px;
}

/* AI generate button */
.casesheet-right button {
    width: 100%;
    margin-top: 14px;
    padding: 12px;

    font-size: 14px;
    font-weight: 600;

    background: #2563eb;
    border-radius: 8px;
}

/* ===============================
   MEDICINE INPUT GRID
================================ */
.medicine-row {
    display: grid;
    grid-template-columns: 1.4fr 1fr 1fr 1fr 1.6fr;
    gap: 8px;
    margin-bottom: 10px;
}

#medicine,
#dosage,
#frequency,
#duration,
#instructions {
    width: 100%;
    margin-right: 0;
}

/* ===============================
   MEDICINE LIST
================================ */
#medicine-list li {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 12px;
    padding: 10px 14px;

    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

#medicine-list li strong {
    font-weight: 600;
    color: #111827;
}

/* Delete button */
#medicine-list li button {
    flex-shrink: 0;
}

/* ===============================
   PREVIEW MODAL POLISH
================================ */
#preview-modal > div {
    border-radius: 12px;
}

#preview-modal table th {
    background: #f3f4f6;
}

/* ===============================
   RESPONSIVE
================================ */
@media (max-width: 1200px) {
    .casesheet-wrapper {
        flex-direction: column;
        padding: 0 16px 40px;
    }

    .casesheet-left {
        flex: 1;
    }

    .casesheet-right {
        position: relative;
        top: auto;
        height: auto;
        min-width: 100%;
    }
}

@media (max-width: 768px) {
    .medicine-row {
        grid-template-columns: 1fr;
    }

    .casesheet-right button {
        font-size: 15px;
    }
}
</style>

<div class="casesheet-wrapper">

    <!-- LEFT: PRESCRIPTION FORM -->
    <div class="casesheet-left">
        <div class="card">

            <h2>{{ $prescription ? 'Edit Prescription' : 'Add Prescription' }}</h2>

            <p><strong>Pet:</strong> {{ $appointment->pet->name }}</p>

            <form method="POST"
                  action="{{ route('vet.prescription.store', $appointment->id) }}">
                @csrf

                {{-- Diagnosis --}}
                <label>Diagnosis / Notes</label>
                <textarea name="notes" rows="3">{{ $prescription->notes ?? '' }}</textarea>

                <hr>

                {{-- Medicine Entry --}}
                <h4>Add Medicine</h4>

                <div class="medicine-row">
                <div style="position:relative;">
                <input id="medicine" placeholder="Type drug name…" autocomplete="off" style="width:100%;">
                <input type="hidden" id="medicine_drug_generic_id">
                <input type="hidden" id="medicine_inventory_item_id">
                <input type="hidden" id="medicine_strength_value">
                <input type="hidden" id="medicine_strength_unit">
                <input type="hidden" id="medicine_form">
                <div id="drug-results" style="
                    position:absolute;top:100%;left:0;right:0;
                    background:#fff;border:1px solid #d1d5db;border-radius:8px;
                    box-shadow:0 8px 20px rgba(0,0,0,.1);z-index:100;
                    max-height:240px;overflow-y:auto;display:none;
                "></div>
                </div>
                    <input id="dosage" placeholder="Dosage">
                    <input id="frequency" placeholder="Frequency">
                    <input id="duration" placeholder="Duration">
                    <input id="instructions" placeholder="Instructions">
                </div>

                <button type="button" onclick="addMedicine()">➕ Add Medicine</button>

                <hr>

                {{-- Added Medicines --}}
                <h4>Added Medicines</h4>

                <ul id="medicine-list">
                    @if($prescription && $prescription->items->count())
                        @foreach($prescription->items as $i => $item)
                            <li>
                                <strong>{{ $item->medicine }}</strong>
                                {{ $item->dosage }},
                                {{ $item->frequency }},
                                {{ $item->duration }}
                                @if($item->instructions)
                                    ({{ $item->instructions }})
                                @endif
                                <button type="button" onclick="removeMedicine(this)">❌</button>

                                <input type="hidden" name="medicines[{{ $i }}][medicine]" value="{{ $item->medicine }}">
                                <input type="hidden" name="medicines[{{ $i }}][dosage]" value="{{ $item->dosage }}">
                                <input type="hidden" name="medicines[{{ $i }}][frequency]" value="{{ $item->frequency }}">
                                <input type="hidden" name="medicines[{{ $i }}][duration]" value="{{ $item->duration }}">
                                <input type="hidden" name="medicines[{{ $i }}][instructions]" value="{{ $item->instructions }}">
                            </li>
                        @endforeach
                    @else
                        <li id="no-meds">No medicines added</li>
                    @endif
                </ul>

                <hr>

                <button type="button" onclick="showPreview()">👁 Preview for Client</button>
                <button type="submit">💾 Save Prescription</button>
            </form>

        </div> {{-- end .card --}}
    </div> {{-- end .casesheet-left --}}

    <!-- RIGHT: AI PRESCRIPTION SUPPORT -->
    <div class="casesheet-right">

        <h3>🧠 AI Prescription Support</h3>

        <p style="font-size:13px;color:#6b7280;">
            AI-assisted suggestions based on the case sheet and diagnostics.
            These are <strong>not</strong> prescriptions and must be reviewed
            by the veterinarian.
        </p>

        <div id="ai-prescription-box"
             style="
                margin-top:10px;
                padding:14px;
                background:#f9fafb;
                border:1px solid #e5e7eb;
                border-radius:8px;
                font-size:14px;
                white-space:pre-wrap;
             ">
            No prescription suggestions generated yet.
        </div>

        <button
            type="button"
            style="margin-top:10px;"
            onclick="generatePrescriptionAI()">
            Generate Prescription Suggestions
        </button>

    </div> {{-- end .casesheet-right --}}

</div> {{-- end .casesheet-wrapper --}}

{{-- PREVIEW MODAL --}}
<div id="preview-modal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999;">

    <div style="background:#fff; width:700px; margin:40px auto; padding:20px;">
        <h2 style="text-align:center;">Prescription</h2>

        <hr>

        <p><strong>Clinic:</strong> {{ $appointment->clinic->name ?? '-' }}</p>
        <p><strong>Doctor:</strong> {{ $appointment->vet->name ?? '-' }}</p>
        <p><strong>Reg No:</strong> {{ $appointment->vet->registration_no ?? '-' }}</p>

        <hr>

        <p><strong>Pet:</strong> {{ $appointment->pet->name }}</p>
        <p><strong>Date:</strong> {{ now()->format('d M Y') }}</p>

        <hr>

        <h4>Diagnosis / Notes</h4>
        <p id="preview-notes">—</p>

        <h4>Medicines</h4>
        <table width="100%" border="1" cellpadding="6">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Instructions</th>
                </tr>
            </thead>
            <tbody id="preview-medicines"></tbody>
        </table>

        <br>
        <button onclick="closePreview()">Close</button>
    </div>
</div>

<script>
let medicineIndex = {{ $prescription ? $prescription->items->count() : 0 }};

function addMedicine() {
    const medicine = document.getElementById('medicine').value.trim();
    if (!medicine) {
        alert('Medicine name is required');
        return;
    }

    const dosage       = document.getElementById('dosage').value;
    const frequency    = document.getElementById('frequency').value;
    const duration     = document.getElementById('duration').value;
    const instructions = document.getElementById('instructions').value;

    // KB / inventory FK data
    const drug_generic_id   = document.getElementById('medicine_drug_generic_id').value;
    const inventory_item_id = document.getElementById('medicine_inventory_item_id').value;
    const strength_value    = document.getElementById('medicine_strength_value').value;
    const strength_unit     = document.getElementById('medicine_strength_unit').value;
    const form              = document.getElementById('medicine_form').value;

    document.getElementById('no-meds')?.remove();

    const inStockBadge = inventory_item_id
        ? '<span style="font-size:11px;background:#d1fae5;color:#065f46;padding:2px 6px;border-radius:999px;margin-left:6px;">In stock</span>'
        : '';

    const li = document.createElement('li');
    li.innerHTML = `
        <span><strong>${medicine}</strong>${inStockBadge}
        — ${dosage}, ${frequency}, ${duration}
        ${instructions ? '(' + instructions + ')' : ''}</span>
        <button type="button" onclick="removeMedicine(this)">❌</button>

        <input type="hidden" name="medicines[${medicineIndex}][medicine]"           value="${medicine}">
        <input type="hidden" name="medicines[${medicineIndex}][dosage]"             value="${dosage}">
        <input type="hidden" name="medicines[${medicineIndex}][frequency]"          value="${frequency}">
        <input type="hidden" name="medicines[${medicineIndex}][duration]"           value="${duration}">
        <input type="hidden" name="medicines[${medicineIndex}][instructions]"       value="${instructions}">
        <input type="hidden" name="medicines[${medicineIndex}][drug_generic_id]"    value="${drug_generic_id}">
        <input type="hidden" name="medicines[${medicineIndex}][inventory_item_id]"  value="${inventory_item_id}">
        <input type="hidden" name="medicines[${medicineIndex}][strength_value]"     value="${strength_value}">
        <input type="hidden" name="medicines[${medicineIndex}][strength_unit]"      value="${strength_unit}">
        <input type="hidden" name="medicines[${medicineIndex}][form]"               value="${form}">
    `;

    document.getElementById('medicine-list').appendChild(li);
    medicineIndex++;

    ['medicine','dosage','frequency','duration','instructions'].forEach(id => {
        document.getElementById(id).value = '';
    });
    ['medicine_drug_generic_id','medicine_inventory_item_id','medicine_strength_value','medicine_strength_unit','medicine_form'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('drug-results').style.display = 'none';
}

function removeMedicine(btn) {
    btn.closest('li').remove();
}

function showPreview() {
    document.getElementById('preview-notes').innerText =
        document.querySelector('textarea[name="notes"]').value || '—';

    const tbody = document.getElementById('preview-medicines');
    tbody.innerHTML = '';

    document.querySelectorAll('#medicine-list li').forEach(li => {
        const inputs = li.querySelectorAll('input');
        if (!inputs.length) return;

        tbody.innerHTML += `
            <tr>
                <td>${inputs[0].value}</td>
                <td>${inputs[1].value}</td>
                <td>${inputs[2].value}</td>
                <td>${inputs[3].value}</td>
                <td>${inputs[4].value}</td>
            </tr>`;
    });

    document.getElementById('preview-modal').style.display = 'block';
}

function closePreview() {
    document.getElementById('preview-modal').style.display = 'none';
}
</script>

</div> {{-- end .casesheet-wrapper --}}

<script>
async function generatePrescriptionAI() {
    const box = document.getElementById('ai-prescription-box');

    box.innerText = '🧠 Senior vet reviewing case, diagnostics & treatment context...';

    try {
        const res = await fetch(
            `/vet/ai/prescription-support/{{ $appointment->id }}`,
            {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }
        );

        const data = await res.json();

        if (!res.ok) {
            box.innerText = data.error || 'Unable to generate prescription guidance.';
            return;
        }

        box.innerText = data.guidance || 'No guidance returned.';
    } catch (e) {
        box.innerText = '⚠️ Failed to connect to prescription AI.';
    }
}

let drugSearchTimer = null;

document.getElementById('medicine').addEventListener('keyup', function() {
    const query = this.value.trim();
    const box   = document.getElementById('drug-results');

    if (query.length < 2) { box.style.display = 'none'; return; }

    clearTimeout(drugSearchTimer);
    drugSearchTimer = setTimeout(() => {
        fetch(`/vet/appointments/{{ $appointment->id }}/drug-search?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            box.innerHTML = '';

            if (!data.length) {
                // Allow free text — show hint
                box.innerHTML = `<div style="padding:10px 14px;font-size:13px;color:#6b7280;">
                    No KB match — press "Add Medicine" to add as free text</div>`;
                box.style.display = 'block';
                return;
            }

            data.forEach(drug => {
                const inStock = drug.in_inventory;
                const dot     = inStock
                    ? '<span style="color:#16a34a;font-size:11px;margin-left:6px;">● In stock</span>'
                    : '<span style="color:#9ca3af;font-size:11px;margin-left:6px;">○ Not in clinic stock</span>';

                const row = document.createElement('div');
                row.style.cssText = 'padding:10px 14px;cursor:pointer;font-size:14px;border-bottom:1px solid #f3f4f6;';
                row.innerHTML = `<strong>${drug.display_name}</strong>${dot}`;
                row.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    selectDrug(drug);
                });
                box.appendChild(row);
            });

            box.style.display = 'block';
        });
    }, 280);
});

document.getElementById('medicine').addEventListener('blur', function() {
    setTimeout(() => { document.getElementById('drug-results').style.display = 'none'; }, 200);
});

function selectDrug(drug) {
    document.getElementById('medicine').value                    = drug.display_name;
    document.getElementById('medicine_drug_generic_id').value   = drug.drug_generic_id || '';
    document.getElementById('medicine_inventory_item_id').value = drug.inventory_item_id || '';
    document.getElementById('medicine_strength_value').value    = drug.strength_value || '';
    document.getElementById('medicine_strength_unit').value     = drug.strength_unit || '';
    document.getElementById('medicine_form').value              = drug.form || '';
    document.getElementById('drug-results').style.display       = 'none';
}

</script>
@endsection
