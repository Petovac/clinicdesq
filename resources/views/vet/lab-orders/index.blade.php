@extends('layouts.vet')

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <h1 class="v-page-title">Lab Test Orders</h1>
    @if($reviewCount > 0)
        <a href="{{ route('vet.lab-orders.index', ['status' => 'results_uploaded']) }}" class="v-btn v-btn--primary v-btn--sm">
            {{ $reviewCount }} Awaiting Review
        </a>
    @endif
</div>

{{-- Status filters --}}
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('vet.lab-orders.index') }}"
       class="v-btn v-btn--sm {{ !$status ? 'v-btn--primary' : 'v-btn--outline' }}">All</a>
    @foreach(['ordered' => 'Ordered', 'routed' => 'Routed', 'processing' => 'Processing', 'results_uploaded' => 'Results Ready', 'approved' => 'Approved', 'retest_requested' => 'Retest'] as $key => $label)
        <a href="{{ route('vet.lab-orders.index', ['status' => $key]) }}"
           class="v-btn v-btn--sm {{ $status === $key ? 'v-btn--primary' : 'v-btn--outline' }}">{{ $label }}</a>
    @endforeach
</div>

@if($orders->isEmpty())
    <div class="v-card" style="text-align:center;padding:40px;color:var(--text-muted);">
        No lab orders found.
    </div>
@else
    <div class="v-card" style="padding:0;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:var(--bg-soft);border-bottom:1px solid var(--border);">
                    <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--text-muted);font-size:11px;text-transform:uppercase;">Order #</th>
                    <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--text-muted);font-size:11px;text-transform:uppercase;">Pet</th>
                    <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--text-muted);font-size:11px;text-transform:uppercase;">Tests</th>
                    <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--text-muted);font-size:11px;text-transform:uppercase;">Priority</th>
                    <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--text-muted);font-size:11px;text-transform:uppercase;">Status</th>
                    <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--text-muted);font-size:11px;text-transform:uppercase;">Lab</th>
                    <th style="padding:10px 14px;text-align:left;font-weight:600;color:var(--text-muted);font-size:11px;text-transform:uppercase;">Date</th>
                    <th style="padding:10px 14px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:10px 14px;font-weight:600;color:var(--primary);">{{ $order->order_number }}</td>
                        <td style="padding:10px 14px;">{{ $order->pet->name ?? '—' }} ({{ $order->pet->species ?? '' }})</td>
                        <td style="padding:10px 14px;">
                            @foreach($order->tests->take(3) as $test)
                                <span style="display:inline-block;background:#eff6ff;color:#1e40af;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;margin:1px;">{{ $test->test_name }}</span>
                            @endforeach
                            @if($order->tests->count() > 3)
                                <span style="font-size:11px;color:var(--text-muted);">+{{ $order->tests->count() - 3 }} more</span>
                            @endif
                        </td>
                        <td style="padding:10px 14px;">
                            @if($order->priority === 'urgent')
                                <span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">URGENT</span>
                            @else
                                <span style="color:var(--text-muted);font-size:12px;">Routine</span>
                            @endif
                        </td>
                        <td style="padding:10px 14px;">
                            <span class="v-badge v-badge--{{ str_replace('_', '-', $order->status) }}">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
                        </td>
                        <td style="padding:10px 14px;font-size:12px;color:var(--text-muted);">{{ $order->lab->name ?? ($order->routing === 'in_house' ? 'In-house' : 'Pending') }}</td>
                        <td style="padding:10px 14px;font-size:12px;color:var(--text-muted);">{{ $order->created_at->format('d M Y') }}</td>
                        <td style="padding:10px 14px;">
                            <a href="{{ route('vet.lab-orders.show', $order) }}" class="v-btn v-btn--outline v-btn--sm">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $orders->appends(request()->query())->links() }}
    </div>
@endif
@endsection

@section('head')
<style>
    .v-badge { display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;text-transform:capitalize; }
    .v-badge--ordered { background:#fef3c7;color:#92400e; }
    .v-badge--routed { background:#dbeafe;color:#1d4ed8; }
    .v-badge--processing { background:#e0e7ff;color:#4338ca; }
    .v-badge--results-uploaded { background:#d1fae5;color:#065f46; }
    .v-badge--vet-review { background:#fef3c7;color:#92400e; }
    .v-badge--approved { background:#dcfce7;color:#166534; }
    .v-badge--retest-requested { background:#fee2e2;color:#991b1b; }
</style>
@endsection
