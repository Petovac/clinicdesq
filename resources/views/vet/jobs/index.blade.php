@extends('layouts.vet')
@section('content')
<style>
.page-hdr { display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px; }
.page-hdr h2 { font-size:22px;font-weight:700;margin:0; }
.filter-bar { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;align-items:end; }
.filter-group { display:flex;flex-direction:column;gap:3px; }
.filter-group label { font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px; }
.filter-group input,.filter-group select { padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;min-width:140px; }
.filter-group input:focus,.filter-group select:focus { outline:none;border-color:#2563eb;box-shadow:0 0 0 2px rgba(37,99,235,0.12); }
.search-btn { padding:8px 16px;background:#2563eb;color:#fff;border:none;border-radius:6px;font-weight:600;cursor:pointer;height:36px;align-self:end; }
.clear-btn { padding:8px 12px;background:#f3f4f6;color:#374151;border:none;border-radius:6px;font-weight:500;cursor:pointer;height:36px;align-self:end;text-decoration:none;font-size:13px; }
.job-list { display:grid;gap:12px; }
.job-card { background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px;transition:border-color .15s; }
.job-card:hover { border-color:#2563eb; }
.job-title { font-size:16px;font-weight:700;color:#111827; }
.job-org { font-size:13px;color:#6b7280;margin-top:2px; }
.job-meta { display:flex;gap:10px;margin-top:8px;flex-wrap:wrap; }
.job-tag { font-size:11px;padding:3px 10px;background:#f3f4f6;border-radius:10px;color:#374151;font-weight:500; }
.job-tag--salary { background:#dcfce7;color:#166534; }
.job-tag--type { background:#dbeafe;color:#1d4ed8; }
.btn { padding:8px 14px;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px; }
.btn-primary { background:#2563eb;color:#fff; }
.btn-applied { background:#dcfce7;color:#166534;cursor:default; }
.btn-apps { background:#7c3aed;color:#fff; }
.empty { text-align:center;padding:40px;color:#9ca3af;font-size:14px; }
.results-count { font-size:13px;color:#6b7280;margin-bottom:10px; }
</style>

<div class="page-hdr">
    <h2>🏥 Job Opportunities</h2>
    <a href="{{ route('vet.jobs.my-applications') }}" class="btn btn-apps">My Applications</a>
</div>

<form method="GET" class="filter-bar">
    <div class="filter-group">
        <label>Search</label>
        <input name="q" placeholder="Title or organisation..." value="{{ request('q') }}">
    </div>

    <div class="filter-group">
        <label>City</label>
        @php $locCities = config('locations.cities', []); $allCities = collect($locCities)->flatten()->sort()->unique()->values(); @endphp
        <select name="city">
            <option value="">All cities</option>
            @foreach($allCities as $c)
            <option value="{{ $c }}" {{ request('city') === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </div>

    <div class="filter-group">
        <label>Type</label>
        <select name="type">
            <option value="">All types</option>
            <option value="full_time" {{ request('type') === 'full_time' ? 'selected' : '' }}>Full Time</option>
            <option value="part_time" {{ request('type') === 'part_time' ? 'selected' : '' }}>Part Time</option>
            <option value="locum" {{ request('type') === 'locum' ? 'selected' : '' }}>Locum</option>
            <option value="contract" {{ request('type') === 'contract' ? 'selected' : '' }}>Contract</option>
        </select>
    </div>

    <div class="filter-group">
        <label>Min Salary (₹/month)</label>
        <input name="min_salary" type="number" placeholder="e.g. 30000" value="{{ request('min_salary') }}" style="width:120px;">
    </div>

    <div class="filter-group">
        <label>Experience</label>
        <select name="max_exp">
            <option value="">Any</option>
            <option value="0" {{ request('max_exp') === '0' ? 'selected' : '' }}>Fresher (0 yrs)</option>
            <option value="2" {{ request('max_exp') === '2' ? 'selected' : '' }}>≤ 2 yrs</option>
            <option value="5" {{ request('max_exp') === '5' ? 'selected' : '' }}>≤ 5 yrs</option>
            <option value="10" {{ request('max_exp') === '10' ? 'selected' : '' }}>≤ 10 yrs</option>
        </select>
    </div>

    <button type="submit" class="search-btn">Search</button>
    @if(request()->hasAny(['q','city','type','min_salary','max_exp']))
    <a href="{{ route('vet.jobs.index') }}" class="clear-btn">Clear</a>
    @endif
</form>

<div class="results-count">{{ $jobs->total() }} job{{ $jobs->total() !== 1 ? 's' : '' }} found</div>

@if($jobs->count())
<div class="job-list">
    @foreach($jobs as $job)
    <div class="job-card">
        <div style="display:flex;justify-content:space-between;align-items:start;">
            <div>
                <div class="job-title">{{ $job->title }}</div>
                <div class="job-org">{{ $job->organisation->name ?? '' }} {{ $job->clinic ? '· '.$job->clinic->name : '' }}</div>
            </div>
            @if(in_array($job->id, $appliedIds))
                <span class="btn btn-applied">✓ Applied</span>
            @else
                <a href="{{ route('vet.jobs.show', $job) }}" class="btn btn-primary">View & Apply</a>
            @endif
        </div>
        <div class="job-meta">
            <span class="job-tag job-tag--type">{{ $job->employment_label }}</span>
            <span class="job-tag">📍 {{ $job->city ?? 'Any location' }}</span>
            <span class="job-tag job-tag--salary">{{ $job->salary_range }}</span>
            @if($job->min_experience_years)<span class="job-tag">{{ $job->min_experience_years }}+ yrs</span>@endif
            @if($job->specialization_required)<span class="job-tag">{{ $job->specialization_required }}</span>@endif
            <span class="job-tag">{{ $job->applications_count }} applicant{{ $job->applications_count !== 1 ? 's' : '' }}</span>
        </div>
        @if($job->description)
        <p style="margin-top:8px;font-size:13px;color:#6b7280;line-height:1.5;">{{ Str::limit($job->description, 150) }}</p>
        @endif
    </div>
    @endforeach
</div>
<div style="margin-top:16px;">{{ $jobs->appends(request()->query())->links() }}</div>
@else
<div class="empty">No job openings match your filters. Try adjusting your search.</div>
@endif
@endsection
