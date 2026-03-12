@extends('layouts.vet')

@section('content')

<style>

/* ===== Base Layout ===== */

body{
    font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Inter,sans-serif;
    background:#f3f4f6;
    color:#111827;
}

.casesheet-wrapper{
    display:flex;
    gap:28px;
    align-items:flex-start;
}

/* ===== Left Panel ===== */

.casesheet-left{
    flex:1;
}

/* ===== Right AI Panel ===== */

.casesheet-right{
    width:360px;
    position:sticky;
    top:90px;
    height:calc(100vh - 120px);
    overflow-y:auto;
}

/* ===== Card ===== */

.card{
    background:#ffffff;
    border-radius:12px;
    border:1px solid #e5e7eb;
    padding:28px;
    box-shadow:0 6px 20px rgba(0,0,0,0.06);
}

/* ===== Headings ===== */

h2{
    font-size:22px;
    font-weight:600;
    margin-bottom:10px;
}

h4{
    font-size:15px;
    font-weight:600;
    margin-top:18px;
    margin-bottom:8px;
    color:#2563eb;
}

/* ===== Text ===== */

p{
    font-size:14px;
    color:#374151;
}

hr{
    border:none;
    border-top:1px solid #e5e7eb;
    margin:22px 0;
}

/* ===== Labels ===== */

label{
    display:block;
    font-size:13px;
    font-weight:600;
    margin-top:14px;
    margin-bottom:5px;
    color:#374151;
}

/* ===== Inputs ===== */

input,
select,
textarea{
    width:100%;
    padding:9px 11px;
    border-radius:7px;
    border:1px solid #d1d5db;
    font-size:14px;
    background:#fff;
    transition:all 0.15s ease;
}

textarea{
    resize:vertical;
}

input:focus,
select:focus,
textarea:focus{
    outline:none;
    border-color:#2563eb;
    box-shadow:0 0 0 2px rgba(37,99,235,0.15);
}

/* ===== Dose Calculator Grid ===== */

#dose-calculator{
    background:#f9fafb;
    border:1px solid #e5e7eb;
    padding:14px;
    border-radius:8px;
}

#dose-info{
    font-size:13px;
    margin-bottom:8px;
    color:#374151;
}

/* ===== Treatments List ===== */

#treatment-list{
    margin-top:10px;
    padding-left:18px;
    font-size:14px;
}

#treatment-list li{
    margin-bottom:5px;
}

/* ===== Buttons ===== */

button{
    font-size:13px;
    padding:8px 14px;
    border-radius:7px;
    border:1px solid transparent;
    cursor:pointer;
    transition:all 0.15s ease;
}

button:hover{
    transform:translateY(-1px);
}

button[type="submit"]{
    background:#2563eb;
    color:#fff;
}

button[type="submit"]:hover{
    background:#1e40af;
}

button[type="button"]{
    background:#f3f4f6;
    border:1px solid #e5e7eb;
}

button[type="button"]:hover{
    background:#e5e7eb;
}

/* ===== AI Rewrite Buttons ===== */

textarea + button{
    margin-top:6px;
    font-size:12px;
    padding:4px 8px;
    background:#f9fafb;
}

/* ===== AI Panel ===== */

.casesheet-right > div{
    background:#ffffff;
    border:1px solid #e5e7eb;
    border-radius:10px;
    padding:18px;
}

#ai-insights-output{
    white-space:pre-wrap;
    font-size:13px;
    background:#f9fafb;
    border:1px solid #e5e7eb;
    border-radius:7px;
    padding:12px;
    margin-top:10px;
    min-height:80px;
}

/* ===== Modal ===== */

#casesheet-preview-modal{
    backdrop-filter:blur(3px);
}

#casesheet-preview-modal > div{
    background:#ffffff;
    width:760px;
    max-width:95%;
    margin:40px auto;
    padding:28px;
    border-radius:10px;
    box-shadow:0 18px 40px rgba(0,0,0,0.35);
    font-family:"Times New Roman",serif;
}

#casesheet-preview-modal h2{
    text-align:center;
    font-size:22px;
}

#casesheet-preview-modal li{
    margin-bottom:10px;
    font-size:14px;
}

#casesheet-preview-modal hr{
    border-top:1px solid #000;
}

/* ===== Links ===== */

a{
    font-size:13px;
    color:#6b7280;
    text-decoration:none;
}

