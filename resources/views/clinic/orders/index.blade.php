@extends('clinic.layout')

@section('content')
<style>
.ord-wrap { max-width: 1000px; }
.ord-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.ord-header h2 { margin:0; font-size:22px; font-weight:700; }
.btn { padding:8px 18px; border-radius:7px; font-size:14px; font-weight:600; border:none; cursor:pointer; text-decoration:none; display:inline-block; }
.btn-blue { background:#2563eb; color:#fff; }
.btn-blue:hover { background:#1d4ed8; }
.ord-table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.06); }
.ord-table th { background:#f9fafb; padding:10px 16px; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; text-align:left; border-bottom:1px solid #e5e7eb; }
.ord-table td { padding:12px 16px; font-size:14px; border-bottom:1px solid #f3f4f6; }
.ord-table tr:hover { background:#f9fafb; }
.badge-status { padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; }
.s-draft { background:#f3f4f6; color:#374151; }
.s-submitted { background:#fef3c7; color:#92400e; }
.s-approved { background:#dbeafe; color:#1e40af; }
.s-fulfilled { background:#d1fae5; color:#065f46; }
.s-cancelled { background:#fee2e2; color:#991b1b; }
</style>

<div class="ord-wrap">
    <div class="ord-header">
        <h2>Order Requests</h2>
        <a href="{{ route('clinic.orders.create') }}" class="btn btn-blue">+ New Order</a>
    </div>

    <table class="ord-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Type</th>
                <th>Vendor / Org</th>
                <th>Items</th>
                <th>Status</th>
                <th>Created</th>
                <th>By</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td style="font-weight:600;">{{ $order->order_number }}</td>
                <td>{{ ucfirst($order->order_type) }}</td>
                <td>{{ $order->order_type === 'vendor' ? ($order->vendor_name ?: '—') : 'Organisation' }}</td>
                <td>{{ $order->items_count }}</td>
                <td><span class="badge-status s-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                <td style="font-size:13px;">{{ $order->created_at->format('d M Y') }}</td>
                <td style="font-size:13px;">{{ optional($order->createdBy)->name ?? '—' }}</td>
                <td><a href="{{ route('clinic.orders.show', $order->id) }}" style="color:#2563eb;font-size:13px;text-decoration:none;">View →</a></td>
            </tr>
        @empty
            <tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:30px;">No orders yet.</td></tr>
        @endforelse
        </tbody>
    </table>

    @if($orders->hasPages())
        <div style="margin-top:16px;">
            {{ $orders->links('pagination::simple-bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
