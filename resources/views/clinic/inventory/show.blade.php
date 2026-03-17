@extends('clinic.layout')

@section('content')
<style>
.inv-wrap { max-width: 1000px; }
.back-link { color:#2563eb; text-decoration:none; font-size:14px; }
.back-link:hover { text-decoration:underline; }
.item-header { margin:12px 0 24px; }
.item-header h2 { margin:0 0 4px; font-size:22px; font-weight:700; }
.item-meta { font-size:13px; color:#6b7280; }
.section { background:#fff; border:1px solid #e5e7eb; border-radius:10px; margin-bottom:20px; overflow:hidden; }
.section-title { padding:12px 20px; font-size:13px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:#6b7280; background:#f9fafb; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; }
table { width:100%; border-collapse:collapse; }
th { padding:10px 16px; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; text-align:left; border-bottom:1px solid #e5e7eb; }
td { padding:10px 16px; font-size:14px; border-bottom:1px solid #f3f4f6; }
.badge-movement { padding:2px 8px; border-radius:999px; font-size:11px; font-weight:600; }
.badge-purchase { background:#d1fae5; color:#065f46; }
.badge-adjustment { background:#fef3c7; color:#92400e; }
.badge-usage { background:#fee2e2; color:#991b1b; }
.badge-transfer { background:#eff6ff; color:#1e40af; }
.btn { padding:7px 16px; border-radius:7px; font-size:13px; font-weight:500; border:none; cursor:pointer; text-decoration:none; display:inline-block; }
.btn-blue { background:#2563eb; color:#fff; }
.btn-amber { background:#f59e0b; color:#fff; }
.add-stock-form { padding:16px 20px; display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; }
.add-stock-form label { font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:4px; }
.add-stock-form input { padding:7px 10px; border:1px solid #e5e7eb; border-radius:6px; font-size:14px; }
.alert-success { background:#d1fae5;border:1px solid #6ee7b7;border-radius:8px;padding:12px 16px;color:#065f46;margin-bottom:16px;font-size:14px; }
</style>

<div class="inv-wrap">
    <a href="{{ route('clinic.inventory.index') }}" class="back-link">← Back to Inventory</a>

    <div class="item-header">
        <h2>{{ $item->name }}</h2>
        <div class="item-meta">
            {{ ucfirst($item->item_type) }}
            @if($item->strength_value) · {{ $item->strength_value }} {{ $item->strength_unit }} @endif
            @if($item->package_type) · {{ ucfirst($item->package_type) }} @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- BATCHES --}}
    <div class="section">
        <div class="section-title">
            <span>Stock Batches</span>
            <span style="font-size:14px;font-weight:700;color:#111827;">
                Total: {{ number_format($batches->sum('quantity'), 1) }}
            </span>
        </div>
        <table>
            <thead><tr><th>Batch #</th><th style="text-align:right;">Qty</th><th>Expiry</th><th>Purchase Price</th></tr></thead>
            <tbody>
            @forelse($batches as $batch)
                <tr>
                    <td>{{ $batch->batch_number ?: '—' }}</td>
                    <td style="text-align:right;font-weight:600;">{{ number_format($batch->quantity, 1) }}</td>
                    <td>
                        @if($batch->expiry_date)
                            <span style="{{ \Carbon\Carbon::parse($batch->expiry_date)->isPast() ? 'color:#dc2626;font-weight:600;' : '' }}">
                                {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $batch->purchase_price ? '₹' . number_format($batch->purchase_price, 2) : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:20px;">No batches at this clinic.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- ADD STOCK FORM --}}
    @if(auth()->user()->hasPermission('inventory.manage'))
    <div class="section">
        <div class="section-title"><span>Add Stock</span></div>
        <form method="POST" action="{{ route('clinic.inventory.addStock') }}">
            @csrf
            <input type="hidden" name="inventory_item_id" value="{{ $item->id }}">
            <div class="add-stock-form">
                <div>
                    <label>Batch #</label>
                    <input type="text" name="batch_number" placeholder="Optional">
                </div>
                <div>
                    <label>Quantity</label>
                    <input type="number" name="quantity" step="0.001" min="0.001" required style="width:100px;">
                </div>
                <div>
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date">
                </div>
                <div>
                    <label>Purchase Price (₹)</label>
                    <input type="number" name="purchase_price" step="0.01" min="0" style="width:110px;">
                </div>
                <button type="submit" class="btn btn-blue">+ Add Stock</button>
            </div>
        </form>
    </div>
    @endif

    {{-- MOVEMENT HISTORY --}}
    <div class="section">
        <div class="section-title"><span>Recent Movements</span></div>
        <table>
            <thead><tr><th>Date</th><th>Type</th><th style="text-align:right;">Qty</th><th>Notes</th><th>By</th></tr></thead>
            <tbody>
            @forelse($movements as $m)
                <tr>
                    <td style="font-size:13px;">{{ $m->created_at ? $m->created_at->format('d M Y H:i') : '—' }}</td>
                    <td>
                        @php
                            $typeClass = match($m->movement_type) {
                                'purchase' => 'badge-purchase',
                                'manual_adjustment' => 'badge-adjustment',
                                'treatment_usage' => 'badge-usage',
                                'transfer_in', 'transfer_out' => 'badge-transfer',
                                default => 'badge-adjustment',
                            };
                        @endphp
                        <span class="badge-movement {{ $typeClass }}">{{ str_replace('_', ' ', ucfirst($m->movement_type)) }}</span>
                    </td>
                    <td style="text-align:right;font-weight:600;{{ $m->quantity < 0 ? 'color:#dc2626;' : 'color:#065f46;' }}">
                        {{ $m->quantity > 0 ? '+' : '' }}{{ number_format($m->quantity, 1) }}
                    </td>
                    <td style="font-size:13px;color:#6b7280;">{{ $m->notes ?: '—' }}</td>
                    <td style="font-size:13px;">{{ optional($m->createdBy)->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:20px;">No movements recorded.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