a:hover{
    text-decoration:underline;
}

/* ===== Mobile ===== */

@media(max-width:1024px){

    .casesheet-wrapper{
        flex-direction:column;
    }

    .casesheet-right{
        width:100%;
        position:relative;
        height:auto;
        top:0;
    }

}

@media(max-width:640px){

    .card{
        padding:20px;
    }

    button{
        width:100%;
        margin-bottom:8px;
    }

}

/* ===== Drug Section Layout ===== */

.drug-grid-2{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
    margin-top:10px;
}

.drug-grid-3{
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:14px;
    margin-top:10px;
}

/* Mobile fallback */

@media(max-width:640px){

    .drug-grid-2{
        grid-template-columns:1fr;
    }

    .drug-grid-3{
        grid-template-columns:1fr;
    }

}

.drug-grid-2 label,
.drug-grid-3 label{
    margin-top:0;
}

/* ===== Readonly Inputs ===== */

input[readonly]{
    background:#f3f4f6;
    color:#374151;
    border:1px solid #e5e7eb;
    cursor:not-allowed;
}

/* optional: slightly darker text for clarity */

input[readonly]::placeholder{
    color:#6b7280;
}

input[readonly]:hover{
    background:#e5e7eb;
}

</style>

<div class="casesheet-wrapper">

    <div class="casesheet-left">
        <div class="card">

        @php
            $pet = $appointment->pet;

            $petSummaryParts = [];

            // Age (from accessor)
            if ($appointment->calculated_age_at_visit) {
                $petSummaryParts[] = $appointment->calculated_age_at_visit;
            }

            // Gender
            if ($pet->gender) {
                $petSummaryParts[] = ucfirst($pet->gender);
            }

            // Breed
            if ($pet->breed) {
                $petSummaryParts[] = ucfirst($pet->breed);
            }

            // Weight
            if ($appointment->weight) {
                $petSummaryParts[] = $appointment->weight . ' kg';
            }

            $petSummary = implode(' · ', $petSummaryParts);
        @endphp

    <h2>
        @if($caseSheet)
            Edit Case Sheet
        @else
            Add Case Sheet
        @endif
    </h2>

    <p>
        <strong>Pet:</strong>
        {{ ucfirst($pet->name) }}
        @if($petSummary)
            · {{ $petSummary }}
        @endif
        <br>

        <strong>Appointment Date:</strong>
        {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A') }}
    </p>

    <hr>

    <form method="POST"
          action="{{ route('vet.casesheet.store', $appointment->id) }}">
        @csrf

        {{-- Helper for textarea + AI --}}
        @php
            function aiTextarea($name, $label, $value = '', $rows = 3, $placeholder = '') {
                return "
                <label>{$label}</label>
                <div style='margin-bottom:14px;'>
                    <textarea
                        name='{$name}'
                        id='{$name}'
                        rows='{$rows}'
                        placeholder='{$placeholder}'
                        style='width:100%;padding:8px;'
                    >{$value}</textarea>

                    <button type='button'
                        onclick=\"refineField('{$name}')\"
                        style='
                            margin-top:6px;
                            font-size:12px;
                            padding:4px 8px;
                            border-radius:6px;
                            border:1px solid #d1d5db;
                            background:#f9fafb;
                            cursor:pointer;
                        '>
                        ✨ Rewrite with AI
                    </button>
                </div>";
            }
        @endphp

        {!! aiTextarea(
            'presenting_complaint',
            'Presenting Complaint',
            $caseSheet->presenting_complaint ?? '',
            3,
            'Chief complaint in owner’s words'
        ) !!}

        {!! aiTextarea(
            'history',
            'History',
            $caseSheet->history ?? '',
            3,
            'Relevant medical, dietary, vaccination, or illness history'
        ) !!}

        {!! aiTextarea(
            'clinical_examination',
            'Clinical Examination',
            $caseSheet->clinical_examination ?? '',
            3,
            'General condition, vitals, system-wise findings'
        ) !!}

        <label>Differentials</label>
        <textarea name="differentials" rows="2"
            placeholder="Possible differential diagnoses">{{ $caseSheet->differentials ?? '' }}</textarea>

        {!! aiTextarea(
            'diagnosis',
            'Diagnosis',
            $caseSheet->diagnosis ?? '',
            2,
            'Confirmed / provisional diagnosis'
        ) !!}

        <hr>

        <div class="drug-grid-2">

    <div>
    <label>Select Drug (Generic)</label>

    <select id="drug-generic-select">
    <option value="">Select drug</option>

    @foreach($drugGenerics as $drug)
    <option value="{{ $drug->id }}">
    {{ $drug->name }}
    </option>
    @endforeach

    </select>
    </div>


    <div>
    <label>Select Strength</label>

    <select id="drug-strength-select">
    <option value="">Select strength</option>
    </select>
    </div>

    </div>


    <div class="drug-grid-3">

    <div>
    <label>Recommended Dose</label>
    <input type="text" id="drug-dose" readonly>
    </div>

    <div>
    <label>Frequency</label>
    <input type="text" id="drug-frequency" readonly>
    </div>

    <div>
    <label>Route</label>
    <input type="text" id="drug-route" readonly>
    </div>

    </div>


    <div class="drug-grid-3">

    <div>
    <label>Dose (mg/kg)</label>
    <input type="number" step="0.001" id="dose-input">
    </div>

    <div>
    <label>System Calculated mg</label>
    <input type="number" step="0.001" id="calculated-mg">
    </div>

    <div>
    <label>Calculated Volume (ml)</label>
    <input type="number" step="0.001" id="calculated-ml">
    </div>

    </div>

    <div id="dose-warning" style="
    display:none;
    margin-top:10px;
    padding:8px 10px;
    border-radius:6px;
    font-size:13px;
    "></div>


    <h4>Treatments Performed</h4>

    <select id="treatment-select">
    <option value="">Select treatment</option>

    @foreach($priceListItems as $item)
    <option value="{{ $item->id }}">
    {{ $item->name }}
    </option>
    @endforeach

    </select>

        <button type="button" onclick="addTreatment()">Add</button>

        <ul id="treatment-list">
            @foreach($appointment->treatments as $treatment)
                <li>
                    {{ $treatment->priceItem->name }}
                </li>
            @endforeach
        </ul>

        {!! aiTextarea(
            'treatment_given',
            'Treatment Given',
            $caseSheet->treatment_given ?? '',
            3,
            'Medications, fluids, injections administered'
        ) !!}

        <label>Procedures Done</label>
        <textarea name="procedures_done" rows="2"
            placeholder="Any procedures performed during the visit">{{ $caseSheet->procedures_done ?? '' }}</textarea>

        {!! aiTextarea(
            'further_plan',
            'Further Treatment Plan',
            $caseSheet->further_plan ?? '',
            2,
            'Follow-up plan, investigations, monitoring'
        ) !!}

        {!! aiTextarea(
            'advice',
            'Advice',
            $caseSheet->advice ?? '',
            2,
            'Dietary advice, care instructions, warnings'
        ) !!}

        <br>

        <button type="button" onclick="showCaseSheetPreview()">
            👁 Preview Case Sheet (Client)
        </button>

        <button type="submit" style="margin-left:10px;">
            💾 Save Case Sheet
        </button>

        <a href="{{ route('vet.appointments.case', $appointment->id) }}"
           style="margin-left:10px;">
            Cancel
        </a>

    </form>

    </div> {{-- end .card --}}
    </div> {{-- end .casesheet-left --}}

    {{-- RIGHT SIDE : AI CLINICAL INSIGHTS --}}
    <div class="casesheet-right">

        <div style="
            border:1px solid #e5e7eb;
            border-radius:10px;
            padding:16px;
            background:#f9fafb;
        ">
    <h3 style="margin-top:0;">🧠 AI Clinical Insights</h3>

    <p style="font-size:13px;color:#374151;">
        AI-generated clinical assistance. Final diagnosis and treatment decisions
        remain the responsibility of the veterinarian.
    </p>

    <button
        type="button"
        onclick="getClinicalInsights()"
        style="
            margin-bottom:12px;
            padding:6px 12px;
            border-radius:6px;
            border:1px solid #d1d5db;
            background:#ffffff;
            cursor:pointer;
        "
    >
        Generate AI Clinical Insights
    </button>

    <div id="ai-insights-output"
         style="
            white-space:pre-wrap;
            font-size:13px;
            color:#111827;
            background:#ffffff;
            border:1px solid #e5e7eb;
            border-radius:6px;
            padding:12px;
            min-height:60px;
         ">
        Click the button to generate insights.
    </div>
</div>

</div>

</div> {{-- end .casesheet-right --}}
</div> {{-- end .casesheet-wrapper --}}

{{-- PREVIEW MODAL --}}
<div id="casesheet-preview-modal"
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.6); overflow:auto; z-index:9999;">

    <div style="background:#fff; width:800px; margin:40px auto; padding:25px;">
        <h2 style="text-align:center;">Case Sheet</h2>

        <hr>

        <p><strong>Clinic:</strong> {{ optional($appointment->clinic)->name }}</p>
        <p><strong>Doctor:</strong> {{ optional($appointment->vet)->name }}</p>
        <p><strong>Reg No:</strong> {{ optional($appointment->vet)->registration_no ?? '-' }}</p>

        <hr>

        <p><strong>Pet:</strong> {{ $appointment->pet->name }}</p>
        <p><strong>Species:</strong> {{ ucfirst($appointment->pet->species) }}</p>
        <p><strong>Date:</strong>
            {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}
        </p>

        <hr>

        <h4>Clinical Details</h4>
        <ul id="casesheet-preview-content"></ul>

        <p style="font-size:12px; margin-top:20px;">
            * This case sheet is generated electronically and is valid without a signature.
        </p>

        <div style="text-align:right;">
            <button onclick="closeCaseSheetPreview()">Close</button>
        </div>
    </div>
