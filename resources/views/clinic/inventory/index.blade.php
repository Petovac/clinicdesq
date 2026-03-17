@extends('clinic.layout')

@section('content')
<style>
.inv-wrap { max-width: 1100px; }
.inv-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:16px; flex-wrap:wrap; }
.inv-header h2 { margin:0; font-size:22px; font-weight:700; color:#111827; }
.search-box { padding:9px 14px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; width:280px; }
.inv-table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.06); }
.inv-table th { background:#f9fafb; padding:10px 16px; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; text-align:left; border-bottom:1px solid #e5e7eb; }
.inv-table td { padding:12px 16px; font-size:14px; border-bottom:1px solid #f3f4f6; }
.inv-table tr:hover { background:#f9fafb; }
.badge-ok { background:#d1fae5; color:#065f46; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; }
.badge-low { background:#fef3c7; color:#92400e; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; }
.badge-out { background:#fee2e2; color:#991b1b; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:600; }
.badge-type { background:#eff6ff; color:#1e40af; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:500; }
.btn-sm { padding:5px 12px; border-radius:6px; font-size:12px; font-weight:500; border:none; cursor:pointer; text-decoration:none; }
.btn-blue { background:#2563eb; color:#fff; }
.btn-blue:hover { background:#1d4ed8; }
.btn-grey { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }
.btn-grey:hover { background:#e5e7eb; }
.actions { display:flex; gap:6px; }
</style>

<div class="inv-wrap">
    <div class="inv-header">
        <h2>Inventory — Current Stock</h2>
        <form method="GET" action="{{ route('clinic.inventory.index') }}" style="display:flex;gap:8px;">
            <input type="text" name="q" class="search-box" placeholder="Search items..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn-sm btn-blue">Search</button>
            @if($search)
                <a href="{{ route('clinic.inventory.index') }}" class="btn-sm btn-grey">Clear</a>
            @endif
        </form>
    </div>

    <table class="inv-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Type</th>
                <th>Strength</th>
                <th>Package</th>
                <th style="text-align:right;">Available Qty</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td style="font-weight:500;">{{ $item->name }}</td>
                <td><span class="badge-type">{{ ucfirst($item->item_type) }}</span></td>
                <td>{{ $item->strength_value ? $item->strength_value . ' ' . $item->strength_unit : '—' }}</td>
                <td>{{ $item->package_type ? ucfirst($item->package_type) : '—' }}</td>
                <td style="text-align:right;font-weight:600;">{{ number_format($item->clinic_qty, 1) }}</td>
                <td>
                    @if($item->clinic_qty <= 0)
                        <span class="badge-out">Out of Stock</span>
                    @elseif($item->clinic_qty <= 5)
                        <span class="badge-low">Low Stock</span>
                    @else
                        <span class="badge-ok">In Stock</span>
                    @endif
                </td>
                <td>
                    <div class="actions">
                        <a href="{{ route('clinic.inventory.show', $item->id) }}" class="btn-sm btn-grey">View</a>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#9ca3af;padding:30px;">No inventory items found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
