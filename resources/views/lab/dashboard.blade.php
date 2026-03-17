@extends('layouts.lab')

@section('content')
<h1 class="page-title">Dashboard</h1>

<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:24px;">
    <div class="card" style="text-align:center;">
        <div style="font-size:28px;font-weight:800;color:#f59e0b;">{{ $counts['pending'] }}</div>
        <div style="font-size:12px;color:var(--text-muted);font-weight:600;">Pending</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size:28px;font-weight:800;color:#4338ca;">{{ $counts['processing'] }}</div>
        <div style="font-size:12px;color:var(--text-muted);font-weight:600;">Processing</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size:28px;font-weight:800;color:#065f46;">{{ $counts['uploaded'] }}</div>
        <div style="font-size:12px;color:var(--text-muted);font-weight:600;">Results Sent</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size:28px;font-weight:800;color:#166534;">{{ $counts['completed'] }}</div>
        <div style="font-size:12px;color:var(--text-muted);font-weight:600;">Approved</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size:28px;font-weight:800;color:#dc2626;">{{ $counts['retest'] }}</div>
        <div style="font-size:12px;color:var(--text-muted);font-weight:600;">Retest</div>
    </div>
</div>

<h2 style="font-size:16px;font-weight:700;margin-bottom:14px;">Active Orders</h2>

@if($recentOrders->isEmpty())
    <div class="card" style="text-align:center;padding:40px;color:var(--text-muted);">
        No active orders at this time.
    </div>
@else
    @foreach($recentOrders as $order)
        <div class="card" style="margin-bottom:12px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-weight:700;color:var(--primary);font-size:14px;">{{ $order->order_number }}</span>
                    @if($order->priority === 'urgent')
                        <span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700;">URGENT</span>
                    @endif
                    <span class="status-badge status-{{ $order->status }}">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
                </div>
                <a href="{{ route('lab.orders.show', $order) }}" class="btn btn-primary btn-sm">Process</a>
            </div>
            <div style="display:flex;gap:20px;font-size:13px;margin-bottom:8px;">
                <div><span style="color:var(--text-muted);">Pet:</span> <strong>{{ $order->pet->name ?? '—' }}</strong> ({{ $order->pet->species ?? '' }})</div>
                <div><span style="color:var(--text-muted);">Clinic:</span> {{ $order->clinic->name ?? '—' }}</div>
                <div><span style="color:var(--text-muted);">Ordered:</span> {{ $order->created_at->format('d M Y') }}</div>
            </div>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                @foreach($order->tests as $test)
                    <span style="background:#eff6ff;color:#1e40af;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;">{{ $test->test_name }}</span>
                @endforeach
            </div>
        </div>
    @endforeach
@endif
@endsection