</div>

{{-- PREVIEW JS --}}
<script>
function showCaseSheetPreview() {
    const sections = [
        ['Presenting Complaint', 'presenting_complaint'],
        ['History', 'history'],
        ['Clinical Examination', 'clinical_examination'],
        ['Differentials', 'differentials'],
        ['Diagnosis', 'diagnosis'],
        ['Treatment Given', 'treatment_given'],
        ['Procedures Done', 'procedures_done'],
        ['Further Treatment Plan', 'further_plan'],
        ['Advice', 'advice'],
    ];

    let html = '';

    sections.forEach(([label, name]) => {
        const field = document.getElementById(name);
        if (field && field.value.trim() !== '') {
            html += `<li><strong>${label}:</strong><br>${field.value}</li>`;
        }
    });

    document.getElementById('casesheet-preview-content').innerHTML =
        html || '<li>No clinical details entered.</li>';

    document.getElementById('casesheet-preview-modal').style.display = 'block';
}

function closeCaseSheetPreview() {
    document.getElementById('casesheet-preview-modal').style.display = 'none';
}
</script>

{{-- AI REWRITE JS --}}
<script>
function refineField(field) {
    const textarea = document.getElementById(field);

    if (!textarea || !textarea.value.trim()) {
        alert('Please enter some text first');
        return;
    }

    fetch('{{ url('/vet/ai/refine') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            field: field.replace('_', ' '),
            text: textarea.value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.refined) {
            textarea.value = data.refined;
        } else {
            alert('AI could not refine the text');
        }
    })
    .catch(() => alert('AI request failed'));
}
</script>

