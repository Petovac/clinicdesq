@extends('layouts.vet')
@section('content')
<style>
.page-hdr { display:flex;align-items:center;gap:12px;margin-bottom:20px; }
.page-hdr h2 { font-size:22px;font-weight:700;margin:0; }
.card { background:#fff;border-radius:10px;padding:16px;border:1px solid #e5e7eb;margin-bottom:10px; }
.badge { display:inline-block;padding:3px 10px;border-radius:10px;font-size:10px;font-weight:600; }
.badge-applied { background:#dbeafe;color:#1d4ed8; }
.badge-shortlisted { background:#dcfce7;color:#166534; }
.badge-interview { background:#fef3c7;color:#92400e; }
.badge-offered { background:#d1fae5;color:#065f46; }
.badge-hired { background:#16a34a;color:#fff; }
.badge-rejected { background:#fee2e2;color:#991b1b; }
.badge-withdrawn { background:#f3f4f6;color:#6b7280; }
.empty { text-align:center;padding:40px;color:#9ca3af;font-size:14px; }
.btn-sm { padding:5px 12px;border-radius:5px;font-size:12px;font-weight:600;text-decoration:none;background:#dbeafe;color:#1d4ed8; }
.success-bar { background:#dcfce7;border:1px solid #bbf7d0;padding:10px;border-radius:6px;margin-bottom:14px;color:#166534;font-size:14px; }
</style>

<div class="page-hdr">
    <a href="{{ route('vet.jobs.index') }}" style="color:#6b7280;text-decoration:none;font-size:18px;">←</a>
    <h2>My Applications</h2>
</div>

@if(session('success'))<div class="success-bar">✓ {{ session('success') }}</div>@endif

@if($applications->count())
@foreach($applications as $app)
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:start;">
        <div>
            <strong style="font-size:15px;">{{ $app->jobPosting->title ?? '—' }}</strong>
            <div style="font-size:12px;color:#6b7280;margin-top:2px;">
                {{ $app->jobPosting->organisation->name ?? '' }}
                {{ $app->jobPosting->clinic ? '· '.$app->jobPosting->clinic->name : '' }}
                · {{ $app->jobPosting->city ?? '' }}
            </div>
        </div>
        <span class="badge badge-{{ $app->status }}">{{ ucfirst($app->status) }}</span>
    </div>
    <div style="margin-top:8px;font-size:12px;color:#9ca3af;">
        Applied {{ $app->created_at->diffForHumans() }}
        @if($app->reviewed_at) · Reviewed {{ $app->reviewed_at->diffForHumans() }} @endif
    </div>
    <div style="margin-top:8px;">
        <a href="{{ route('vet.jobs.show', $app->jobPosting) }}" class="btn-sm">View Job</a>
    </div>
</div>
@endforeach
@else
<div class="card empty">No applications yet. <a href="{{ route('vet.jobs.index') }}" style="color:#2563eb;">Browse jobs →</a></div>
@endif
@endsection
