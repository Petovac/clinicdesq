@extends('organisation.layout')
@section('content')
<style>
.page-hdr { display:flex;align-items:center;gap:12px;margin-bottom:20px; }
.page-hdr h2 { font-size:22px;font-weight:700;margin:0; }
.card { background:#fff;border-radius:10px;padding:18px;border:1px solid #e5e7eb;margin-bottom:14px; }
.stat-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:16px; }
.stat { background:#f8fafc;border:1px solid #f0f0f0;border-radius:8px;padding:14px;text-align:center; }
.stat .val { font-size:24px;font-weight:700;color:#111827; }
.stat .lbl { font-size:10px;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-top:2px; }
.stat--gold .val { color:#f59e0b; }
.stat--green .val { color:#16a34a; }
.stat--blue .val { color:#2563eb; }
.detail-row { display:flex;padding:8px 0;border-bottom:1px solid #f3f4f6; }
.detail-label { width:160px;font-size:12px;font-weight:600;color:#6b7280;flex-shrink:0; }
.detail-value { font-size:14px;color:#111827;flex:1; }
.badge { display:inline-block;padding:3px 10px;border-radius:10px;font-size:10px;font-weight:600; }
.badge-active { background:#dcfce7;color:#166534; }
.badge-inactive { background:#f3f4f6;color:#6b7280; }
.btn { padding:8px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:4px; }
.clinic-history { width:100%;border-collapse:collapse;font-size:13px;margin-top:8px; }
.clinic-history th { text-align:left;padding:6px 8px;font-weight:600;color:#6b7280;font-size:10px;text-transform:uppercase;border-bottom:1px solid #e5e7eb; }
.clinic-history td { padding:6px 8px;border-bottom:1px solid #f3f4f6; }
.action-bar { display:flex;gap:8px;flex-wrap:wrap; }
.action-bar form { display:inline; }
.action-btn { padding:8px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;border:none;color:#fff; }
.success-bar { background:#dcfce7;border:1px solid #bbf7d0;padding:10px;border-radius:6px;margin-bottom:14px;color:#166534;font-size:14px; }
.cover-note { background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px;font-size:14px;color:#1e40af;margin-bottom:12px;line-height:1.6; }
</style>

<div class="page-hdr">
    <a href="{{ route('organisation.jobs.show', $job) }}" style="color:#6b7280;text-decoration:none;font-size:18px;">←</a>
    <h2>{{ $application->vet->name ?? 'Unknown Vet' }}</h2>
    <span class="badge badge-{{ $application->status }}" style="padding:4px 12px;font-size:12px;">{{ ucfirst($application->status) }}</span>
</div>

@if(session('success'))<div class="success-bar">✓ {{ session('success') }}</div>@endif

<div style="font-size:13px;color:#6b7280;margin-bottom:14px;">
    Applied for <strong>{{ $job->title }}</strong> · {{ $application->created_at->diffForHumans() }}
</div>

@if($application->cover_note)
<div class="cover-note">
    <strong>Cover Note:</strong><br>{{ $application->cover_note }}
</div>
@endif

{{-- Performance Stats --}}
<div class="stat-grid">
    <div class="stat">
        <div class="val">{{ $analytics['total_cases'] ?? 0 }}</div>
        <div class="lbl">Total Cases</div>
    </div>
    <div class="stat">
        <div class="val">{{ $analytics['completed_cases'] ?? 0 }}</div>
        <div class="lbl">Completed</div>
    </div>
    <div class="stat stat--green">
        <div class="val">{{ $analytics['repeat_rate'] ?? 0 }}%</div>
        <div class="lbl">Repeat Rate</div>
    </div>
    <div class="stat stat--gold">
        <div class="val">{{ $analytics['avg_rating'] ?? '—' }}</div>
        <div class="lbl">Avg Rating ({{ $analytics['review_count'] ?? 0 }})</div>
    </div>
    <div class="stat stat--blue">
        <div class="val">{{ $analytics['avg_revenue_per_case'] ? '₹'.number_format($analytics['avg_revenue_per_case']) : '—' }}</div>
        <div class="lbl">Avg Revenue/Case</div>
    </div>
</div>

{{-- Profile Details --}}
<div class="card">
    <h3 style="margin:0 0 12px;font-size:15px;font-weight:700;">Profile</h3>
    <div class="detail-row">
        <div class="detail-label">Degree</div>
        <div class="detail-value">{{ $analytics['degree'] ?? '—' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Specialization</div>
        <div class="detail-value">{{ $analytics['specialization'] ?? '—' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Registration No.</div>
        <div class="detail-value">{{ $analytics['registration_number'] ?? '—' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Experience</div>
        <div class="detail-value">{{ $analytics['experience'] ?? '—' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">On Platform Since</div>
        <div class="detail-value">{{ $analytics['platform_since'] ?? '—' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Email</div>
        <div class="detail-value">{{ $application->vet->email ?? '—' }}</div>
    </div>
    <div class="detail-row" style="border-bottom:none;">
        <div class="detail-label">Phone</div>
        <div class="detail-value">{{ $application->vet->phone ?? '—' }}</div>
    </div>
</div>

{{-- Work History --}}
@if(($analytics['clinics_worked'] ?? collect())->count())
<div class="card">
    <h3 style="margin:0 0 10px;font-size:15px;font-weight:700;">Work History</h3>
    <table class="clinic-history">
        <tr>
            <th>Clinic</th>
            <th>Organisation</th>
            <th>City</th>
            <th>Joined</th>
            <th>Left</th>
            <th>Status</th>
        </tr>
        @foreach($analytics['clinics_worked'] as $cw)
        <tr>
            <td><strong>{{ $cw->clinic_name }}</strong></td>
            <td>{{ $cw->org_name }}</td>
            <td>{{ $cw->city ?? '—' }}</td>
            <td>{{ $cw->joined_at ? \Carbon\Carbon::parse($cw->joined_at)->format('M Y') : '—' }}</td>
            <td>{{ $cw->offboarded_at ? \Carbon\Carbon::parse($cw->offboarded_at)->format('M Y') : '—' }}</td>
            <td><span class="badge {{ $cw->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $cw->is_active ? 'Active' : 'Left' }}</span></td>
        </tr>
        @endforeach
    </table>
</div>
@endif

{{-- Actions --}}
<div class="card">
    <h3 style="margin:0 0 12px;font-size:15px;font-weight:700;">Update Status</h3>
    <div class="action-bar">
        @foreach(['shortlisted' => ['#16a34a','Shortlist'], 'interview' => ['#f59e0b','Interview'], 'offered' => ['#2563eb','Offer'], 'hired' => ['#7c3aed','Hire'], 'rejected' => ['#ef4444','Reject']] as $status => [$color, $label])
        @if($application->status !== $status)
        <form method="POST" action="{{ route('organisation.jobs.applicant.update', [$job, $application]) }}">
            @csrf
            <input type="hidden" name="status" value="{{ $status }}">
            <button class="action-btn" style="background:{{ $color }};">{{ $label }}</button>
        </form>
        @endif
        @endforeach
    </div>

    <div style="margin-top:14px;">
        <form method="POST" action="{{ route('organisation.jobs.applicant.update', [$job, $application]) }}">
            @csrf
            <input type="hidden" name="status" value="{{ $application->status }}">
            <label style="font-size:12px;font-weight:600;color:#374151;">Internal Notes</label>
            <textarea name="org_notes" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;margin-top:4px;min-height:60px;">{{ $application->org_notes }}</textarea>
            <button class="btn" style="background:#4f46e5;color:#fff;margin-top:6px;">Save Notes</button>
        </form>
    </div>
</div>
@endsection
