@extends('layouts.vet')

@section('head')
<style>
    .slot-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; min-height: 40px; }
    .slot-pill {
        padding: 8px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;
        border: 2px solid transparent; cursor: pointer; transition: all 0.15s;
    }
    .slot-pill.available { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
    .slot-pill.available:hover { background: #bbf7d0; border-color: #16a34a; transform: scale(1.05); }
    .slot-pill.selected { background: #2563eb !important; color: #fff !important; border-color: #1d4ed8 !important; }
    .slot-pill.booked { background: #fee2e2; color: #991b1b; cursor: not-allowed; opacity: 0.7; }
    .slot-pill.past { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; opacity: 0.5; }
    .slot-loading { color: var(--text-muted); font-size: 13px; padding: 10px 0; }
    .slot-empty { color: var(--text-muted); font-size: 13px; font-style: italic; padding: 10px 0; }
    .slot-legend { display: flex; gap: 14px; margin-top: 8px; font-size: 11px; color: var(--text-muted); }
    .slot-legend span { display: flex; align-items: center; gap: 4px; }
    .slot-legend .dot { width: 10px; height: 10px; border-radius: 50%; }
</style>
@endsection

@section('content')

<div class="v-form-card">
    <div class="v-card">
        <h2 style="text-align:center;font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 18px;">
            Create Appointment
        </h2>

        <p style="margin-bottom:6px;"><strong style="color:var(--text-dark);">Pet:</strong> {{ $pet->name }} ({{ ucfirst($pet->species ?? '') }})</p>
        <p style="margin-bottom:18px;"><strong style="color:var(--text-dark);">Pet Parent:</strong> {{ $pet->petParent->name }} ({{ $pet->petParent->phone }})</p>

        <form method="POST" action="{{ route('vet.appointments.store') }}" id="apptForm">
            @csrf
            <input type="hidden" name="pet_id" value="{{ $pet->id }}">
            <input type="hidden" name="pet_parent_id" value="{{ $pet->pet_parent_id }}">
            <input type="hidden" name="scheduled_at" id="scheduled_at" required>

            {{-- Date Picker --}}
            <div class="v-form-group">
                <label>Appointment Date</label>
                <input type="date" id="appt_date" class="v-input"
                       min="{{ now()->format('Y-m-d') }}"
                       value="{{ now()->format('Y-m-d') }}" required>
            </div>

            {{-- Slot Grid --}}
            <div class="v-form-group">
                <label>Select Time Slot</label>
                <div id="slot-container" class="slot-grid">
                    <div class="slot-loading">Loading slots...</div>
                </div>
                <div class="slot-legend">
                    <span><span class="dot" style="background:#dcfce7;border:1px solid #bbf7d0;"></span> Available</span>
                    <span><span class="dot" style="background:#fee2e2;border:1px solid #fecaca;"></span> Booked</span>
                    <span><span class="dot" style="background:#2563eb;"></span> Selected</span>
                </div>
                <div id="selected-time" style="margin-top:8px;font-size:13px;font-weight:600;color:var(--primary);display:none;"></div>
            </div>

            {{-- Weight --}}
            <div class="v-form-group">
                <label>Pet Weight (kg) <span style="color:var(--danger);">*</span></label>
                <input type="number" step="0.1" name="weight" required
                       value="{{ old('weight', $lastWeight) }}"
                       placeholder="Enter weight in kg"
                       class="v-input">
                @if($lastWeight)
                    <p class="v-form-hint">Last recorded weight: {{ $lastWeight }} kg</p>
                @endif
            </div>

            <button type="submit" class="v-btn v-btn--primary v-btn--block" id="submitBtn" disabled>
                Select a time slot to continue
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
let selectedSlot = null;

function loadSlots() {
    const date = document.getElementById('appt_date').value;
    if (!date) return;

    const container = document.getElementById('slot-container');
    container.innerHTML = '<div class="slot-loading">Loading available slots...</div>';

    fetch('/vet/appointments/slots?date=' + date)
        .then(r => r.json())
        .then(slots => {
            if (!slots.length) {
                container.innerHTML = '<div class="slot-empty">No slots available on this day (day off or schedule not configured).</div>';
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

            // Re-select if same slot still available
            if (selectedSlot) {
                const match = container.querySelector(`.slot-pill[data-time="${selectedSlot}"]`);
                if (match && match.classList.contains('available')) {
                    match.classList.add('selected');
                } else {
                    clearSelection();
                }
            }
        })
        .catch(() => {
            container.innerHTML = '<div class="slot-empty">Failed to load slots. Please try again.</div>';
        });
}

function selectSlot(pill, slot) {
    // Remove previous selection
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
    btn.textContent = 'Select a time slot to continue';
}

document.getElementById('appt_date').addEventListener('change', function() {
    clearSelection();
    loadSlots();
});

// Validate before submit
document.getElementById('apptForm').addEventListener('submit', function(e) {
    if (!document.getElementById('scheduled_at').value) {
        e.preventDefault();
        alert('Please select a time slot.');
    }
});

// Load slots on page load
document.addEventListener('DOMContentLoaded', loadSlots);
</script>
@endsection
