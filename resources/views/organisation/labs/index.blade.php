@extends('organisation.layout')

@section('content')
<style>
.page-title { font-size:22px; font-weight:700; margin-bottom:20px; }
.card { background:#fff; border-radius:10px; padding:20px; border:1px solid #e5e7eb; margin-bottom:16px; }
.search-bar { display:flex; gap:10px; margin-bottom:16px; }
.search-bar input { flex:1; padding:10px 14px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; }
.search-bar input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 2px rgba(37,99,235,0.12); }
.search-bar button { padding:10px 18px; background:#2563eb; color:#fff; border:none; border-radius:8px; font-weight:600; cursor:pointer; }
.lab-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:12px; }
.lab-card { border:1px solid #e5e7eb; border-radius:10px; padding:16px; background:#fff; transition:border-color .15s; }
.lab-card:hover { border-color:#2563eb; }
.lab-name { font-size:15px; font-weight:700; color:#111827; }
.lab-city { font-size:12px; color:#6b7280; margin-top:2px; }
.lab-meta { font-size:12px; color:#6b7280; margin-top:6px; }
.lab-tests { font-size:11px; color:#2563eb; margin-top:4px; }
.badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:600; }
.badge-pending { background:#fef3c7; color:#92400e; }
.btn { padding:8px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; border:none; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }
.btn-primary { background:#2563eb; color:#fff; }
.btn-outline { background:#fff; color:#374151; border:1px solid #d1d5db; }
.btn-sm { padding:5px 10px; font-size:11px; }
.btn-request { background:#f59e0b; color:#fff; }
.section-title { font-size:14px; font-weight:700; color:#374151; margin:0 0 10px; }
.empty { text-align:center; padding:24px; color:#9ca3af; font-size:13px; }
.success-bar { background:#dcfce7; border:1px solid #bbf7d0; padding:10px 14px; border-radius:6px; margin-bottom:14px; color:#166534; font-size:14px; }
.error-bar { background:#fee2e2; border:1px solid #fca5a5; padding:10px 14px; border-radius:6px; margin-bottom:14px; color:#991b1b; font-size:14px; }
.test-list { margin-top:8px; max-height:0; overflow:hidden; transition:max-height .3s ease; }
.test-list.open { max-height:500px; overflow-y:auto; }
.test-row { display:flex; justify-content:space-between; padding:4px 0; border-bottom:1px solid #f3f4f6; font-size:12px; }
.test-row:last-child { border-bottom:none; }
.test-toggle { cursor:pointer; color:#2563eb; font-size:11px; font-weight:600; background:none; border:none; padding:0; margin-top:4px; }
</style>

<h2 class="page-title">External Labs</h2>

@if(session('success'))<div class="success-bar">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div class="error-bar">{{ session('error') }}</div>@endif

{{-- Connected Labs --}}
@if($tiedUpLabs->count())
<div class="card">
    <div class="section-title">✅ Connected Labs ({{ $tiedUpLabs->count() }})</div>
    <div class="lab-grid">
        @foreach($tiedUpLabs as $lab)
        @php
            $masterTests = \App\Models\ExternalLabTest::where('external_lab_id', $lab->id)->whereNull('organisation_id')->orderBy('test_name')->get();
            $importedCount = $lab->testOfferings->count();
        @endphp
        <div class="lab-card" style="border-left:3px solid #16a34a;">
            <div class="lab-name">{{ $lab->name }}</div>
            <div class="lab-city">📍 {{ $lab->city ?? '—' }}{{ $lab->state ? ', '.$lab->state : '' }}</div>
            <div class="lab-meta">{{ $lab->phone ?? '' }} {{ $lab->email ? '· '.$lab->email : '' }}</div>
            <div class="lab-tests">{{ $importedCount }} imported · {{ $masterTests->count() }} total tests</div>

            @if($masterTests->count())
            <button class="test-toggle" onclick="this.nextElementSibling.classList.toggle('open'); this.textContent = this.nextElementSibling.classList.contains('open') ? '▲ Hide tests' : '▼ View tests & pricing'">▼ View tests & pricing</button>
            <div class="test-list">
                @foreach($masterTests as $mt)
                <div class="test-row">
                    <span>{{ $mt->test_name }} <span style="color:#9ca3af;">({{ $mt->test_code }})</span></span>
                    <span style="font-weight:600;color:#111;">₹{{ number_format($mt->b2b_price) }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div style="margin-top:8px;">
                <a href="{{ route('organisation.labs.edit', $lab) }}" class="btn btn-sm btn-primary">Manage & Import Tests</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Pending Requests --}}
@if($pendingLabs->count())
<div class="card" style="border-left:3px solid #f59e0b;">
    <div class="section-title">⏳ Pending Requests ({{ $pendingLabs->count() }})</div>
    <div class="lab-grid">
        @foreach($pendingLabs as $lab)
        <div class="lab-card" style="background:#fffbeb;">
            <div style="display:flex;justify-content:space-between;align-items:start;">
                <div>
                    <div class="lab-name">{{ $lab->name }}</div>
                    <div class="lab-city">📍 {{ $lab->city ?? '—' }}</div>
                </div>
                <span class="badge badge-pending">Pending</span>
            </div>
            <div class="lab-meta">Waiting for lab to accept your request.</div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Browse Available Labs --}}
<div class="card">
    <div class="section-title">🔍 Browse Labs {{ $orgCities->isNotEmpty() && !$search ? 'in '.implode(', ', $orgCities->toArray()) : '' }}</div>
    <p style="font-size:13px;color:#6b7280;margin:0 0 12px;">Browse lab catalogs and send connection requests. Labs must accept before you can order tests.</p>

    <form method="GET" class="search-bar">
        <input type="text" name="q" value="{{ $search }}" placeholder="Search labs by name or city...">
        <button type="submit">Search</button>
        @if($search)<a href="{{ route('organisation.labs.index') }}" class="btn btn-outline">Clear</a>@endif
    </form>

    @if($availableLabs->count())
    <div class="lab-grid">
        @foreach($availableLabs as $lab)
        @php $labTests = \App\Models\ExternalLabTest::where('external_lab_id', $lab->id)->whereNull('organisation_id')->orderBy('test_name')->get(); @endphp
        <div class="lab-card">
            <div class="lab-name">{{ $lab->name }}</div>
            <div class="lab-city">📍 {{ $lab->city ?? 'City not set' }}{{ $lab->state ? ', '.$lab->state : '' }}</div>
            @if($lab->description)<div class="lab-meta">{{ Str::limit($lab->description, 80) }}</div>@endif
            <div class="lab-meta">{{ $lab->phone ?? '' }} {{ $lab->email ? '· '.$lab->email : '' }}</div>
            <div class="lab-tests" style="color:#16a34a;">{{ $labTests->count() }} tests available</div>

            @if($labTests->count())
            <button class="test-toggle" onclick="this.nextElementSibling.classList.toggle('open'); this.textContent = this.nextElementSibling.classList.contains('open') ? '▲ Hide catalog' : '▼ View test catalog & pricing'">▼ View test catalog & pricing</button>
            <div class="test-list">
                @foreach($labTests as $lt)
                <div class="test-row">
                    <span>{{ $lt->test_name }} <span style="color:#9ca3af;">({{ $lt->test_code }})</span></span>
                    <span style="font-weight:600;color:#111;">₹{{ number_format($lt->b2b_price) }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div style="margin-top:8px;">
                <form method="POST" action="{{ route('organisation.labs.onboard') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="lab_id" value="{{ $lab->id }}">
                    <button type="submit" class="btn btn-sm btn-request">Send Request</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty">
        @if($search) No labs found matching "{{ $search }}".
        @else No available labs found in your clinic cities. Try searching by name or city. @endif
    </div>
    @endif
</div>
@endsection
