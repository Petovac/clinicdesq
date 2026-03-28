@extends('organisation.layout')

@section('content')
<style>
.rv-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px; }
.rv-header h2 { font-size:22px;font-weight:700;margin:0; }
.clinic-filter { padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px; }
.stat-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px; }
.stat { background:#fff;padding:16px;border-radius:10px;border:1px solid #f0f0f0;text-align:center; }
.stat .value { font-size:28px;font-weight:700;color:#111827; }
.stat .label { font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-top:2px; }
.stat--gold .value { color:#f59e0b; }
.stat--green .value { color:#16a34a; }
.stat--blue .value { color:#2563eb; }
.card { background:#fff;border-radius:10px;border:1px solid #f0f0f0;padding:18px;margin-bottom:16px; }
.card h3 { font-size:15px;font-weight:700;margin:0 0 12px; }
.dist-bar { display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:13px; }
.dist-bar .stars { color:#f59e0b;font-weight:600;width:30px; }
.dist-bar .bar-wrap { flex:1;background:#f3f4f6;border-radius:4px;height:18px;overflow:hidden; }
.dist-bar .bar-fill { height:100%;background:#f59e0b;border-radius:4px;transition:width .3s; }
.dist-bar .count { color:#6b7280;width:30px;text-align:right; }
.sub-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:10px; }
.sub-item { text-align:center;padding:12px;background:#f9fafb;border-radius:8px; }
.sub-item .val { font-size:24px;font-weight:700;color:#f59e0b; }
.sub-item .lbl { font-size:11px;color:#6b7280;margin-top:2px; }
.review-list { }
.review-item { padding:14px 0;border-bottom:1px solid #f3f4f6; }
.review-item:last-child { border-bottom:none; }
.review-meta { display:flex;justify-content:space-between;align-items:center;margin-bottom:6px; }
.review-stars { color:#f59e0b;font-size:16px;letter-spacing:1px; }
.review-by { font-size:12px;color:#6b7280; }
.review-text { font-size:14px;color:#374151;line-height:1.6; }
.review-tags { display:flex;gap:6px;margin-top:6px; }
.tag { font-size:10px;padding:2px 8px;border-radius:10px;font-weight:600; }
.tag-recommend { background:#dcfce7;color:#166534; }
.tag-no { background:#fee2e2;color:#991b1b; }
.clinic-rank-table { width:100%;border-collapse:collapse;font-size:13px; }
.clinic-rank-table th { text-align:left;padding:8px 10px;font-weight:600;color:#6b7280;font-size:10px;text-transform:uppercase;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.clinic-rank-table td { padding:8px 10px;border-bottom:1px solid #f3f4f6; }
.clinic-rank-table tr:hover td { background:#f9fafb; }
.rating-display { display:inline-flex;align-items:center;gap:4px; }
.rating-display .num { font-weight:700;font-size:14px; }
.rating-display .star { color:#f59e0b; }
.chart-grid { display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px; }
@media (max-width:768px) { .chart-grid { grid-template-columns:1fr; } }
</style>

<div class="rv-header">
    <h2>⭐ Reviews & Feedback</h2>
    <select class="clinic-filter" onchange="location.href='?clinic='+this.value">
        <option value="">All Clinics</option>
        @foreach($clinics as $c)
        <option value="{{ $c->id }}" {{ $filterClinic == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
    </select>
</div>

{{-- Stats --}}
<div class="stat-grid">
    <div class="stat stat--gold">
        <div class="value">{{ $avgRating ? number_format($avgRating, 1) : '—' }}</div>
        <div class="label">Avg Rating</div>
    </div>
    <div class="stat stat--blue">
        <div class="value">{{ $totalReviews }}</div>
        <div class="label">Total Reviews</div>
    </div>
    <div class="stat stat--green">
        <div class="value">{{ $recommendPct }}%</div>
        <div class="label">Would Recommend</div>
    </div>
    <div class="stat">
        <div class="value">{{ $pendingReviews }}</div>
        <div class="label">Pending</div>
    </div>
    <div class="stat">
        <div class="value">{{ $gmbSent }}</div>
        <div class="label">GMB Prompts</div>
    </div>
</div>

<div class="chart-grid">
    {{-- Rating Distribution --}}
    <div class="card">
        <h3>Rating Distribution</h3>
        @foreach($distribution as $rating => $count)
        @php $pct = $totalReviews > 0 ? round($count / $totalReviews * 100) : 0; @endphp
        <div class="dist-bar">
            <span class="stars">{{ $rating }}★</span>
            <div class="bar-wrap"><div class="bar-fill" style="width:{{ $pct }}%"></div></div>
            <span class="count">{{ $count }}</span>
        </div>
        @endforeach
    </div>

    {{-- Sub Ratings --}}
    <div class="card">
        <h3>Category Ratings</h3>
        <div class="sub-grid">
            @foreach($subRatings as $label => $val)
            <div class="sub-item">
                <div class="val">{{ $val ? number_format($val, 1) : '—' }}</div>
                <div class="lbl">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="chart-grid">
    {{-- Per Clinic Ratings --}}
    <div class="card">
        <h3>Clinic Rankings</h3>
        @if(count($clinicRatings))
        <table class="clinic-rank-table">
            <thead><tr><th>Clinic</th><th>Rating</th><th>Reviews</th></tr></thead>
            <tbody>
            @foreach($clinicRatings as $cr)
            <tr>
                <td><strong>{{ $cr['clinic']->name }}</strong></td>
                <td>
                    @if($cr['avg_rating'])
                    <span class="rating-display"><span class="num">{{ $cr['avg_rating'] }}</span><span class="star">★</span></span>
                    @else — @endif
                </td>
                <td>{{ $cr['count'] }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <p style="color:#9ca3af;font-size:13px;text-align:center;padding:20px;">No reviews yet.</p>
        @endif
    </div>

    {{-- Per Vet Ratings --}}
    <div class="card">
        <h3>Doctor Ratings</h3>
        @if($vetRatings->count())
        <table class="clinic-rank-table">
            <thead><tr><th>Doctor</th><th>Rating</th><th>Reviews</th></tr></thead>
            <tbody>
            @foreach($vetRatings as $vr)
            <tr>
                <td><strong>{{ str_starts_with($vr->name, 'Dr') ? $vr->name : 'Dr. '.$vr->name }}</strong></td>
                <td><span class="rating-display"><span class="num">{{ number_format($vr->avg_rating, 1) }}</span><span class="star">★</span></span></td>
                <td>{{ $vr->review_count }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <p style="color:#9ca3af;font-size:13px;text-align:center;padding:20px;">No doctor ratings yet.</p>
        @endif
    </div>
</div>

{{-- Recent Reviews --}}
<div class="card">
    <h3>Recent Reviews</h3>
    @if($reviews->count())
    <div class="review-list">
        @foreach($reviews as $rv)
        <div class="review-item">
            <div class="review-meta">
                <div>
                    <span class="review-stars">@for($i=1;$i<=5;$i++){{ $i <= $rv->overall_rating ? '★' : '☆' }}@endfor</span>
                    <span style="font-weight:600;margin-left:6px;">{{ $rv->overall_rating }}/5</span>
                </div>
                <div class="review-by">
                    {{ $rv->petParent->name ?? 'Anonymous' }}
                    · {{ $rv->clinic->name }}
                    · {{ $rv->submitted_at?->diffForHumans() }}
                </div>
            </div>
            @if($rv->feedback)
            <div class="review-text">{{ $rv->feedback }}</div>
            @endif
            <div class="review-tags">
                @if($rv->would_recommend === true)<span class="tag tag-recommend">👍 Recommends</span>@endif
                @if($rv->would_recommend === false)<span class="tag tag-no">👎 Wouldn't recommend</span>@endif
                @if($rv->vet)<span class="tag" style="background:#dbeafe;color:#1d4ed8;">{{ str_starts_with($rv->vet->name, 'Dr') ? $rv->vet->name : 'Dr. '.$rv->vet->name }}</span>@endif
                @if($rv->appointment && $rv->appointment->pet)<span class="tag" style="background:#f3f4f6;color:#6b7280;">{{ $rv->appointment->pet->name }}</span>@endif
            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:12px;">{{ $reviews->appends(['clinic' => $filterClinic])->links() }}</div>
    @else
    <p style="color:#9ca3af;font-size:13px;text-align:center;padding:30px;">No reviews submitted yet. Reviews will appear here once pet parents submit feedback.</p>
    @endif
</div>
@endsection
