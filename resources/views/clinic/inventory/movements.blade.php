@extends('clinic.layout')

@section('content')
<style>
.mov-wrap { max-width: 1100px; }
.mov-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
.mov-header h2 { margin:0; font-size:22px; font-weight:700; }
.filter-row { display:flex; gap:8px; align-items:center; }
.filter-row select { padding:8px 12px; border:1px solid #e5e7eb; border-radius:7px; font-size:13px; }
.filter-row a { padding:7px 14px; border-radius:7px; font-size:13px; background:#f3f4f6; color:#374151; text-decoration:none; border:1px solid #e5e7eb; }
.mov-table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.06); }
.mov-table th { background:#f9fafb; padding:10px 16px; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; text-align:left; border-bottom:1px solid #e5e7eb; }
.mov-table td { padding:10px 16px; font-size:14px; border-bottom:1px solid #f3f4f6; }
.badge-m { padding:2px 8px; border-radius:999px; font-size:11px; font-weight:600; }
.bg-purchase { background:#d1fae5; color:#065f46; }
.bg-adjustment { background:#fef3c7; color:#92400e; }
.bg-usage { background:#fee2e2; color:#991b1b; }
.bg-transfer { background:#eff6ff; color:#1e40af; }
.bg-expired { background:#fce7f3; color:#9d174d; }
.pagination { margin-top:16px; display:flex; gap:6px; }
.pagination a, .pagination span { padding:6px 12px; border-radius:6px; font-size:13px; text-decoration:none; border:1px solid #e5e7eb; }
.pagination span { background:#2563eb; color:#fff; border-color:#2563eb; }
</style>

<div class="mov-wrap">
    <div class="mov-header">
        <h2>Stock Movements</h2>
        <div class="filter-row">
            <form method="GET" action="{{ route('clinic.inventory.movements') }}" style="display:flex;gap:8px;">
                <select name="type" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    @foreach(['purchase','transfer_in','transfer_out','treatment_usage','manual_adjustment','expired'] as $t)
                        <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                    @endforeach
                </select>
            </form>
            @if($type)
                <a href="{{ route('clinic.inventory.movements') }}">Clear Filter</a>
            @endif
        </div>
    </div>

    <table class="mov-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Item</th>
                <th>Type</th>
                <th style="text-align:right;">Qty</th>
                <th>Notes</th>
                <th>By</th>
            </tr>
        </thead>
        <tbody>
        @forelse($movements as $m)
            <tr>
                <td style="font-size:13px;white-space:nowrap;">{{ $m->created_at ? $m->created_at->format('d M Y H:i') : '—' }}</td>
                <td style="font-weight:500;">{{ optional($m->inventoryItem)->name ?? '—' }}</td>
                <td>
                    @php
                        $cls = match($m->movement_type) {
                            'purchase' => 'bg-purchase',
                            'manual_adjustment' => 'bg-adjustment',
                            'treatment_usage' => 'bg-usage',
                            'transfer_in','transfer_out' => 'bg-transfer',
                            'expired' => 'bg-expired',
                            default => 'bg-adjustment',
                        };
                    @endphp
                    <span class="badge-m {{ $cls }}">{{ ucfirst(str_replace('_',' ',$m->movement_type)) }}</span>
                </td>
                <td style="text-align:right;font-weight:600;{{ $m->quantity < 0 ? 'color:#dc2626;' : 'color:#065f46;' }}">
                    {{ $m->quantity > 0 ? '+' : '' }}{{ number_format($m->quantity, 1) }}
                </td>
                <td style="font-size:13px;color:#6b7280;max-width:250px;">{{ $m->notes ?: '—' }}</td>
                <td style="font-size:13px;">{{ optional($m->createdBy)->name ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:30px;">No movements found.</td></tr>
        @endforelse
        </tbody>
    </table>

    @if($movements->hasPages())
        <div class="pagination">
            {{ $movements->appends(request()->query())->links('pagination::simple-bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
