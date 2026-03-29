@extends('clinic.layout')

@section('content')

<style>
.appt-page { max-width: 1200px; }

/* Header */
.appt-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
.appt-title { font-size: 22px; font-weight: 700; color: #1e293b; margin: 0; }
.btn-new-appt {
    display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px;
    border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none;
    background: #16a34a; color: #fff; transition: all 0.2s;
}
.btn-new-appt:hover { background: #15803d; color: #fff; transform: translateY(-1px); }

/* Stat Cards */
.stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 28px; }
@media (max-width: 768px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
.stat-card {
    background: #fff; border-radius: 10px; padding: 18px 20px; text-align: center;
    border: 1px solid #e5e7eb; transition: all 0.2s;
}
.stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
.stat-label { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.stat-value { font-size: 28px; font-weight: 800; }
.stat-waiting { color: #f59e0b; }
.stat-consult { color: #0ea5e9; }
.stat-done { color: #22c55e; }
.stat-billing { color: #ef4444; }

/* Section Headers */
.section-bar {
    display: flex; align-items: center; gap: 10px; margin-bottom: 14px; margin-top: 8px;
}
.section-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.section-label { font-size: 15px; font-weight: 700; color: #1e293b; }
.section-count {
    font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 10px;
    background: #f1f5f9; color: #64748b;
}

/* Queue Cards */
.queue-list { display: flex; flex-direction: column; gap: 8px; margin-bottom: 28px; }
.queue-item {
    display: flex; align-items: center; justify-content: space-between;
    background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 18px;
    transition: all 0.15s;
}
.queue-item:hover { border-color: #cbd5e1; box-shadow: 0 2px 6px rgba(0,0,0,0.04); }
.queue-left { display: flex; align-items: center; gap: 12px; }
.queue-num {
    width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center;
    justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0;
}
.queue-num.waiting { background: #fef3c7; color: #92400e; }
.queue-num.consult { background: #e0f2fe; color: #0369a1; }
.queue-pet { font-size: 14px; font-weight: 600; color: #1e293b; }
.queue-owner { font-size: 12px; color: #64748b; }
.queue-right { text-align: right; }
.queue-time { font-size: 13px; font-weight: 600; color: #374151; }
.queue-elapsed { font-size: 11px; color: #9ca3af; }

/* Table */
.appt-table-wrap {
    background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden;
    margin-bottom: 28px;
}
.appt-table { width: 100%; border-collapse: collapse; }
.appt-table thead { background: #f8fafc; }
.appt-table th {
    padding: 12px 16px; font-size: 12px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px; text-align: left; border-bottom: 1px solid #e5e7eb;
}
.appt-table td { padding: 12px 16px; font-size: 14px; color: #374151; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.appt-table tbody tr:hover { background: #f8fafc; }
.appt-table tbody tr:last-child td { border-bottom: none; }

/* Status Badge */
.status-badge {
    display: inline-block; font-size: 11px; font-weight: 600; padding: 4px 10px;
    border-radius: 20px; white-space: nowrap;
}
.status-scheduled { background: #f1f5f9; color: #475569; }
.status-checked_in { background: #fef3c7; color: #92400e; }
.status-in_consultation { background: #e0f2fe; color: #0369a1; }
.status-awaiting_lab_results { background: #ede9fe; color: #6d28d9; }
.status-completed { background: #dcfce7; color: #166534; }
.status-cancelled { background: #fee2e2; color: #991b1b; }

/* Action Buttons */
.act-btn {
    display: inline-flex; align-items: center; padding: 5px 10px; border-radius: 6px;
    font-size: 12px; font-weight: 600; border: none; cursor: pointer;
    text-decoration: none; transition: all 0.15s; margin: 2px;
}
.act-checkin { background: #fef3c7; color: #92400e; }
.act-checkin:hover { background: #fde68a; }
.act-start { background: #e0f2fe; color: #0369a1; }
.act-start:hover { background: #bae6fd; }
.act-complete { background: #dcfce7; color: #166534; }
.act-complete:hover { background: #bbf7d0; }
.act-lab { background: #ede9fe; color: #6d28d9; }
.act-lab:hover { background: #ddd6fe; }
.act-bill { background: #dbeafe; color: #1e40af; }
.act-bill:hover { background: #bfdbfe; }
.act-reschedule { background: #f1f5f9; color: #475569; }
.act-reschedule:hover { background: #e2e8f0; }
.act-cancel { background: #fee2e2; color: #991b1b; }
.act-cancel:hover { background: #fecaca; }

.empty-row { text-align: center; color: #9ca3af; padding: 32px 16px !important; }

/* Billing Alert */
.billing-alert {
    background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px;
    padding: 14px 18px; margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between;
}
.billing-alert-text { font-size: 14px; font-weight: 600; color: #991b1b; }
.billing-alert-count {
    background: #ef4444; color: #fff; font-size: 12px; font-weight: 700;
    padding: 2px 8px; border-radius: 10px; margin-left: 6px;
}

/* Waiting Timer */
.timer { font-size: 12px; font-weight: 600; color: #f59e0b; font-variant-numeric: tabular-nums; }
.consult-dur { font-size: 12px; color: #64748b; }
</style>

<div class="appt-page">

    {{-- Header --}}
    <div class="appt-header">
        <h1 class="appt-title">Appointments</h1>
        @if(auth()->user()->hasPermission('appointments.create'))
        <a href="{{ route('clinic.appointments.create') }}" class="btn-new-appt">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            New Appointment
        </a>
        @endif
    </div>

    {{-- Stats --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-label">Waiting</div>
            <div class="stat-value stat-waiting">{{ $waitingCount }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">In Consultation</div>
            <div class="stat-value stat-consult">{{ $consultationCount }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Completed Today</div>
            <div class="stat-value stat-done">{{ $completedCount }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Ready for Billing</div>
            <div class="stat-value stat-billing">{{ $needsBillingCount }}</div>
        </div>
    </div>

    {{-- Waiting Queue --}}
    @php $waitingAppts = $appointments->where('status','checked_in'); @endphp
    @if($waitingAppts->count() > 0)
    <div class="section-bar">
        <span class="section-dot" style="background:#f59e0b;"></span>
        <span class="section-label">Waiting Queue</span>
        <span class="section-count">{{ $waitingAppts->count() }}</span>
    </div>
    <div class="queue-list">
        @foreach($waitingAppts as $wa)
        <div class="queue-item">
            <div class="queue-left">
                <div class="queue-num waiting">#{{ $wa->appointment_number }}</div>
                <div>
                    <div class="queue-pet">{{ $wa->pet->name ?? '—' }}</div>
                    <div class="queue-owner">{{ $wa->pet->petParent->name ?? '—' }}</div>
                </div>
            </div>
            <div class="queue-right">
                <div class="queue-time">{{ $wa->scheduled_at->format('h:i A') }}</div>
                <div class="queue-elapsed waiting-timer" data-time="{{ \Carbon\Carbon::parse($wa->checked_in_at)->toIso8601String() }}">—</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- In Consultation --}}
    @php $consultAppts = $appointments->where('status','in_consultation'); @endphp
    @if($consultAppts->count() > 0)
    <div class="section-bar">
        <span class="section-dot" style="background:#0ea5e9;"></span>
        <span class="section-label">In Consultation</span>
        <span class="section-count">{{ $consultAppts->count() }}</span>
    </div>
    <div class="queue-list">
        @foreach($consultAppts as $ca)
        <div class="queue-item">
            <div class="queue-left">
                <div class="queue-num consult">#{{ $ca->appointment_number }}</div>
                <div>
                    <div class="queue-pet">{{ $ca->pet->name ?? '—' }}</div>
                    <div class="queue-owner">Dr. {{ $ca->vet->name ?? 'Unassigned' }}</div>
                </div>
            </div>
            <div class="queue-right">
                <div class="queue-time">Started {{ \Carbon\Carbon::parse($ca->consultation_started_at)->diffForHumans() }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Ready for Billing --}}
    @if($needsBillingCount > 0)
    <div class="section-bar">
        <span class="section-dot" style="background:#ef4444;"></span>
        <span class="section-label">Ready for Billing</span>
        <span class="section-count">{{ $needsBillingCount }}</span>
    </div>
    <div class="appt-table-wrap" style="margin-bottom:28px;">
        <table class="appt-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Vet</th>
                    <th>Completed</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($needsBilling as $nb)
                <tr>
                    <td><strong>#{{ $nb->appointment_number }}</strong></td>
                    <td>{{ $nb->pet->name ?? '—' }}</td>
                    <td>{{ $nb->pet->petParent->name ?? '—' }}</td>
                    <td>{{ $nb->vet->name ?? 'Unassigned' }}</td>
                    <td>{{ $nb->completed_at ? \Carbon\Carbon::parse($nb->completed_at)->format('d M h:i A') : '—' }}</td>
                    <td>
                        @if($nb->bill && $nb->bill->status == 'draft')
                            <span class="status-badge" style="background:#fef3c7;color:#92400e;">Draft Bill</span>
                        @else
                            <span class="status-badge" style="background:#fee2e2;color:#991b1b;">No Bill</span>
                        @endif
                    </td>
                    <td>
                        @if(auth()->user()->hasPermission('billing.create'))
                        <a href="{{ route('clinic.billing.create', $nb->id) }}" class="act-btn act-bill">
                            {{ $nb->bill ? 'Edit Bill' : 'Create Bill' }}
                        </a>
                        @elseif(auth()->user()->hasPermission('billing.view') && $nb->bill)
                        <a href="{{ route('clinic.billing.create', $nb->id) }}" class="act-btn act-bill">View</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- All Appointments Table --}}
    <div class="section-bar">
        <span class="section-dot" style="background:#64748b;"></span>
        <span class="section-label">All Appointments</span>
        <span class="section-count">{{ $appointments->count() }}</span>
    </div>

    <div class="appt-table-wrap">
        <table class="appt-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date & Time</th>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Vet</th>
                    <th>Status</th>
                    <th>Timing</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appointment)
                <tr>
                    <td><strong>#{{ $appointment->appointment_number }}</strong></td>
                    <td>{{ $appointment->scheduled_at->format('d M h:i A') }}</td>
                    <td>{{ $appointment->pet->name ?? '—' }}</td>
                    <td>{{ $appointment->pet->petParent->name ?? '—' }}</td>
                    <td>{{ $appointment->vet ? 'Dr. ' . $appointment->vet->name : 'Unassigned' }}</td>
                    <td>
                        <span class="status-badge status-{{ $appointment->status }}">
                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                        </span>
                    </td>
                    <td>
                        @if($appointment->status == 'checked_in')
                            <span class="timer waiting-timer" data-time="{{ \Carbon\Carbon::parse($appointment->checked_in_at)->toIso8601String() }}">—</span>
                        @elseif($appointment->consultation_started_at && $appointment->completed_at)
                            <span class="consult-dur">{{ \Carbon\Carbon::parse($appointment->consultation_started_at)->diffInMinutes($appointment->completed_at) }} min consult</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        {{-- Check In --}}
                        @if($appointment->status == 'scheduled' && auth()->user()->hasPermission('appointments.manage'))
                        <form method="POST" action="{{ route('clinic.appointments.updateStatus', $appointment->id) }}" style="display:inline">
                            @csrf
                            <input type="hidden" name="status" value="checked_in">
                            <button class="act-btn act-checkin">Check In</button>
                        </form>
                        @endif

                        {{-- Start Consultation --}}
                        @if($appointment->status == 'checked_in' && auth()->user()->hasPermission('appointments.manage'))
                        <form method="POST" action="{{ route('clinic.appointments.updateStatus', $appointment->id) }}" style="display:inline">
                            @csrf
                            <input type="hidden" name="status" value="in_consultation">
                            <button class="act-btn act-start">Start</button>
                        </form>
                        @endif

                        {{-- Complete / Awaiting Lab --}}
                        @if($appointment->status == 'in_consultation' && auth()->user()->hasPermission('appointments.manage'))
                        <form method="POST" action="{{ route('clinic.appointments.updateStatus', $appointment->id) }}" style="display:inline">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button class="act-btn act-complete">Complete</button>
                        </form>
                        <form method="POST" action="{{ route('clinic.appointments.updateStatus', $appointment->id) }}" style="display:inline">
                            @csrf
                            <input type="hidden" name="status" value="awaiting_lab_results">
                            <button class="act-btn act-lab">Awaiting Lab</button>
                        </form>
                        @endif

                        {{-- Awaiting lab → Complete --}}
                        @if($appointment->status == 'awaiting_lab_results' && auth()->user()->hasPermission('appointments.manage'))
                        <form method="POST" action="{{ route('clinic.appointments.updateStatus', $appointment->id) }}" style="display:inline">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button class="act-btn act-complete">Mark Complete</button>
                        </form>
                        @endif

                        {{-- Billing --}}
                        @if(in_array($appointment->status, ['completed','awaiting_lab_results']) && auth()->user()->hasPermission('billing.create'))
                        <a href="{{ route('clinic.billing.create', $appointment->id) }}" class="act-btn act-bill">Billing</a>
                        @endif

                        {{-- Reschedule --}}
                        @if(!in_array($appointment->status, ['completed','cancelled','awaiting_lab_results']) && auth()->user()->hasPermission('appointments.manage'))
                        <button class="act-btn act-reschedule reschedule-btn"
                            data-id="{{ $appointment->id }}"
                            data-pet="{{ $appointment->pet->name ?? '' }}"
                            data-owner="{{ $appointment->pet->petParent->name ?? '' }}"
                            data-time="{{ $appointment->scheduled_at->format('Y-m-d\TH:i') }}">
                            Reschedule
                        </button>
                        @endif

                        {{-- Cancel --}}
                        @if(!in_array($appointment->status, ['completed','cancelled','awaiting_lab_results']) && auth()->user()->hasPermission('appointments.manage'))
                        <form method="POST" action="{{ route('clinic.appointments.updateStatus', $appointment->id) }}" style="display:inline"
                              onsubmit="return confirm('Cancel this appointment?')">
                            @csrf
                            <input type="hidden" name="status" value="cancelled">
                            <button class="act-btn act-cancel">Cancel</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="empty-row">No appointments scheduled today</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Reschedule Modal --}}
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;overflow:hidden;">
            <div class="modal-header" style="background:#f8fafc;border-bottom:1px solid #e5e7eb;">
                <h5 class="modal-title" style="font-size:16px;font-weight:700;">Reschedule Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rescheduleForm">
                @csrf
                <div class="modal-body" style="padding:20px;">
                    <p id="modalPet" style="font-size:14px;font-weight:600;color:#1e293b;margin-bottom:16px;"></p>
                    <label style="font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;display:block;">New Date & Time</label>
                    <input type="datetime-local" name="scheduled_at" id="modalTime" class="form-control"
                           style="padding:10px 14px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;" required>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e5e7eb;background:#f8fafc;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
                    <button class="btn btn-primary" style="border-radius:8px;background:#2563eb;border:none;">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Waiting timers
function updateWaitingTimers() {
    document.querySelectorAll('.waiting-timer').forEach(function(el) {
        var start = new Date(el.dataset.time);
        var now = new Date();
        var diff = Math.floor((now - start) / 60000);
        if (diff < 0) diff = 0;
        if (diff < 60) {
            el.innerText = diff + ' min wait';
        } else {
            el.innerText = Math.floor(diff / 60) + 'h ' + (diff % 60) + 'm wait';
        }
    });
}
updateWaitingTimers();
setInterval(updateWaitingTimers, 10000);

// Reschedule modal
document.querySelectorAll('.reschedule-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('modalPet').innerText = this.dataset.pet + ' (' + this.dataset.owner + ')';
        document.getElementById('modalTime').value = this.dataset.time;
        document.getElementById('rescheduleForm').action = '/clinic/appointments/' + this.dataset.id + '/reschedule';
        new bootstrap.Modal(document.getElementById('rescheduleModal')).show();
    });
});

// Auto-refresh every 30s
setInterval(function() { location.reload(); }, 30000);
</script>

@endsection
