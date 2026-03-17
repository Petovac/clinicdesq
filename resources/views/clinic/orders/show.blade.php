@extends('clinic.layout')

@section('content')
<style>
.os-wrap { max-width: 800px; }
.os-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; }
.os-header h2 { margin:0; font-size:22px; font-weight:700; }
.os-meta { color:#6b7280; font-size:13px; margin-top:4px; }
.os-card { background:#fff; border-radius:10px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,.06); margin-bottom:20px; }
.os-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px 24px; margin-bottom:16px; }
.os-label { font-size:12px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:2px; }
.os-val { font-size:14px; font-weight:500; color:#1f2937; }
.os-table { width:100%; border-collapse:collapse; }
.os-table th { background:#f9fafb; padding:10px 14px; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; text-align:left; border-bottom:1px solid #e5e7eb; }
.os-table td { padding:12px 14px; font-size:14px; border-bottom:1px solid #f3f4f6; }
.os-table tr:hover { background:#f9fafb; }
.badge-status { padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; }
.s-draft { background:#f3f4f6; color:#374151; }
.s-submitted { background:#fef3c7; color:#92400e; }
.s-approved { background:#dbeafe; color:#1e40af; }
.s-fulfilled { background:#d1fae5; color:#065f46; }
.s-cancelled { background:#fee2e2; color:#991b1b; }
.btn { padding:8px 18px; border-radius:7px; font-size:14px; font-weight:600; border:none; cursor:pointer; text-decoration:none; display:inline-block; }
.btn-blue { background:#2563eb; color:#fff; }
.btn-blue:hover { background:#1d4ed8; }
.btn-outline { background:#fff; color:#374151; border:1px solid #d1d5db; }
.btn-outline:hover { background:#f9fafb; }
.btn-back { color:#6b7280; text-decoration:none; font-size:14px; }
.btn-back:hover { color:#374151; }
.alert-success { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px; }
</style>

<div class="os-wrap">
    <a href="{{ route('clinic.orders.index') }}" class="btn-back">← Back to Orders</a>

    @if(session('success'))
        <div class="alert-success" style="margin-top:12px;">{{ session('success') }}</div>
    @endif

    <div class="os-header" style="margin-top:12px;">
        <div>
            <h2>Order {{ $order->order_number }}</h2>
            <div class="os-meta">Created {{ $order->created_at->format('d M Y, h:i A') }} by {{ optional($order->createdBy)->name ?? '—' }}</div>
        </div>
        <span class="badge-status s-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
    </div>

    <div class="os-card">
        <div class="os-grid">
            <div>
                <div class="os-label">Order Type</div>
                <div class="os-val">{{ $order->order_type === 'vendor' ? 'Vendor (External)' : 'Organisation (Internal)' }}</div>
            </div>
            @if($order->order_type === 'vendor')
            <div>
                <div class="os-label">Vendor</div>
                <div class="os-val">{{ $order->vendor_name ?: '—' }}</div>
            </div>
            @endif
            <div>
                <div class="os-label">Status</div>
                <div class="os-val"><span class="badge-status s-{{ $order->status }}">{{ ucfirst($order->status) }}</span></div>
            </div>
            <div>
                <div class="os-label">Items</div>
                <div class="os-val">{{ $order->items->count() }} item(s)</div>
            </div>
        </div>

        @if($order->notes)
        <div style="margin-top:8px;">
            <div class="os-label">Notes</div>
            <div class="os-val">{{ $order->notes }}</div>
        </div>
        @endif
    </div>

    <div class="os-card">
        <h3 style="font-size:16px;font-weight:600;margin:0 0 14px;">Order Items</h3>
        <table class="os-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Qty Requested</th>
                    <th>Qty Fulfilled</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
            @foreach($order->items as $i => $line)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:500;">
                        {{ optional($line->inventoryItem)->name ?? '—' }}
                        @if(optional($line->inventoryItem)->strength)
                            <span style="color:#6b7280;font-size:12px;">({{ $line->inventoryItem->strength }})</span>
                        @endif
                    </td>
                    <td>
                        @if(optional($line->inventoryItem)->item_type === 'drug')
                            <span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;">Drug</span>
                        @else
                            <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;">Consumable</span>
                        @endif
                    </td>
                    <td>{{ $line->quantity_requested }}</td>
                    <td>{{ $line->quantity_fulfilled ?? '—' }}</td>
                    <td style="color:#6b7280;font-size:13px;">{{ $line->notes ?: '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if($order->status === 'draft')
    <div style="display:flex;gap:12px;">
        <form method="POST" action="{{ route('clinic.orders.submit', $order->id) }}">
            @csrf
            <button type="submit" class="btn btn-blue" onclick="return confirm('Submit this order? It cannot be edited after submission.')">Submit Order</button>
        </form>
        <a href="{{ route('clinic.orders.index') }}" class="btn btn-outline">Back</a>
    </div>
    @endif
</div>
@endsection
