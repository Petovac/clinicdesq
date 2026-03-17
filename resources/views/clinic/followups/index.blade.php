@extends('clinic.layout')

@section('content')
<style>
.fu-wrap { max-width: 1100px; }
.fu-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:16px; flex-wrap:wrap; }
.fu-header h2 { margin:0; font-size:22px; font-weight:700; color:#111827; }
.fu-tabs { display:flex; gap:6px; }
.fu-tab { padding:7px 16px; border-radius:8px; font-size:13px; font-weight:500; text-decoration:none; border:1px solid #e5e7eb; color:#374151; background:#fff; }
.fu-tab:hover { background:#f3f4f6; }
.fu-tab.active { background:#2563eb; color:#fff; border-color:#2563eb; }
.fu-table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.06); }
.fu-table th { background:#f9fafb; padding:10px 14px; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; text-align:left; border-bottom:1px solid #e5e7eb; }
.fu-table td { padding:12px 14px; font-size:14px; border-bottom:1px solid #f3f4f6; }
.fu-table tr:hover { background:#f9fafb; }
.badge-upcoming { background:#d1fae5; color:#065f46; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; }
.badge-today { background:#fef3c7; color:#92400e; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; }
.badge-overdue { background:#fee2e2; color:#991b1b; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; }
.badge-prognosis { background:#eff6ff; color:#1e40af; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:500; }
</style>

<div class="fu-wrap">
    <div class="fu-header">
        <h2>Follow-ups</h2>
        <div class="fu-tabs">
            <a href="{{ route('clinic.followups.index', ['filter' => 'upcoming']) }}"
               class="fu-tab {{ $filter === 'upcoming' ? 'active' : '' }}">Upcoming</a>
            <a href="{{ route('clinic.followups.index', ['filter' => 'today']) }}"
               class="fu-tab {{ $filter === 'today' ? 'active' : '' }}">Today</a>
            <a href="{{ route('clinic.followups.index', ['filter' => 'overdue']) }}"
               class="fu-tab {{ $filter === 'overdue' ? 'active' : '' }}">Overdue</a>
            <a href="{{ route('clinic.followups.index', ['filter' => 'all']) }}"
               class="fu-tab {{ $filter === 'all' ? 'active' : '' }}">All</a>
        </div>
    </div>

    <table class="fu-table">
        <thead>
            <tr>
                <th>Follow-up Date</th>
                <th>Pet</th>
                <th>Pet Parent</th>
                <th>Phone</th>
                <th>Reason</th>
                <th>Prognosis</th>
                <th>Doctor</th>
                <th>Clinic</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @forelse($followups as $fu)
            @php
                $appt = $fu->appointment;
                $isToday = $fu->followup_date->isToday();
                $isOverdue = $fu->followup_date->lt($today) && !$isToday;
            @endphp
            <tr>
                <td style="font-weight:600;">
                    {{ $fu->followup_date->format('d M Y') }}
                </td>
                <td>{{ $appt->pet->name ?? '—' }}</td>
                <td>{{ $appt->pet->petParent->name ?? '—' }}</td>
                <td>
                    @if($appt->pet->petParent->phone ?? null)
                        <a href="tel:{{ $appt->pet->petParent->phone }}" style="color:#2563eb;text-decoration:none;">
                            {{ $appt->pet->petParent->phone }}
                        </a>
                    @else
                        —
                    @endif
                </td>
                <td style="max-width:200px;font-size:13px;color:#374151;">
                    {{ \Illuminate\Support\Str::limit($fu->followup_reason, 60) ?? '—' }}
                </td>
                <td>
                    @if($fu->prognosis)
                        <span class="badge-prognosis">{{ ucfirst($fu->prognosis) }}</span>
                    @else
                        —
                    @endif
                </td>
                <td style="font-size:13px;">{{ $appt->vet->name ?? '—' }}</td>
                <td style="font-size:13px;">{{ $appt->clinic->name ?? '—' }}</td>
                <td>
                    @if($isToday)
                        <span class="badge-today">Today</span>
                    @elseif($isOverdue)
                        <span class="badge-overdue">Overdue</span>
                    @else
                        <span class="badge-upcoming">Upcoming</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center;color:#9ca3af;padding:30px;">
                    No follow-ups found for this filter.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    @if($followups->hasPages())
        <div style="margin-top:16px;">
            {{ $followups->appends(['filter' => $filter])->links() }}
        </div>
    @endif
</div>
@endsection
