@extends('organisation.layout')

@section('content')
<style>
/* ── Page ── */
.co-header { margin-bottom: 24px; }
.co-header h2 { font-size: 22px; font-weight: 700; margin: 0 0 4px; }
.co-header p { color: #6b7280; font-size: 14px; margin: 0; }

/* ── Clinic selector ── */
.clinic-selector { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding: 16px 20px; background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.clinic-selector label { font-size: 13px; font-weight: 600; color: #374151; white-space: nowrap; }
.clinic-selector select { flex: 1; max-width: 360px; padding: 9px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff; cursor: pointer; }
.clinic-selector select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }

/* ── Summary strip ── */
.co-summary { display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
.co-stat { background: #fff; border-radius: 10px; padding: 14px 20px; flex: 1; min-width: 140px; box-shadow: 0 1px 4px rgba(0,0,0,.06); border-left: 4px solid; }
.co-stat.s-total { border-left-color: #2563eb; }
.co-stat.s-instock { border-left-color: #10b981; }
.co-stat.s-low { border-left-color: #f59e0b; }
.co-stat.s-out { border-left-color: #ef4444; }
.co-stat .cs-num { font-size: 22px; font-weight: 700; line-height: 1; }
.co-stat .cs-label { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-top: 2px; }

/* ── Tabs ── */
.co-tabs { display: flex; gap: 0; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; }
.co-tab { padding: 10px 24px; font-size: 14px; font-weight: 600; color: #6b7280; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all .15s; background: none; border-top: none; border-left: none; border-right: none; }
.co-tab:hover { color: #374151; }
.co-tab.active { color: #2563eb; border-bottom-color: #2563eb; }

/* ── Table ── */
.co-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
.co-table th { background: #f9fafb; padding: 10px 16px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .04em; text-align: left; border-bottom: 2px solid #e5e7eb; }
.co-table td { padding: 11px 16px; font-size: 14px; border-bottom: 1px solid #f3f4f6; color: #334155; }
.co-table tr:hover td { background: #f9fafb; }
.co-table tr:last-child td { border-bottom: none; }

/* ── Badges ── */
.badge-ok { background: #d1fae5; color: #065f46; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
.badge-low { background: #fef3c7; color: #92400e; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
.badge-out { background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
.badge-type { background: #eff6ff; color: #1e40af; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 500; }
.badge-type.consumable { background: #fef3c7; color: #b45309; }
.badge-type.surgical { background: #fce7f3; color: #be185d; }
.badge-type.product { background: #ecfdf5; color: #059669; }

/* ── Movement badges ── */
.badge-m { padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
.bg-purchase { background: #d1fae5; color: #065f46; }
.bg-adjustment { background: #fef3c7; color: #92400e; }
.bg-usage { background: #fee2e2; color: #991b1b; }
.bg-transfer { background: #dbeafe; color: #1e40af; }
.bg-expired { background: #fce7f3; color: #9d174d; }

/* ── Filter row ── */
.filter-row { display: flex; gap: 8px; align-items: center; margin-bottom: 16px; flex-wrap: wrap; }
.filter-row select { padding: 7px 12px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 13px; background: #fff; }
.filter-row select:focus { outline: none; border-color: #2563eb; }

/* ── Search ── */
.co-search { display: flex; gap: 8px; }
.co-search input { padding: 7px 12px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 13px; width: 220px; }
.co-search input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,.1); }
.co-search button { background: #2563eb; color: #fff; border: none; padding: 7px 14px; border-radius: 7px; font-size: 13px; font-weight: 600; cursor: pointer; }

/* ── Tab panel ── */
.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* ── Empty ── */
.co-empty { text-align: center; padding: 50px 20px; color: #9ca3af; font-size: 14px; }
</style>

{{-- Header --}}
<div class="co-header">
    <h2>Clinic Inventory</h2>
    <p>View stock levels and movement logs for individual clinics.</p>
</div>

@if(!$clinic)
    <div class="co-empty">No clinics found in this organisation.</div>
@else

{{-- Clinic Selector --}}
<div class="clinic-selector">
    <label>Select Clinic:</label>
    <select onchange="window.location.href='/organisation/clinic-inventory/' + this.value">
        @foreach($clinics as $c)
            <option value="{{ $c->id }}" {{ $clinic->id == $c->id ? 'selected' : '' }}>
                {{ $c->name }} {{ $c->city ? '— '.$c->city : '' }}
            </option>
        @endforeach
    </select>
</div>

{{-- Summary --}}
@php
    $totalItems = $items->count();
    $inStockCount = $items->where('clinic_qty', '>', 5)->count();
    $lowStockCount = $items->filter(fn($i) => $i->clinic_qty > 0 && $i->clinic_qty <= 5)->count();
    $outOfStockCount = $items->where('clinic_qty', '<=', 0)->count();
@endphp
<div class="co-summary">
    <div class="co-stat s-total">
        <div class="cs-num" style="color:#2563eb;">{{ $totalItems }}</div>
        <div class="cs-label">Total Items</div>
    </div>
    <div class="co-stat s-instock">
        <div class="cs-num" style="color:#10b981;">{{ $inStockCount }}</div>
        <div class="cs-label">In Stock</div>
    </div>
    <div class="co-stat s-low">
        <div class="cs-num" style="color:#f59e0b;">{{ $lowStockCount }}</div>
        <div class="cs-label">Low Stock</div>
    </div>
    <div class="co-stat s-out">
        <div class="cs-num" style="color:#ef4444;">{{ $outOfStockCount }}</div>
        <div class="cs-label">Out of Stock</div>
    </div>
</div>

{{-- Tabs --}}
<div class="co-tabs">
    <button class="co-tab active" onclick="switchTab('stock', this)">Stock Overview</button>
    <button class="co-tab" onclick="switchTab('movements', this)">Movement Log ({{ $movements->count() }})</button>
</div>

{{-- Tab: Stock --}}
<div class="tab-panel active" id="tab-stock">
    <form method="GET" action="{{ route('organisation.inventory.clinic-overview', $clinic->id) }}" class="co-search" style="margin-bottom:16px;">
        <input type="text" name="q" placeholder="Search items..." value="{{ $search ?? '' }}">
        <button type="submit">Search</button>
        @if($search)
            <a href="{{ route('organisation.inventory.clinic-overview', $clinic->id) }}" style="padding:7px 14px; border-radius:7px; font-size:13px; background:#f3f4f6; color:#374151; text-decoration:none; border:1px solid #e5e7eb;">Clear</a>
        @endif
    </form>

    <table class="co-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Type</th>
                <th>Strength</th>
                <th>Package</th>
                <th style="text-align:right;">Available Qty</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td style="font-weight:600;">{{ $item->name }}</td>
                <td>
                    <span class="badge-type {{ $item->item_type }}">{{ ucfirst($item->item_type) }}</span>
                </td>
                <td>{{ $item->strength_value ? $item->strength_value . ' ' . $item->strength_unit : '—' }}</td>
                <td>{{ $item->package_type ? ucfirst($item->package_type) : '—' }}</td>
                <td style="text-align:right; font-weight:700; font-size:15px;">{{ number_format($item->clinic_qty, 1) }}</td>
                <td>
                    @if($item->clinic_qty <= 0)
                        <span class="badge-out">Out of Stock</span>
                    @elseif($item->clinic_qty <= 5)
                        <span class="badge-low">Low Stock</span>
                    @else
                        <span class="badge-ok">In Stock</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="co-empty">No inventory items found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Tab: Movements --}}
<div class="tab-panel" id="tab-movements">
    <div class="filter-row">
        <select id="movTypeFilter" onchange="filterMovements()">
            <option value="">All Types</option>
            @foreach(['purchase','transfer_in','transfer_out','treatment_usage','manual_adjustment','expired'] as $t)
                <option value="{{ $t }}" {{ $movementType === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
            @endforeach
        </select>
    </div>

    <table class="co-table">
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
        <tbody id="movBody">
        @forelse($movements as $m)
            <tr data-type="{{ $m->movement_type }}">
                <td style="font-size:13px; white-space:nowrap;">{{ $m->created_at ? $m->created_at->format('d M Y H:i') : '—' }}</td>
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
                <td style="text-align:right; font-weight:600; {{ $m->quantity < 0 ? 'color:#dc2626;' : 'color:#065f46;' }}">
                    {{ $m->quantity > 0 ? '+' : '' }}{{ number_format($m->quantity, 1) }}
                </td>
                <td style="font-size:13px; color:#6b7280; max-width:280px;">{{ $m->notes ?: '—' }}</td>
                <td style="font-size:13px;">{{ optional($m->createdBy)->name ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="co-empty">No movements recorded for this clinic.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<script>
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.co-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    btn.classList.add('active');
}

function filterMovements() {
    const type = document.getElementById('movTypeFilter').value;
    document.querySelectorAll('#movBody tr').forEach(row => {
        if (!type || row.dataset.type === type) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

@endif
@endsection
