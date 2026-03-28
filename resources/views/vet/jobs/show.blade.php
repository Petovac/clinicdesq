@extends('layouts.vet')
@section('content')
<style>
.page-hdr { display:flex;align-items:center;gap:12px;margin-bottom:20px; }
.page-hdr h2 { font-size:22px;font-weight:700;margin:0; }
.card { background:#fff;border-radius:10px;padding:20px;border:1px solid #e5e7eb;margin-bottom:14px;max-width:700px; }
.job-org { font-size:14px;color:#6b7280;margin-top:2px; }
.job-tags { display:flex;gap:8px;margin-top:10px;flex-wrap:wrap; }
.tag { font-size:12px;padding:4px 12px;background:#f3f4f6;border-radius:10px;color:#374151;font-weight:500; }
.tag--salary { background:#dcfce7;color:#166534; }
.section { margin-top:16px; }
.section h3 { font-size:14px;font-weight:700;color:#111827;margin:0 0 6px; }
.section p { font-size:14px;color:#374151;line-height:1.6;white-space:pre-line; }
.apply-box { background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:18px;max-width:700px; }
.apply-box h3 { margin:0 0 10px;font-size:16px;font-weight:700;color:#1e40af; }
.form-group { margin-bottom:12px; }
.form-group label { display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:4px; }
.form-group textarea { width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;min-height:80px;font-family:inherit; }
.btn { padding:10px 18px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;border:none; }
.btn-primary { background:#2563eb;color:#fff; }
.btn-back { background:#e5e7eb;color:#374151;text-decoration:none; }
.applied-box { background:#dcfce7;border:1px solid #bbf7d0;border-radius:10px;padding:18px;max-width:700px;text-align:center; }
.success-bar { background:#dcfce7;border:1px solid #bbf7d0;padding:10px;border-radius:6px;margin-bottom:14px;color:#166534;font-size:14px; }
.error-bar { background:#fee2e2;border:1px solid #fca5a5;padding:10px;border-radius:6px;margin-bottom:14px;color:#991b1b;font-size:14px; }
.badge { display:inline-block;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:600; }
</style>

<div class="page-hdr">
    <a href="{{ route('vet.jobs.index') }}" style="color:#6b7280;text-decoration:none;font-size:18px;">←</a>
    <h2>{{ $job->title }}</h2>
</div>

@if(session('success'))<div class="success-bar">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div class="error-bar">{{ session('error') }}</div>@endif

<div class="card">
    <div class="job-org">{{ $job->organisation->name ?? '' }} {{ $job->clinic ? '· '.$job->clinic->name : '' }}</div>

    <div class="job-tags">
        <span class="tag">{{ $job->employment_label }}</span>
        <span class="tag">{{ $job->city ?? 'Any location' }}{{ $job->state ? ', '.$job->state : '' }}</span>
        <span class="tag tag--salary">{{ $job->salary_range }}</span>
        @if($job->min_experience_years)<span class="tag">{{ $job->min_experience_years }}+ yrs experience</span>@endif
        @if($job->specialization_required)<span class="tag">{{ $job->specialization_required }}</span>@endif
    </div>

    @if($job->description)
    <div class="section">
        <h3>About the Role</h3>
        <p>{{ $job->description }}</p>
    </div>
    @endif

    @if($job->requirements)
    <div class="section">
        <h3>Requirements</h3>
        <p>{{ $job->requirements }}</p>
    </div>
    @endif

    <div style="margin-top:12px;font-size:12px;color:#9ca3af;">
        Posted {{ $job->published_at?->diffForHumans() ?? '' }}
        @if($job->closes_at) · Closes {{ $job->closes_at->format('d M Y') }} @endif
    </div>
</div>

@if($hasApplied)
<div class="applied-box">
    <h3 style="margin:0 0 6px;font-size:16px;font-weight:700;color:#166534;">✓ You've Applied</h3>
    <p style="margin:0;font-size:14px;color:#374151;">
        Status: <span class="badge" style="background:#dbeafe;color:#1d4ed8;">{{ ucfirst($application->status) }}</span>
        · Applied {{ $application->created_at->diffForHumans() }}
    </p>
    @if($application->status === 'applied')
    <form method="POST" action="{{ route('vet.jobs.withdraw', $application) }}" style="margin-top:10px;" onsubmit="return confirm('Withdraw your application?')">
        @csrf
        <button class="btn" style="background:#fee2e2;color:#991b1b;">Withdraw Application</button>
    </form>
    @endif
</div>
@else
<div class="apply-box">
    <h3>Apply for this Position</h3>
    <p style="font-size:13px;color:#6b7280;margin-bottom:12px;">Your profile details and performance history will be shared with the organisation after you apply.</p>
    <form method="POST" action="{{ route('vet.jobs.apply', $job) }}">
        @csrf
        <div class="form-group">
            <label>Cover Note (optional)</label>
            <textarea name="cover_note" placeholder="Introduce yourself, highlight your experience, why you're interested..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Application</button>
    </form>
</div>
@endif
@endsection