<script>
function getClinicalInsights() {
    document.getElementById('ai-insights-output').innerText =
        'Generating AI clinical insights...';

    fetch('{{ url('/vet/ai/clinical-insights') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            presenting_complaint:
                document.getElementById('presenting_complaint')?.value || '',
            history:
                document.getElementById('history')?.value || '',
            clinical_examination:
                document.getElementById('clinical_examination')?.value || '',
            diagnosis:
                document.getElementById('diagnosis')?.value || '',

            // ✅ ADD THESE (CRITICAL)
            species: "{{ $appointment->pet->species }}",
            breed: "{{ $appointment->pet->breed }}",
            gender: "{{ $appointment->pet->gender }}",
            pet_age: "{{ $appointment->calculated_age_at_visit }}",
            body_weight: "{{ $appointment->weight }}"
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.raw) {
            document.getElementById('ai-insights-output').innerText = data.raw;
        } else {
            document.getElementById('ai-insights-output').innerText =
                'AI did not return insights.';
        }
    })
    .catch(() => {
        document.getElementById('ai-insights-output').innerText =
            'Failed to generate AI insights.';
    });
}

document.getElementById('treatment-select').addEventListener('change', function(){

const genericId = this.options[this.selectedIndex].dataset.generic;

if(!genericId){
    document.getElementById('dose-calculator').style.display='none';
    return;
}

fetch(`/vet/drug-dosage/${genericId}`)
.then(res=>res.json())
.then(data=>{

    if(!data.dosages || data.dosages.length===0){
        document.getElementById('dose-calculator').style.display='none';
        return;
    }

    const dosage = data.dosages[0];

    const weight = {{ $appointment->weight ?? 0 }};

    const dose = dosage.dose_min * weight;

    document.getElementById('dose-info').innerHTML =
        `Recommended: ${dosage.dose_min} - ${dosage.dose_max} ${dosage.dose_unit}`;

    document.getElementById('calculated-dose').value =
        dose.toFixed(2) + " mg";

    document.getElementById('dose-calculator').style.display='block';

});

});

