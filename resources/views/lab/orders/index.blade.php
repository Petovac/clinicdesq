@extends('layouts.lab')

@section('content')
<h1 class="page-title">All Orders</h1>

<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('lab.orders.index') }}"
       class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-outline' }}">All</a>
    @foreach(['routed' => 'New', 'processing' => 'Processing', 'results_uploaded' => 'Submitted', 'approved' => 'Approved', 'retest_requested' => 'Retest'] as $key => $label)
        <a href="{{ route('lab.orders.index', ['status' => $key]) }}"
           class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-outline' }}">{{ $label }}</a>
    @endforeach
</div>

@if($orders->isEmpty())
    <div class="card" style="text-align:center;padding:40px;color:var(--text-muted);">No orders found.</div>
@else
    <div class="card" style="padding:0;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f9fafb;border-bottom:1px solid var(--border);">
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Order #</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Pet</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Clinic</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Tests</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Status</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Date</th>
                    <th style="padding:10px 14px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:10px 14px;font-weight:600;color:var(--primary);">
                            {{ $order->order_number }}
                            @if($order->priority === 'urgent')
                                <span style="background:#fee2e2;color:#991b1b;padding:1px 6px;border-radius:8px;font-size:9px;font-weight:700;margin-left:4px;">URGENT</span>
                            @endif
                        </td>
                        <td style="padding:10px 14px;">{{ $order->pet->name ?? '—' }}</td>
                        <td style="padding:10px 14px;">{{ $order->clinic->name ?? '—' }}</td>
                        <td style="padding:10px 14px;">{{ $order->tests->pluck('test_name')->implode(', ') }}</td>
                        <td style="padding:10px 14px;">
                            <span class="status-badge status-{{ $order->status }}">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
                        </td>
                        <td style="padding:10px 14px;color:var(--text-muted);">{{ $order->created_at->format('d M') }}</td>
                        <td style="padding:10px 14px;">
                            <a href="{{ route('lab.orders.show', $order) }}" class="btn btn-outline btn-sm">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $orders->appends(request()->query())->links() }}</div>
@endif
@endsection
