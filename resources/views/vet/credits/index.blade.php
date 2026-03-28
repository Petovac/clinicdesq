@extends('layouts.vet')

@section('content')
<style>
.credits-page { max-width:1000px; margin:0 auto; }
.credits-page h2 { font-size:22px; font-weight:700; margin-bottom:20px; }
.credit-balance-card {
    background:linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    color:#fff;
    border-radius:14px;
    padding:28px 32px;
    margin-bottom:24px;
    display:flex;
    align-items:center;
    justify-content:space-between;
}
.balance-info h3 { font-size:14px; font-weight:500; opacity:0.85; margin:0 0 4px; }
.balance-number { font-size:42px; font-weight:800; margin:0; line-height:1; }
.balance-sub { font-size:13px; opacity:0.7; margin-top:6px; }
.balance-stats { display:flex; gap:24px; }
.balance-stat { text-align:center; }
.balance-stat .val { font-size:20px; font-weight:700; }
.balance-stat .lbl { font-size:11px; opacity:0.7; }

.packs-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:16px; margin-bottom:28px; }
.pack-card {
    border:2px solid #e5e7eb;
    border-radius:12px;
    padding:22px;
    text-align:center;
    background:#fff;
    transition:border-color .2s, box-shadow .2s;
    position:relative;
}
.pack-card:hover { border-color:#3b82f6; box-shadow:0 4px 16px rgba(59,130,246,0.15); }
.pack-card.popular { border-color:#f59e0b; }
.pack-card.popular::before {
    content:'Most Popular';
    position:absolute;
    top:-10px;
    left:50%;
    transform:translateX(-50%);
    background:#f59e0b;
    color:#fff;
    font-size:10px;
    font-weight:700;
    padding:2px 10px;
    border-radius:10px;
}
.pack-name { font-size:16px; font-weight:700; color:#111827; margin-bottom:4px; }
.pack-credits { font-size:32px; font-weight:800; color:#2563eb; margin:8px 0; }
.pack-credits span { font-size:14px; font-weight:500; color:#6b7280; }
.pack-price { font-size:22px; font-weight:700; color:#111827; margin:8px 0; }
.pack-per { font-size:12px; color:#6b7280; margin-bottom:14px; }
.pack-btn {
    width:100%;
    padding:10px;
    border:none;
    border-radius:8px;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    background:#2563eb;
    color:#fff;
}
.pack-btn:hover { background:#1d4ed8; }

.cost-table { width:100%; border-collapse:collapse; margin-bottom:28px; font-size:14px; }
.cost-table th { text-align:left; padding:10px 14px; background:#f9fafb; font-size:12px; font-weight:600; color:#6b7280; border-bottom:1px solid #e5e7eb; }
.cost-table td { padding:10px 14px; border-bottom:1px solid #f3f4f6; }
.cost-badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:700; background:#dbeafe; color:#1d4ed8; }

.tx-table { width:100%; border-collapse:collapse; font-size:13px; }
.tx-table th { text-align:left; padding:8px 12px; background:#f9fafb; font-size:11px; font-weight:600; color:#6b7280; border-bottom:1px solid #e5e7eb; }
.tx-table td { padding:8px 12px; border-bottom:1px solid #f3f4f6; }
.tx-purchase { color:#16a34a; font-weight:600; }
.tx-deduction { color:#dc2626; font-weight:600; }
.card { background:#fff; border-radius:12px; padding:20px; border:1px solid #e5e7eb; margin-bottom:20px; }
.card-title { font-size:15px; font-weight:700; margin:0 0 14px; color:#374151; }
.success-bar { background:#dcfce7; border:1px solid #bbf7d0; padding:10px 14px; border-radius:6px; margin-bottom:14px; color:#166534; font-size:14px; }

.usage-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:16px; }
.usage-stat { background:#f0f9ff; border-radius:8px; padding:14px; text-align:center; }
.usage-stat .num { font-size:22px; font-weight:800; color:#0369a1; }
.usage-stat .lbl { font-size:11px; color:#6b7280; }
</style>

<div class="credits-page">

<h2>AI Credits</h2>

@if(session('success'))
<div class="success-bar">{{ session('success') }}</div>
@endif

{{-- Balance Card --}}
<div class="credit-balance-card">
    <div class="balance-info">
        <h3>Available Credits</h3>
        <p class="balance-number">{{ $credit->balance }}</p>
        <p class="balance-sub">credits remaining</p>
    </div>
    <div class="balance-stats">
        <div class="balance-stat">
            <div class="val">{{ $credit->total_purchased }}</div>
            <div class="lbl">Total Purchased</div>
        </div>
        <div class="balance-stat">
            <div class="val">{{ $credit->total_used }}</div>
            <div class="lbl">Total Used</div>
        </div>
    </div>
</div>

{{-- Credit Packs --}}
<div class="card">
    <div class="card-title">Purchase Credit Packs</div>
    <div class="packs-grid">
        @foreach($packs as $key => $pack)
        <div class="pack-card {{ $key === 'standard' ? 'popular' : '' }}">
            <div class="pack-name">{{ $pack['name'] }}</div>
            <div class="pack-credits">{{ $pack['credits'] }} <span>credits</span></div>
            <div class="pack-price">₹{{ number_format($pack['price']) }}</div>
            <div class="pack-per">₹{{ $pack['per_credit'] }} per credit</div>
            <form method="POST" action="{{ route('vet.credits.purchase') }}">
                @csrf
                <input type="hidden" name="pack" value="{{ $key }}">
                <button type="submit" class="pack-btn">Buy Now</button>
            </form>
        </div>
        @endforeach
    </div>
</div>

{{-- Usage Summary --}}
@if($usageStats && $usageStats->total_requests > 0)
<div class="card">
    <div class="card-title">Your AI Usage</div>
    <div class="usage-grid">
        <div class="usage-stat">
            <div class="num">{{ $usageStats->total_requests }}</div>
            <div class="lbl">Total AI Requests</div>
        </div>
        <div class="usage-stat">
            <div class="num">{{ $credit->total_used }}</div>
            <div class="lbl">Credits Used</div>
        </div>
        <div class="usage-stat">
            <div class="num">{{ $credit->balance }}</div>
            <div class="lbl">Credits Remaining</div>
        </div>
    </div>
    @if($featureBreakdown->isNotEmpty())
    <table class="cost-table">
        <thead>
            <tr>
                <th>Feature</th>
                <th>Times Used</th>
                <th>Credits Per Use</th>
                <th>Total Credits Used</th>
            </tr>
        </thead>
        <tbody>
            @foreach($featureBreakdown as $fb)
            <tr>
                <td style="font-weight:600;">{{ ucwords(str_replace('_', ' ', $fb->ai_feature)) }}</td>
                <td>{{ $fb->uses }}</td>
                <td><span class="cost-badge">1 credit</span></td>
                <td style="font-weight:600;">{{ $fb->uses }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endif

{{-- Credit Cost Per Feature --}}
<div class="card">
    <div class="card-title">Credit Cost Per AI Feature</div>
    <table class="cost-table">
        <thead>
            <tr>
                <th>AI Feature</th>
                <th>Description</th>
                <th>Credits</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight:600;">Clinical Insights</td>
                <td style="color:#6b7280;">AI analysis of case sheet with recommendations</td>
                <td><span class="cost-badge">{{ $creditCosts['clinical_insights'] }} credit</span></td>
            </tr>
            <tr>
                <td style="font-weight:600;">Text Refinement</td>
                <td style="color:#6b7280;">Polish clinical documentation text</td>
                <td><span class="cost-badge">{{ $creditCosts['refine'] }} credit</span></td>
            </tr>
            <tr>
                <td style="font-weight:600;">Senior Vet Guidance</td>
                <td style="color:#6b7280;">Comprehensive case review with history and diagnostics</td>
                <td><span class="cost-badge">{{ $creditCosts['senior_support'] }} credit</span></td>
            </tr>
            <tr>
                <td style="font-weight:600;">Prescription Support</td>
                <td style="color:#6b7280;">Drug selection and dosage guidance with safety checks</td>
                <td><span class="cost-badge">{{ $creditCosts['prescription_support'] }} credit</span></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Transaction History --}}
<div class="card">
    <div class="card-title">Transaction History</div>
    @if($transactions->isEmpty())
        <p style="text-align:center;color:#9ca3af;padding:20px;font-size:13px;">No transactions yet. Purchase a credit pack to get started.</p>
    @else
    <table class="tx-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Credits</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            <tr>
                <td style="color:#6b7280;">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                <td>
                    @if($tx->type === 'purchase' || $tx->type === 'bonus')
                        <span style="font-size:10px;padding:2px 8px;border-radius:10px;background:#dcfce7;color:#166534;font-weight:600;">{{ ucfirst($tx->type) }}</span>
                    @elseif($tx->type === 'deduction')
                        <span style="font-size:10px;padding:2px 8px;border-radius:10px;background:#fee2e2;color:#991b1b;font-weight:600;">Used</span>
                    @else
                        <span style="font-size:10px;padding:2px 8px;border-radius:10px;background:#dbeafe;color:#1d4ed8;font-weight:600;">{{ ucfirst($tx->type) }}</span>
                    @endif
                </td>
                <td>{{ $tx->description }}</td>
                <td>
                    @if($tx->type === 'deduction')
                        <span class="tx-deduction">-{{ $tx->credits }}</span>
                    @else
                        <span class="tx-purchase">+{{ $tx->credits }}</span>
                    @endif
                </td>
                <td style="font-weight:600;">{{ $tx->balance_after }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:12px;">{{ $transactions->links() }}</div>
    @endif
</div>

</div>
@endsection