function addTreatment(){

const id = document.getElementById('treatment-select').value;

if(!id){
    alert("Select treatment");
    return;
}

fetch(`/vet/appointments/{{ $appointment->id }}/treatment/add`,{

    method:'POST',

    headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN':'{{ csrf_token() }}'
    },

    body: JSON.stringify({
        price_list_item_id: id
    })

})
.then(res=>res.json())
.then(()=>{
    location.reload();
});

}

</script>


<script>
document.getElementById('drug-generic-select').addEventListener('change', function(){

const genericId = this.value;

if(!genericId) return;

fetch(`/vet/drug-strengths/${genericId}`)
.then(res => res.json())
.then(data => {

    const strengthSelect = document.getElementById('drug-strength-select');

    strengthSelect.innerHTML = '<option value="">Select strength</option>';

    data.forEach(function(item){

        const option = document.createElement('option');

        option.value = item.strength_value;

        option.text =
            item.strength_value + ' ' + item.strength_unit + ' (' + item.form + ')';

        strengthSelect.appendChild(option);

    });

});

});

const weight = {{ $appointment->weight ?? 0 }};

let recommendedMin = null;
let recommendedMax = null;

function calculateDose(){

    const dose = parseFloat(document.getElementById('dose-input').value);
    const strength = parseFloat(document.getElementById('drug-strength-select').value);

    if(!dose || !strength || !weight) return;

    const mg = dose * weight;
    const ml = mg / strength;

    document.getElementById('calculated-mg').value = mg.toFixed(2);
    document.getElementById('calculated-ml').value = ml.toFixed(2);

    checkDoseSafety(dose);
}

document.getElementById('dose-input').addEventListener('input', calculateDose);

document.getElementById('drug-strength-select').addEventListener('change', calculateDose);
</script>

<script>
document.getElementById('drug-generic-select').addEventListener('change', function(){

    const genericId = this.value;

    if(!genericId) return;

    fetch(`/vet/drug-dosage/${genericId}?species={{ $appointment->pet->species }}`)
    .then(res => res.json())
    .then(data => {

        if(!data.dosages || data.dosages.length === 0){
            document.getElementById('drug-dose').value =
                'Dosage guidelines not available for {{ ucfirst($appointment->pet->species) }}';
            return;
        }

        const dose = data.dosages[0];

        recommendedMin = parseFloat(dose.dose_min);
        recommendedMax = parseFloat(dose.dose_max);

        document.getElementById('drug-dose').value =
            dose.dose_min + " - " + dose.dose_max + " " + dose.dose_unit;

        document.getElementById('dose-input').value = dose.dose_min;

        document.getElementById('drug-frequency').value = dose.frequencies || '';

        document.getElementById('drug-route').value = dose.routes || '';      

    });

});
</script>

<script>

function checkDoseSafety(dose){

const warningBox = document.getElementById('dose-warning');

if(recommendedMin === null || recommendedMax === null){
    warningBox.style.display = 'none';
    return;
}

if(dose < recommendedMin){

    warningBox.style.display = 'block';
    warningBox.style.background = '#fef3c7';
    warningBox.style.border = '1px solid #fde68a';
    warningBox.innerText = '⚠ Dose below recommended range';

}

else if(dose > recommendedMax){

    warningBox.style.display = 'block';
    warningBox.style.background = '#fee2e2';
    warningBox.style.border = '1px solid #fecaca';
    warningBox.innerText = '⚠ Dose exceeds recommended range';

}

else{

    warningBox.style.display = 'block';
    warningBox.style.background = '#dcfce7';
    warningBox.style.border = '1px solid #bbf7d0';
    warningBox.innerText = '✓ Dose within recommended range';

}

}
</script>


@endsection

