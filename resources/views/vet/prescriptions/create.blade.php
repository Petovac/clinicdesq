@extends('layouts.vet')

@section('page-class')v-page--split @endsection

@php
    $prescription = $prescription ?? null;
    $petWeight    = $appointment->weight ?? 0;
    $petSpecies   = strtolower($appointment->pet->species ?? 'dog');
@endphp

@section('head')
<style>
    /* Drug search dropdown */
    .rx-search-wrap { position: relative; }
    #drug-results {
        position: absolute; top: 100%; left: 0; right: 0;
        background: #fff; border: 1px solid #d1d5db; border-radius: var(--radius-md);
        box-shadow: var(--shadow-md); z-index: 999; max-height: 260px;
        overflow-y: auto; display: none;
    }
    #drug-results .dr-row { padding: 10px 14px; cursor: pointer; font-size: 14px; border-bottom: 1px solid var(--border-light); }
    #drug-results .dr-row:hover { background: var(--primary-soft); }

    /* Dose calculator panel */
    .rx-dose-panel {
        background: var(--primary-soft); border: 1px solid var(--primary-border);
        border-radius: var(--radius-md); padding: 14px 16px; margin-top: 12px; display: none;
    }
    .rx-dose-panel.active { display: block; }
    .rx-dose-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 10px; }
    .rx-dose-grid > div { display: flex; flex-direction: column; }
    .rx-dose-result { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }

    /* Medicine list */
    #medicine-list { list-style: none; padding: 0; margin: 10px 0 0; }
    #medicine-list li {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 14px; background: var(--bg-soft); border: 1px solid var(--border);
        border-radius: var(--radius-md); margin-bottom: 8px; font-size: 14px;
    }
    #medicine-list li .med-info { flex: 1; min-width: 0; }
    #medicine-list li strong { color: var(--text-dark); font-weight: 600; }
    #medicine-list li .med-detail { color: var(--text-muted); font-size: 13px; }
    .badge-stock { font-size: 11px; background: var(--success-soft); color: #065f46; padding: 2px 6px; border-radius: var(--radius-full); margin-left: 6px; }
    #no-meds { font-size: 14px; color: var(--text-muted); font-style: italic; border: none; background: none; }

    /* Preview modal */
    #preview-modal { backdrop-filter: blur(2px); }
    #preview-modal > div {
        background: #fff; width: 700px; max-width: 95%; margin: 40px auto;
        padding: 26px 30px; border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg); font-family: "Times New Roman", serif; color: #000;
    }
    #preview-modal h2 { text-align: center; font-size: 22px; margin-bottom: 10px; color: #000; }
    #preview-modal table { border-collapse: collapse; width: 100%; margin-top: 8px; }
    #preview-modal th { background: #f3f4f6; font-weight: 600; }
    #preview-modal th, #preview-modal td { border: 1px solid #000; padding: 6px; text-align: left; font-size: 14px; }

    @media (max-width: 768px) { .rx-dose-grid, .rx-dose-result { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')

<div class="v-main">
    <div class="v-card">

        <h2 style="text-align:center;font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 14px;">
            {{ $prescription ? 'Edit Prescription' : 'Add Prescription' }}
        </h2>

        <p style="font-size:14px;margin:0 0 16px;">
            <strong>Pet:</strong> {{ $appointment->pet->name }}
            @if($petWeight) &nbsp;|&nbsp; <strong>Weight:</strong> {{ $petWeight }} kg @endif
            @if($petSpecies) &nbsp;|&nbsp; <strong>Species:</strong> {{ ucfirst($petSpecies) }} @endif
        </p>

        <form method="POST" action="{{ route('vet.prescription.store', $appointment->id) }}">
            @csrf

            <div class="v-form-group">
                <label>Diagnosis / Notes</label>
                <textarea name="notes" rows="3" class="v-input">{{ $prescription->notes ?? '' }}</textarea>
            </div>

            <hr class="v-divider">

            {{-- Drug search --}}
            <h4 style="font-size:15px;font-weight:600;color:var(--primary);margin:0 0 10px;">Add Medicine</h4>

            <div class="rx-search-wrap">
                <input id="medicine" class="v-input" placeholder="Type drug name..." autocomplete="off">
                <input type="hidden" id="hid_generic_id">
                <input type="hidden" id="hid_inventory_id">
                <input type="hidden" id="hid_strength_val">
                <input type="hidden" id="hid_strength_unit">
                <input type="hidden" id="hid_form">
                <div id="drug-results"></div>
            </div>

            {{-- Dose calculator --}}
            <div class="rx-dose-panel" id="dose-panel">
                <div class="rx-dose-grid">
                    <div><label>KB Dose Range</label><input id="rx-kb-range" class="v-input v-input--readonly" readonly></div>
                    <div><label>Frequency (KB)</label><input id="rx-kb-freq" class="v-input v-input--readonly" readonly></div>
                    <div><label>Routes (KB)</label><input id="rx-kb-routes" class="v-input v-input--readonly" readonly></div>
                </div>

                <div class="rx-dose-result">
                    <div><label>Dose (mg/kg)</label><input type="number" step="0.01" id="rx-dose-input" class="v-input" placeholder="Enter dose"></div>
                    <div>
                        <label>Calculated Dosage</label>
                        <div style="position:relative;">
                            <input type="number" step="0.1" id="rx-calc-qty" class="v-input" placeholder="Qty" style="padding-right:110px;">
                            <span id="rx-calc-unit" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:13px;color:var(--text-muted);pointer-events:none;"></span>
                        </div>
                    </div>
                    <div><label>Frequency</label><input id="rx-frequency" class="v-input" placeholder="e.g. BID"></div>
                </div>
                <input type="hidden" id="rx-calc-dosage">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;">
                    <div><label>Duration</label><input id="rx-duration" class="v-input" placeholder="e.g. 5 days"></div>
                    <div><label>Instructions</label><input id="rx-instructions" class="v-input" placeholder="After food, etc."></div>
                </div>

                <div style="margin-top:12px;display:flex;gap:8px;">
                    <button type="button" class="v-btn v-btn--primary v-btn--sm" onclick="addMedicine()">+ Add Medicine</button>
                    <button type="button" class="v-btn v-btn--outline v-btn--sm" onclick="clearDrugPanel()">Clear</button>
                </div>
            </div>

            <hr class="v-divider">

            {{-- Added medicines --}}
            <h4 style="font-size:15px;font-weight:600;color:var(--primary);margin:0 0 10px;">Added Medicines</h4>

            <ul id="medicine-list">
                @if($prescription && $prescription->items->count())
                    @foreach($prescription->items as $i => $item)
                        <li>
                            <div class="med-info">
                                <strong>{{ $item->medicine }}</strong>
                                @if($item->inventory_item_id)<span class="badge-stock">In stock</span>@endif
                                <div class="med-detail">
                                    {{ $item->dosage }}
                                    @if($item->frequency), {{ $item->frequency }}@endif
                                    @if($item->duration), {{ $item->duration }}@endif
                                    @if($item->instructions) — {{ $item->instructions }}@endif
                                </div>
                            </div>
                            <button type="button" class="v-btn v-btn--danger v-btn--sm" onclick="removeMedicine(this)">Remove</button>

                            <input type="hidden" name="medicines[{{ $i }}][medicine]"          value="{{ $item->medicine }}">
                            <input type="hidden" name="medicines[{{ $i }}][dosage]"            value="{{ $item->dosage }}">
                            <input type="hidden" name="medicines[{{ $i }}][frequency]"         value="{{ $item->frequency }}">
                            <input type="hidden" name="medicines[{{ $i }}][duration]"          value="{{ $item->duration }}">
                            <input type="hidden" name="medicines[{{ $i }}][instructions]"      value="{{ $item->instructions }}">
                            <input type="hidden" name="medicines[{{ $i }}][drug_generic_id]"   value="{{ $item->drug_generic_id }}">
                            <input type="hidden" name="medicines[{{ $i }}][inventory_item_id]" value="{{ $item->inventory_item_id }}">
                            <input type="hidden" name="medicines[{{ $i }}][strength_value]"    value="{{ $item->strength_value }}">
                            <input type="hidden" name="medicines[{{ $i }}][strength_unit]"     value="{{ $item->strength_unit }}">
                            <input type="hidden" name="medicines[{{ $i }}][form]"              value="{{ $item->form }}">
                        </li>
                    @endforeach
                @else
                    <li id="no-meds">No medicines added yet</li>
                @endif
            </ul>

            <hr class="v-divider">

            <div style="display:flex;gap:10px;">
                <button type="button" class="v-btn v-btn--outline" onclick="showPreview()">Preview for Client</button>
                <button type="submit" class="v-btn v-btn--primary">Save Prescription</button>
            </div>
        </form>
    </div>
</div>

{{-- RIGHT: AI Panel --}}
<div class="v-aside">
    <div class="v-card v-card--compact" style="background:var(--bg-soft);position:sticky;top:calc(var(--header-h) + 24px);">
        <h3 style="margin:0 0 8px;font-size:16px;font-weight:600;">AI Prescription Support</h3>
        <p style="font-size:13px;color:var(--text-muted);line-height:1.5;margin:0 0 10px;">
            AI-assisted suggestions based on the case sheet and diagnostics.
            These are <strong>not</strong> prescriptions and must be reviewed by the veterinarian.
        </p>
        <div id="ai-prescription-box" style="padding:14px;background:#fff;border:1px solid var(--border);border-radius:var(--radius-md);font-size:14px;white-space:pre-wrap;min-height:120px;">
            No prescription suggestions generated yet.
        </div>
        <button type="button" class="v-btn v-btn--primary v-btn--block" style="margin-top:12px;" onclick="generatePrescriptionAI()">
            Generate Suggestions
        </button>
    </div>
</div>

@php
    $org = $appointment->clinic->organisation ?? null;
    $clinic = $appointment->clinic;
    $vet = $appointment->vet;
    $logoUrl = $org && $org->logo_path ? asset('storage/' . $org->logo_path) : null;
@endphp

{{-- PREVIEW MODAL --}}
<div id="preview-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;overflow-y:auto;">
    <div style="background:#fff;width:700px;max-width:95%;margin:30px auto;padding:0;border-radius:12px;box-shadow:0 25px 60px rgba(0,0,0,0.3);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">

        {{-- Header with branding --}}
        <div style="display:flex;justify-content:space-between;align-items:center;padding:24px 30px 16px;border-bottom:3px solid #2563eb;">
            <div>
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $org->name ?? '' }}" style="max-height:50px;max-width:120px;margin-bottom:4px;">
                @endif
                <div style="font-weight:700;font-size:14px;color:#1a1a1a;">{{ $org->name ?? '' }}</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:17px;font-weight:700;color:#2563eb;">{{ $clinic->name ?? '' }}</div>
                <div style="font-size:11px;color:#6b7280;">{{ $clinic->address ?? '' }}, {{ $clinic->city ?? '' }} {{ $clinic->pincode ?? '' }}</div>
                @if($clinic->phone ?? null)
                    <div style="font-size:11px;color:#6b7280;">{{ $clinic->phone }}</div>
                @endif
            </div>
        </div>

        <div style="padding:20px 30px 24px;">
            <div style="display:inline-block;background:#2563eb;color:#fff;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;letter-spacing:.5px;margin-bottom:14px;">PRESCRIPTION</div>

            {{-- Patient info card --}}
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;margin-bottom:14px;display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:13px;">
                <div><strong>Patient:</strong> {{ $appointment->pet->name }} ({{ ucfirst($appointment->pet->species ?? '') }})</div>
                <div><strong>Date:</strong> {{ now()->format('d M Y') }}</div>
                <div><strong>Breed:</strong> {{ $appointment->pet->breed ?? '—' }}</div>
                <div><strong>Weight:</strong> {{ $appointment->weight ? $appointment->weight.' kg' : '—' }}</div>
                <div><strong>Parent:</strong> {{ $appointment->pet->petParent->name ?? '—' }}</div>
                <div><strong>Doctor:</strong> {{ $vet->name ?? '—' }}</div>
            </div>

            {{-- Notes --}}
            <div id="preview-notes-wrap" style="display:none;background:#eff6ff;border-left:3px solid #2563eb;padding:10px 14px;margin-bottom:14px;font-size:12px;border-radius:0 6px 6px 0;">
                <strong>Notes:</strong> <span id="preview-notes"></span>
            </div>

            {{-- Medicines table --}}
            <table style="width:100%;border-collapse:separate;border-spacing:0;">
                <thead>
                    <tr>
                        <th style="background:#2563eb;color:#fff;padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;border-radius:8px 0 0 0;">#</th>
                        <th style="background:#2563eb;color:#fff;padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Medicine</th>
                        <th style="background:#2563eb;color:#fff;padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Dosage</th>
                        <th style="background:#2563eb;color:#fff;padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Frequency</th>
                        <th style="background:#2563eb;color:#fff;padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Duration</th>
                        <th style="background:#2563eb;color:#fff;padding:10px 12px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.5px;border-radius:0 8px 0 0;">Instructions</th>
                    </tr>
                </thead>
                <tbody id="preview-medicines"></tbody>
            </table>

            {{-- Footer --}}
            <div style="margin-top:30px;display:flex;justify-content:space-between;align-items:flex-end;">
                <div style="font-size:11px;color:#94a3b8;">Preview — {{ now()->format('d M Y, h:i A') }}</div>
                <div style="text-align:right;">
                    <div style="border:2px solid #333;display:inline-block;padding:8px 16px;text-align:center;font-size:12px;border-radius:4px;">
                        <div style="font-weight:bold;font-size:13px;">{{ $vet->name ?? '' }}</div>
                        @if($vet->degree ?? null)
                            <div style="color:#555;">{{ $vet->degree }}</div>
                        @endif
                        @if($vet->registration_number ?? null)
                            <div style="color:#555;">Reg. No: {{ $vet->registration_number }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div style="margin-top:16px;text-align:right;">
                <button class="v-btn v-btn--outline v-btn--sm" onclick="closePreview()">Close Preview</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const petWeight  = {{ $petWeight }};
const petSpecies = '{{ $petSpecies }}';
let medicineIndex = {{ $prescription ? $prescription->items->count() : 0 }};

let selectedDrug = null;
let drugSearchTimer = null;

document.getElementById('medicine').addEventListener('input', function() {
    const query = this.value.trim();
    const box   = document.getElementById('drug-results');

    selectedDrug = null;
    document.getElementById('dose-panel').classList.remove('active');

    if (query.length < 2) { box.style.display = 'none'; return; }

    clearTimeout(drugSearchTimer);
    drugSearchTimer = setTimeout(() => {
        fetch(`/vet/appointments/{{ $appointment->id }}/drug-search?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            box.innerHTML = '';

            if (!data.length) {
                box.innerHTML = '<div class="dr-row" style="color:var(--text-muted);">No KB match — press "Add Medicine" to add as free text</div>';
                box.style.display = 'block';
                return;
            }

            data.forEach(drug => {
                const dot = drug.in_inventory
                    ? '<span style="color:var(--success);font-size:11px;margin-left:6px;">● In stock</span>'
                    : '<span style="color:var(--text-light);font-size:11px;margin-left:6px;">○ Not in stock</span>';

                const row = document.createElement('div');
                row.className = 'dr-row';
                row.innerHTML = `<strong>${drug.display_name}</strong>${dot}`;
                row.addEventListener('mousedown', (e) => { e.preventDefault(); selectDrug(drug); });
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
    selectedDrug = drug;

    document.getElementById('medicine').value       = drug.display_name;
    document.getElementById('hid_generic_id').value  = drug.drug_generic_id || '';
    document.getElementById('hid_inventory_id').value= drug.inventory_item_id || '';
    document.getElementById('hid_strength_val').value= drug.strength_value || '';
    document.getElementById('hid_strength_unit').value= drug.strength_unit || '';
    document.getElementById('hid_form').value        = drug.form || '';
    document.getElementById('drug-results').style.display = 'none';

    const panel = document.getElementById('dose-panel');
    const d = drug.dosage;

    if (d) {
        document.getElementById('rx-kb-range').value  = `${d.dose_min} – ${d.dose_max} ${d.dose_unit}`;
        document.getElementById('rx-kb-freq').value   = (d.frequencies || []).join(', ') || '-';
        document.getElementById('rx-kb-routes').value = (d.routes || []).join(', ') || '-';
        document.getElementById('rx-frequency').value = d.frequencies?.[0] || '';

        if (d.dose_unit === 'mg/kg' || d.dose_unit === 'mcg/kg') {
            const mid = ((d.dose_min + d.dose_max) / 2);
            document.getElementById('rx-dose-input').value = mid;
            calculatePrescriptionDose();
        } else if (d.dose_unit === 'topical') {
            document.getElementById('rx-dose-input').value = '';
            document.getElementById('rx-calc-qty').value = '';
            document.getElementById('rx-calc-unit').textContent = 'As directed';
            document.getElementById('rx-calc-dosage').value = 'Apply as directed';
        }
    } else {
        document.getElementById('rx-kb-range').value  = 'Not in KB';
        document.getElementById('rx-kb-freq').value   = '-';
        document.getElementById('rx-kb-routes').value = '-';
        document.getElementById('rx-dose-input').value = '';
        document.getElementById('rx-calc-qty').value = '';
        document.getElementById('rx-calc-unit').textContent = '';
        document.getElementById('rx-calc-dosage').value = '';
        document.getElementById('rx-frequency').value = '';
    }

    document.getElementById('rx-duration').value     = '';
    document.getElementById('rx-instructions').value = '';
    panel.classList.add('active');
    document.getElementById('rx-dose-input').focus();
}

function getCalcUnit() {
    if (!selectedDrug) return '';
    const form = selectedDrug.form;
    if (form === 'tablet' || form === 'capsule') return 'tabs';
    if (form === 'syrup' || form === 'fluid') return 'mL';
    if (form === 'ointment' || form === 'cream' || form === 'shampoo' || form === 'drops') return '';
    return '';
}

function calculatePrescriptionDose() {
    if (!selectedDrug) return;

    const dosePerKg   = parseFloat(document.getElementById('rx-dose-input').value);
    const strengthVal = parseFloat(selectedDrug.strength_value);
    const form        = selectedDrug.form;
    const d           = selectedDrug.dosage;
    const doseUnit    = d?.dose_unit || 'mg/kg';

    const qtyEl  = document.getElementById('rx-calc-qty');
    const unitEl = document.getElementById('rx-calc-unit');
    const hidEl  = document.getElementById('rx-calc-dosage');

    if (!dosePerKg || !petWeight) {
        qtyEl.value = ''; unitEl.textContent = ''; hidEl.value = '';
        return;
    }

    if (doseUnit === 'mg/kg') {
        const totalMg = dosePerKg * petWeight;

        if ((form === 'tablet' || form === 'capsule') && strengthVal) {
            const tabs = (totalMg / strengthVal).toFixed(1);
            qtyEl.value = tabs;
            unitEl.textContent = `tabs · ${totalMg.toFixed(0)} mg`;
            hidEl.value = `${tabs} tabs (${totalMg.toFixed(0)} mg)`;
        } else if ((form === 'syrup' || form === 'fluid') && strengthVal) {
            const ml = (totalMg / strengthVal).toFixed(1);
            qtyEl.value = ml;
            unitEl.textContent = `mL · ${totalMg.toFixed(0)} mg`;
            hidEl.value = `${ml} mL (${totalMg.toFixed(0)} mg)`;
        } else if (form === 'ointment' || form === 'cream' || form === 'shampoo' || form === 'drops') {
            qtyEl.value = ''; unitEl.textContent = 'As directed'; hidEl.value = 'As directed';
        } else {
            qtyEl.value = totalMg.toFixed(1);
            unitEl.textContent = 'mg';
            hidEl.value = `${totalMg.toFixed(1)} mg`;
        }
    } else if (doseUnit === 'mcg/kg') {
        const totalMcg = dosePerKg * petWeight;
        const totalMg  = totalMcg / 1000;
        if (strengthVal) {
            const ml = (totalMg / strengthVal).toFixed(2);
            qtyEl.value = ml;
            unitEl.textContent = `mL · ${totalMcg.toFixed(0)} mcg`;
            hidEl.value = `${ml} mL (${totalMcg.toFixed(0)} mcg)`;
        } else {
            qtyEl.value = totalMcg.toFixed(0);
            unitEl.textContent = 'mcg';
            hidEl.value = `${totalMcg.toFixed(0)} mcg`;
        }
    } else if (doseUnit === 'topical') {
        qtyEl.value = ''; unitEl.textContent = 'As directed'; hidEl.value = 'As directed';
    } else {
        qtyEl.value = dosePerKg;
        unitEl.textContent = doseUnit;
        hidEl.value = `${dosePerKg} ${doseUnit}`;
    }
}

/* Reverse calculate: when user edits qty, recalculate mg and mg/kg */
function reverseCalcFromQty() {
    if (!selectedDrug) return;

    const qty         = parseFloat(document.getElementById('rx-calc-qty').value);
    const strengthVal = parseFloat(selectedDrug.strength_value);
    const form        = selectedDrug.form;
    const d           = selectedDrug.dosage;
    const doseUnit    = d?.dose_unit || 'mg/kg';
    const unitEl      = document.getElementById('rx-calc-unit');
    const hidEl       = document.getElementById('rx-calc-dosage');
    const doseInput   = document.getElementById('rx-dose-input');

    if (!qty || !petWeight) return;

    if (doseUnit === 'mg/kg') {
        if ((form === 'tablet' || form === 'capsule') && strengthVal) {
            const totalMg = qty * strengthVal;
            const mgPerKg = totalMg / petWeight;
            unitEl.textContent = `tabs · ${totalMg.toFixed(0)} mg`;
            hidEl.value = `${qty} tabs (${totalMg.toFixed(0)} mg)`;
            doseInput.value = mgPerKg.toFixed(2);
        } else if ((form === 'syrup' || form === 'fluid') && strengthVal) {
            const totalMg = qty * strengthVal;
            const mgPerKg = totalMg / petWeight;
            unitEl.textContent = `mL · ${totalMg.toFixed(0)} mg`;
            hidEl.value = `${qty} mL (${totalMg.toFixed(0)} mg)`;
            doseInput.value = mgPerKg.toFixed(2);
        } else {
            const mgPerKg = qty / petWeight;
            unitEl.textContent = 'mg';
            hidEl.value = `${qty} mg`;
            doseInput.value = mgPerKg.toFixed(2);
        }
    } else if (doseUnit === 'mcg/kg') {
        if (strengthVal) {
            const totalMg  = qty * strengthVal;
            const totalMcg = totalMg * 1000;
            const mcgPerKg = totalMcg / petWeight;
            unitEl.textContent = `mL · ${totalMcg.toFixed(0)} mcg`;
            hidEl.value = `${qty} mL (${totalMcg.toFixed(0)} mcg)`;
            doseInput.value = mcgPerKg.toFixed(2);
        }
    }
}

document.getElementById('rx-dose-input').addEventListener('input', calculatePrescriptionDose);
document.getElementById('rx-calc-qty').addEventListener('input', reverseCalcFromQty);

function addMedicine() {
    const medicineName = document.getElementById('medicine').value.trim();
    if (!medicineName) { alert('Select or type a drug name first.'); return; }

    let dosage, frequency, duration, instructions;

    if (document.getElementById('dose-panel').classList.contains('active')) {
        dosage       = document.getElementById('rx-calc-dosage').value || document.getElementById('rx-dose-input').value;
        frequency    = document.getElementById('rx-frequency').value;
        duration     = document.getElementById('rx-duration').value;
        instructions = document.getElementById('rx-instructions').value;
    } else {
        dosage = frequency = duration = instructions = '';
    }

    const drug_generic_id   = document.getElementById('hid_generic_id').value;
    const inventory_item_id = document.getElementById('hid_inventory_id').value;
    const strength_value    = document.getElementById('hid_strength_val').value;
    const strength_unit     = document.getElementById('hid_strength_unit').value;
    const form              = document.getElementById('hid_form').value;

    document.getElementById('no-meds')?.remove();

    const stockBadge = inventory_item_id ? '<span class="badge-stock">In stock</span>' : '';

    const detailParts = [dosage, frequency, duration].filter(Boolean).join(', ');
    const instrPart   = instructions ? ` — ${instructions}` : '';

    const li = document.createElement('li');
    li.innerHTML = `
        <div class="med-info">
            <strong>${medicineName}</strong>${stockBadge}
            <div class="med-detail">${detailParts}${instrPart}</div>
        </div>
        <button type="button" class="v-btn v-btn--danger v-btn--sm" onclick="removeMedicine(this)">Remove</button>
        <input type="hidden" name="medicines[${medicineIndex}][medicine]"          value="${medicineName}">
        <input type="hidden" name="medicines[${medicineIndex}][dosage]"            value="${dosage}">
        <input type="hidden" name="medicines[${medicineIndex}][frequency]"         value="${frequency}">
        <input type="hidden" name="medicines[${medicineIndex}][duration]"          value="${duration}">
        <input type="hidden" name="medicines[${medicineIndex}][instructions]"      value="${instructions}">
        <input type="hidden" name="medicines[${medicineIndex}][drug_generic_id]"   value="${drug_generic_id}">
        <input type="hidden" name="medicines[${medicineIndex}][inventory_item_id]" value="${inventory_item_id}">
        <input type="hidden" name="medicines[${medicineIndex}][strength_value]"    value="${strength_value}">
        <input type="hidden" name="medicines[${medicineIndex}][strength_unit]"     value="${strength_unit}">
        <input type="hidden" name="medicines[${medicineIndex}][form]"              value="${form}">
    `;

    document.getElementById('medicine-list').appendChild(li);
    medicineIndex++;
    clearDrugPanel();
}

function clearDrugPanel() {
    document.getElementById('medicine').value = '';
    ['hid_generic_id','hid_inventory_id','hid_strength_val','hid_strength_unit','hid_form'].forEach(id => {
        document.getElementById(id).value = '';
    });
    ['rx-dose-input','rx-calc-qty','rx-calc-dosage','rx-frequency','rx-duration','rx-instructions','rx-kb-range','rx-kb-freq','rx-kb-routes'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('rx-calc-unit').textContent = '';
    document.getElementById('dose-panel').classList.remove('active');
    selectedDrug = null;
    document.getElementById('medicine').focus();
}

function removeMedicine(btn) { btn.closest('li').remove(); }

function showPreview() {
    const notes = document.querySelector('textarea[name="notes"]').value;
    const notesWrap = document.getElementById('preview-notes-wrap');
    const notesEl = document.getElementById('preview-notes');
    if (notes) {
        notesEl.innerText = notes;
        notesWrap.style.display = 'block';
    } else {
        notesWrap.style.display = 'none';
    }

    const tbody = document.getElementById('preview-medicines');
    tbody.innerHTML = '';
    const tdStyle = 'padding:10px 12px;font-size:13px;border-bottom:1px solid #f1f5f9;';

    let idx = 0;
    document.querySelectorAll('#medicine-list li').forEach(li => {
        const inputs = li.querySelectorAll('input[type="hidden"]');
        if (!inputs.length) return;
        idx++;
        tbody.innerHTML += `<tr>
            <td style="${tdStyle}">${idx}</td>
            <td style="${tdStyle}font-weight:600;">${inputs[0].value}</td>
            <td style="${tdStyle}">${inputs[1].value || '—'}</td>
            <td style="${tdStyle}">${inputs[2].value || '—'}</td>
            <td style="${tdStyle}">${inputs[3].value || '—'}</td>
            <td style="${tdStyle}">${inputs[4].value || ''}</td>
        </tr>`;
    });

    document.getElementById('preview-modal').style.display = 'block';
}
function closePreview() { document.getElementById('preview-modal').style.display = 'none'; }

async function generatePrescriptionAI() {
    const box = document.getElementById('ai-prescription-box');
    box.innerText = 'Generating suggestions...';
    try {
        const res = await fetch(`/vet/ai/prescription-support/{{ $appointment->id }}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        });
        const data = await res.json();
        box.innerText = res.ok ? (data.guidance || 'No guidance returned.') : (data.error || 'Unable to generate.');
    } catch (e) {
        box.innerText = 'Failed to connect to prescription AI.';
    }
}
</script>
@endsection
