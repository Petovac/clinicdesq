@extends('organisation.layout')
@section('content')
<style>
.page-hdr { display:flex;align-items:center;gap:12px;margin-bottom:20px; }
.page-hdr h2 { font-size:22px;font-weight:700;margin:0; }
.card { background:#fff;border-radius:10px;padding:18px;border:1px solid #e5e7eb;margin-bottom:14px; }
.badge { display:inline-block;padding:3px 10px;border-radius:10px;font-size:10px;font-weight:600; }
.badge-active { background:#dcfce7;color:#166534; }
.badge-draft { background:#f3f4f6;color:#6b7280; }
.badge-paused { background:#fef3c7;color:#92400e; }
.badge-closed { background:#fee2e2;color:#991b1b; }
.badge-applied { background:#dbeafe;color:#1d4ed8; }
.badge-shortlisted { background:#dcfce7;color:#166534; }
.badge-rejected { background:#fee2e2;color:#991b1b; }
.badge-interview { background:#fef3c7;color:#92400e; }
.badge-offered { background:#d1fae5;color:#065f46; }
.badge-hired { background:#16a34a;color:#fff; }
.badge-withdrawn { background:#f3f4f6;color:#6b7280; }
.meta { font-size:13px;color:#6b7280; }
.stat-grid { display:flex;gap:16px;margin-top:8px; }
.stat { font-size:13px; }
.stat .n { font-weight:700; }
.btn { padding:6px 12px;border-radius:5px;font-size:12px;font-weight:600;cursor:pointer;border:none;text-decoration:none; }
.btn-view { background:#dbeafe;color:#1d4ed8; }
.btn-sm { padding:5px 10px;font-size:11px; }
table { width:100%;border-collapse:collapse; }
th { text-align:left;padding:10px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #e5e7eb; }
td { padding:10px;border-bottom:1px solid #f1f5f9;font-size:14px; }
tr:hover td { background:#f9fafb; }
.status-actions { display:flex;gap:6px;flex-wrap:wrap; }
.status-actions form { display:inline; }
.status-btn { padding:4px 10px;border-radius:4px;font-size:10px;font-weight:600;cursor:pointer;border:1px solid #d1d5db;background:#fff;color:#374151; }
.status-btn:hover { background:#f3f4f6; }
.empty { text-align:center;padding:30px;color:#9ca3af;font-size:13px; }
.success-bar { background:#dcfce7;border:1px solid #bbf7d0;padding:10px;border-radius:6px;margin-bottom:14px;color:#166534;font-size:14px; }
</style>

<div class="page-hdr">
    <a href="{{ route('organisation.jobs.index') }}" style="color:#6b7280;text-decoration:none;font-size:18px;">←</a>
    <h2>{{ $job->title }}</h2>
    <span class="badge badge-{{ $job->status }}">{{ ucfirst($job->status) }}</span>
</div>

@if(session('success'))<div class="success-bar">✓ {{ session('success') }}</div>@endif

{{-- Job Info --}}
<div class="card">
    <div class="meta">
        {{ $job->employment_label }} · {{ $job->city ?? 'Any location' }}
        @if($job->clinic) · {{ $job->clinic->name }} @endif
        · {{ $job->salary_range }}
        @if($job->min_experience_years) · {{ $job->min_experience_years }}+ yrs @endif
    </div>
    @if($job->description)<p style="margin-top:8px;font-size:14px;color:#374151;">{{ $job->description }}</p>@endif

    <div style="margin-top:12px;display:flex;gap:8px;">
        @if($job->status !== 'active')
        <form method="POST" action="{{ route('organisation.jobs.toggle', $job) }}">@csrf<input type="hidden" name="status" value="active"><button class="btn" style="background:#16a34a;color:#fff;">Publish</button></form>
        @endif
        @if($job->status === 'active')
        <form method="POST" action="{{ route('organisation.jobs.toggle', $job) }}">@csrf<input type="hidden" name="status" value="paused"><button class="btn" style="background:#f59e0b;color:#fff;">Pause</button></form>
        @endif
        @if($job->status !== 'closed')
        <form method="POST" action="{{ route('organisation.jobs.toggle', $job) }}">@csrf<input type="hidden" name="status" value="closed"><button class="btn" style="background:#ef4444;color:#fff;">Close</button></form>
        @endif
    </div>
</div>

{{-- Applications --}}
<div class="card">
    <h3 style="margin:0 0 12px;font-size:16px;font-weight:700;">Applications ({{ $applications->count() }})</h3>

    @if($applications->count())
    <table>
        <tr>
            <th>Vet</th>
            <th>Degree</th>
            <th>Applied</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        @foreach($applications as $app)
        <tr>
            <td>
                <strong>{{ $app->vet->name ?? '—' }}</strong>
                <div style="font-size:11px;color:#6b7280;">{{ $app->vet->specialization ?? '' }}</div>
            </td>
            <td style="font-size:13px;">{{ $app->vet->degree ?? '—' }}</td>
            <td class="meta">{{ $app->created_at->diffForHumans() }}</td>
            <td><span class="badge badge-{{ $app->status }}">{{ ucfirst($app->status) }}</span></td>
            <td>
                <a href="{{ route('organisation.jobs.applicant', [$job, $app]) }}" class="btn btn-view btn-sm">View Profile & Stats</a>
            </td>
        </tr>
        @endforeach
    </table>
    @else
    <div class="empty">No applications yet. Share the job link with vets.</div>
    @endif
</div>
@endsection
