@extends('organisation.layout')
@section('content')
<style>
.page-hdr { display:flex;justify-content:space-between;align-items:center;margin-bottom:20px; }
.page-hdr h2 { font-size:22px;font-weight:700;margin:0; }
.btn { padding:8px 16px;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px; }
.btn-primary { background:#4f46e5;color:#fff; }
.card { background:#fff;border-radius:10px;padding:18px;border:1px solid #e5e7eb;margin-bottom:12px; }
.job-grid { display:grid;gap:12px; }
.job-card { background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px;transition:border-color .15s; }
.job-card:hover { border-color:#4f46e5; }
.job-title { font-size:16px;font-weight:700;color:#111827; }
.job-meta { font-size:12px;color:#6b7280;margin-top:4px; }
.badge { display:inline-block;padding:3px 10px;border-radius:10px;font-size:10px;font-weight:600; }
.badge-active { background:#dcfce7;color:#166534; }
.badge-draft { background:#f3f4f6;color:#6b7280; }
.badge-paused { background:#fef3c7;color:#92400e; }
.badge-closed { background:#fee2e2;color:#991b1b; }
.badge-filled { background:#dbeafe;color:#1d4ed8; }
.stat-row { display:flex;gap:20px;margin-top:8px; }
.stat { font-size:13px; }
.stat .n { font-weight:700;color:#111827; }
.empty { text-align:center;padding:40px;color:#9ca3af;font-size:14px; }
.success-bar { background:#dcfce7;border:1px solid #bbf7d0;padding:10px;border-radius:6px;margin-bottom:14px;color:#166534;font-size:14px; }
</style>

<div class="page-hdr">
    <h2>📋 Hiring Portal</h2>
    <a href="{{ route('organisation.jobs.create') }}" class="btn btn-primary">+ Post New Job</a>
</div>

@if(session('success'))<div class="success-bar">✓ {{ session('success') }}</div>@endif

@if($jobs->count())
<div class="job-grid">
    @foreach($jobs as $job)
    <div class="job-card">
        <div style="display:flex;justify-content:space-between;align-items:start;">
            <div>
                <div class="job-title">{{ $job->title }}</div>
                <div class="job-meta">
                    {{ $job->employment_label }} · {{ $job->city ?? 'Any location' }}
                    @if($job->clinic) · {{ $job->clinic->name }} @endif
                    · Posted {{ $job->published_at?->diffForHumans() ?? 'Not published' }}
                </div>
            </div>
            <span class="badge badge-{{ $job->status }}">{{ ucfirst($job->status) }}</span>
        </div>
        <div class="stat-row">
            <div class="stat"><span class="n">{{ $job->applications_count }}</span> applicants</div>
            <div class="stat">{{ $job->salary_range }}</div>
            @if($job->min_experience_years)<div class="stat">{{ $job->min_experience_years }}+ yrs exp</div>@endif
        </div>
        <div style="margin-top:10px;">
            <a href="{{ route('organisation.jobs.show', $job) }}" class="btn" style="background:#dbeafe;color:#1d4ed8;">View Applications</a>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card empty">
    <p>No job postings yet.</p>
    <p style="margin-top:8px;"><a href="{{ route('organisation.jobs.create') }}" class="btn btn-primary">Post your first job</a></p>
</div>
@endif
@endsection
