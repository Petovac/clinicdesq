@extends('clinic.layout')

@section('content')
<style>
.appt-card { background:#fff; border-radius:10px; padding:24px; border:1px solid #e5e7eb; max-width:600px; margin:20px auto; }
.appt-title { font-size:20px; font-weight:700; margin-bottom:16px; }
.info-line { font-size:14px; margin-bottom:4px; color:#374151; }
.info-line strong { color:#111827; }
.form-group { margin-bottom:16px; }
.form-group label { display:block; font-size:13px; font-weight:600; margin-bottom:4px; color:#374151; }
.form-group select, .form-group input { width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:6px; font-size:14px; }
.form-group select:focus, .form-group input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 2px rgba(37,99,235,0.15); }
.slot-grid { display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; min-height:40px; }
.slot-pill {
    padding:8px 14px; border-radius:20px; font-size:13px; font-weight:600;
    border:2px solid transparent; cursor:pointer; transition:all 0.15s;
}
.slot-pill.available { background:#dcfce7; color:#166534; border-color:#bbf7d0; }
.slot-pill.available:hover { background:#bbf7d0; border-color:#16a34a; transform:scale(1.05); }
.slot-pill.selected { background:#2563eb !important; color:#fff !important; border-color:#1d4ed8 !important; }
.slot-pill.booked { background:#fee2e2; color:#991b1b; cursor:not-allowed; opacity:0.7; }
.slot-pill.past { background:#f3f4f6; color:#9ca3af; cursor:not-allowed; opacity:0.5; }
.slot-loading { color:#6b7280; font-size:13px; padding:10px 0; }
.slot-empty { color:#6b7280; font-size:13px; font-style:italic; padding:10px 0; }
.slot-legend { display:flex; gap:14px; margin-top:8px; font-size:11px; color:#6b7280; }
.slot-legend span { display:flex; align-items:center; gap:4px; }
.slot-legend .dot { width:10px; height:10px; border-radius:50%; }
.btn-create { background:#16a34a; color:#fff; padding:12px 24px; border:none; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; width:100%; }
.btn-create:hover { background:#15803d; }
.btn-create:disabled { background:#d1d5db; color:#6b7280; cursor:not-allowed; }
</style>

<div class="appt-card">
    <div class="appt-title">Create Appointment</div>

    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;margin-bottom:20px;">
        <div class="info-line"><strong>Owner:</strong> {{ $pet->petParent->name }} ({{ $pet->petParent->phone }})</div>
        <div class="info-line"><strong>Pet:</strong> {{ $pet->name }} — {{ ucfirst($pet->species ?? '') }} {{ $pet->breed ? '(' . $pet->breed . ')' : '' }}</div>
    </div>

    <form method="POST" action="{{ route('clinic.appointments.store') }}" id="apptForm">
        @csrf
        <input type="hidden" name="pet_id" value="{{ $pet->id }}">
        <input type="hidden" name="pet_parent_id" value="{{ $pet->pet_parent_id }}">
        <input type="hidden" name="scheduled_at" id="scheduled_at">

        {{-- Vet Selection --}}
        <div class="form-group">
            <label>Assign Vet</label>
            <select name="vet_id" id="vet_select">
                <option value="">— Select Doctor —</option>
                @foreach($vets as $vet)
                    <option value="{{ $vet->id }}">Dr. {{ $vet->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Date --}}
        <div class="form-group">
            <label>Appointment Date</label>
            <input type="date" id="appt_date" min="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}">
        </div>

        {{-- Slot Grid --}}
        <div class="form-group">
            <label>Select Time Slot</label>
            <div id="slot-container" class="slot-grid">
                <div class="slot-empty">Select a doctor and date to see available slots.</div>
            </div>
            <div class="slot-legend">
                <span><span class="dot" style="background:#dcfce7;border:1px solid #bbf7d0;"></span> Available</span>
                <span><span class="dot" style="background:#fee2e2;border:1px solid #fecaca;"></span> Booked</span>
                <span><span class="dot" style="background:#2563eb;"></span> Selected</span>
            </div>
            <div id="selected-time" style="margin-top:8px;font-size:13px;font-weight:600;color:#2563eb;display:none;"></div>
        </div>

        {{-- Weight --}}
        <div class="form-group">
            <label>Weight (kg) — optional</label>
            <input type="number" step="0.01" name="weight" placeholder="Enter weight">
        </div>

        <button type="submit" class="btn-create" id="submitBtn" disabled>
            Select a doctor and time slot
        </button>
    </form>
</div>

<script>
let selectedSlot = null;

function loadSlots() {
    const vetId = document.getElementById('vet_select').value;
    const date = document.getElementById('appt_date').value;
    const container = document.getElementById('slot-container');

    if (!vetId) {
        container.innerHTML = '<div class="slot-empty">Select a doctor to see available slots.</div>';
        clearSelection();
        return;
    }
    if (!date) {
        container.innerHTML = '<div class="slot-empty">Select a date.</div>';
        clearSelection();
        return;
    }

    container.innerHTML = '<div class="slot-loading">Loading available slots...</div>';

    fetch('/clinic/appointments/slots?vet_id=' + vetId + '&date=' + date)
        .then(r => r.json())
        .then(slots => {
            if (!slots.length) {
                container.innerHTML = '<div class="slot-empty">No slots available — doctor may be off on this day.</div>';
                clearSelection();
                return;
            }

            container.innerHTML = '';
            slots.forEach(slot => {
                const pill = document.createElement('div');
                pill.className = 'slot-pill ' + (slot.available ? 'available' : (slot.on_break ? 'booked' : (slot.booked ? 'booked' : 'past')));
                pill.textContent = slot.display;
                pill.dataset.time = slot.time;

                if (slot.available) {
                    pill.addEventListener('click', () => selectSlot(pill, slot));
                }

                container.appendChild(pill);
            });
        })
        .catch(() => {
            container.innerHTML = '<div class="slot-empty">Failed to load slots.</div>';
        });
}

function selectSlot(pill, slot) {
    document.querySelectorAll('.slot-pill.selected').forEach(p => p.classList.remove('selected'));
    pill.classList.add('selected');
    selectedSlot = slot.time;

    const date = document.getElementById('appt_date').value;
    document.getElementById('scheduled_at').value = date + ' ' + slot.time + ':00';

    document.getElementById('selected-time').style.display = 'block';
    document.getElementById('selected-time').textContent = 'Selected: ' + slot.display;

    const btn = document.getElementById('submitBtn');
    btn.disabled = false;
    btn.textContent = 'Create Appointment';
}

function clearSelection() {
    selectedSlot = null;
    document.getElementById('scheduled_at').value = '';
    document.getElementById('selected-time').style.display = 'none';
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = 'Select a doctor and time slot';
}

document.getElementById('vet_select').addEventListener('change', function() {
    clearSelection();
    loadSlots();
});

document.getElementById('appt_date').addEventListener('change', function() {
    clearSelection();
    loadSlots();
});

document.getElementById('apptForm').addEventListener('submit', function(e) {
    if (!document.getElementById('scheduled_at').value) {
        e.preventDefault();
        alert('Please select a time slot.');
    }
});
</script>

@endsection
