@extends('layouts.vet')

@section('head')
<style>
    .sched-wrap { max-width: 900px; margin: 0 auto; }
    .sched-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 20px; }
    .sched-title { font-size: 20px; font-weight: 700; margin-bottom: 6px; }
    .sched-subtitle { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }

    .day-row { display: grid; grid-template-columns: 40px 90px 1fr; gap: 12px; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border-light); }
    .day-row:last-child { border-bottom: none; }
    .day-name { font-weight: 600; font-size: 14px; color: var(--text); }
    .day-name.off { color: var(--text-muted); text-decoration: line-through; }

    .day-fields { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
    .day-field { display: flex; flex-direction: column; gap: 2px; }
    .day-field label { font-size: 10px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .day-field input, .day-field select {
        padding: 6px 8px; border: 1px solid var(--border); border-radius: var(--radius-sm);
        font-size: 13px; background: var(--bg-soft); width: auto; min-width: 80px;
    }
    .day-field input:focus, .day-field select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 2px rgba(37,99,235,0.12); }
    .day-field input[type="time"] { min-width: 100px; }

    .toggle-wrap { display: flex; align-items: center; justify-content: center; }
    .toggle-cb { width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary); }

    .sched-actions { display: flex; gap: 10px; margin-top: 20px; }
    .copy-btn { background: var(--bg-soft); border: 1px solid var(--border); padding: 8px 14px; border-radius: var(--radius-sm); font-size: 12px; font-weight: 600; cursor: pointer; color: var(--text-muted); }
    .copy-btn:hover { background: var(--primary-soft); color: var(--primary); border-color: var(--primary-border); }

    /* Break card */
    .break-card { border-radius: var(--radius-lg); padding: 20px 24px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; }
    .break-card.available { background: var(--success-soft); border: 2px solid var(--success-border); }
    .break-card.on-break { background: #fef3c7; border: 2px solid #fde68a; }
    .break-status { font-size: 16px; font-weight: 700; }
    .break-status .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 8px; }
    .break-btn { padding: 10px 20px; border-radius: var(--radius-sm); border: none; font-weight: 600; font-size: 14px; cursor: pointer; }
    .break-btn.start { background: #f59e0b; color: #fff; }
    .break-btn.start:hover { background: #d97706; }
    .break-btn.end { background: var(--success); color: #fff; }
    .break-btn.end:hover { background: #15803d; }
</style>
@endsection

@section('content')
<div class="sched-wrap">

    @if(session('success'))
    <div style="background:var(--success-soft);border:1px solid var(--success-border);padding:10px 14px;border-radius:var(--radius-sm);margin-bottom:16px;color:var(--success);font-size:13px;">
        {{ session('success') }}
    </div>
    @endif

    {{-- Break Toggle --}}
    <div class="break-card {{ $activeBreak ? 'on-break' : 'available' }}" id="breakCard">
        <div>
            <div class="break-status">
                @if($activeBreak)
                    <span class="dot" style="background:#f59e0b;"></span> On Break
                    <div style="font-size:12px;font-weight:400;color:#92400e;margin-top:4px;">
                        Since {{ $activeBreak->started_at->format('g:i A') }} ({{ $activeBreak->started_at->diffForHumans() }})
                        @if($activeBreak->reason) — {{ $activeBreak->reason }} @endif
                    </div>
                @else
                    <span class="dot" style="background:var(--success);"></span> Available
                    <div style="font-size:12px;font-weight:400;color:var(--text-muted);margin-top:4px;">
                        You are currently available for appointment bookings
                    </div>
                @endif
            </div>
        </div>
        <form method="POST" action="{{ url('/vet/schedule/break') }}" style="display:flex;gap:8px;align-items:center;">
            @csrf
            @if(!$activeBreak)
                <input name="reason" placeholder="Reason (optional)" style="padding:8px 10px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:160px;">
            @endif
            <button type="submit" class="break-btn {{ $activeBreak ? 'end' : 'start' }}">
                {{ $activeBreak ? '✓ End Break' : '☕ Take Break' }}
            </button>
        </form>
    </div>

    {{-- Schedule Config --}}
    <div class="sched-card">
        <div class="sched-title">Appointment Schedule</div>
        <div class="sched-subtitle">Configure your working hours and slot duration for each day. Patients will only be able to book during these slots.</div>

        <form method="POST" action="{{ url('/vet/schedule') }}">
            @csrf

            <div style="display:grid;grid-template-columns:40px 90px 1fr;gap:12px;padding-bottom:8px;border-bottom:2px solid var(--border);margin-bottom:4px;">
                <div style="font-size:10px;font-weight:600;color:var(--text-muted);text-align:center;">ON</div>
                <div style="font-size:10px;font-weight:600;color:var(--text-muted);">DAY</div>
                <div style="font-size:10px;font-weight:600;color:var(--text-muted);">SCHEDULE</div>
            </div>

            @foreach($grid as $dayNum => $day)
            <div class="day-row" id="day-{{ $dayNum }}">
                <div class="toggle-wrap">
                    <input type="checkbox" class="toggle-cb" name="days[{{ $dayNum }}][is_active]" value="1"
                        {{ $day['is_active'] ? 'checked' : '' }}
                        onchange="toggleDay({{ $dayNum }}, this.checked)">
                </div>

                <div class="day-name {{ !$day['is_active'] ? 'off' : '' }}" id="dayname-{{ $dayNum }}">
                    {{ $day['day_name'] }}
                </div>

                <div class="day-fields" id="fields-{{ $dayNum }}" style="{{ !$day['is_active'] ? 'opacity:0.4;pointer-events:none;' : '' }}">
                    <div class="day-field">
                        <label>Start</label>
                        <input type="time" name="days[{{ $dayNum }}][start_time]" value="{{ $day['start_time'] }}">
                    </div>
                    <div class="day-field">
                        <label>End</label>
                        <input type="time" name="days[{{ $dayNum }}][end_time]" value="{{ $day['end_time'] }}">
                    </div>
                    <div class="day-field">
                        <label>Slot Duration</label>
                        <select name="days[{{ $dayNum }}][slot_duration_minutes]">
                            @foreach([10, 15, 20, 30, 45, 60] as $mins)
                                <option value="{{ $mins }}" {{ $day['slot_duration_minutes'] == $mins ? 'selected' : '' }}>{{ $mins }} min</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="sched-actions">
                <button type="submit" class="v-btn v-btn--primary">Save Schedule</button>
                <button type="button" class="copy-btn" onclick="copyMondayToWeekdays()">Copy Monday → All Weekdays</button>
            </div>
        </form>
    </div>

    {{-- Preview --}}
    <div class="sched-card">
        <div style="font-size:14px;font-weight:600;margin-bottom:8px;">Preview Today's Slots</div>
        <div id="slot-preview" style="display:flex;flex-wrap:wrap;gap:6px;min-height:40px;">
            <span style="color:var(--text-muted);font-size:13px;">Loading...</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleDay(dayNum, active) {
    const fields = document.getElementById('fields-' + dayNum);
    const name = document.getElementById('dayname-' + dayNum);
    if (active) {
        fields.style.opacity = '1';
        fields.style.pointerEvents = 'auto';
        name.classList.remove('off');
    } else {
        fields.style.opacity = '0.4';
        fields.style.pointerEvents = 'none';
        name.classList.add('off');
    }
}

function copyMondayToWeekdays() {
    const mon = document.querySelector('#day-1');
    if (!mon) return;

    const monInputs = mon.querySelectorAll('input[type="time"], select');
    const monActive = mon.querySelector('.toggle-cb').checked;

    for (let d = 2; d <= 5; d++) {
        const row = document.querySelector('#day-' + d);
        if (!row) continue;
        const cb = row.querySelector('.toggle-cb');
        cb.checked = monActive;
        toggleDay(d, monActive);
        const inputs = row.querySelectorAll('input[type="time"], select');
        monInputs.forEach((monInput, i) => {
            if (inputs[i]) inputs[i].value = monInput.value;
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    fetch('/vet/appointments/slots?date=' + today)
        .then(r => r.json())
        .then(slots => {
            const container = document.getElementById('slot-preview');
            if (!slots.length) {
                container.innerHTML = '<span style="color:var(--text-muted);font-size:13px;">No slots today (day off).</span>';
                return;
            }
            container.innerHTML = slots.map(s => {
                let bg, color, label = '';
                if (s.on_break) { bg = '#fef3c7'; color = '#92400e'; label = '(break)'; }
                else if (s.booked) { bg = '#fee2e2'; color = '#991b1b'; label = '(booked)'; }
                else if (s.past) { bg = '#f3f4f6'; color = '#9ca3af'; label = ''; }
                else { bg = '#dcfce7'; color = '#166534'; }
                return `<span style="padding:4px 10px;border-radius:14px;font-size:12px;font-weight:600;background:${bg};color:${color};">${s.display} ${label}</span>`;
            }).join('');
        })
        .catch(() => {});
});
</script>
@endsection
