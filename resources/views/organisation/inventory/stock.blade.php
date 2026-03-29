@extends('organisation.layout')

@section('content')
<style>
/* ── Page ── */
.stock-header { margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
.stock-header div h2 { font-size: 22px; font-weight: 700; margin: 0 0 4px; }
.stock-header div p { color: #6b7280; font-size: 14px; margin: 0; }
.stock-search { display: flex; gap: 8px; }
.stock-search input { padding: 8px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; width: 240px; }
.stock-search input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.stock-search button { background: #2563eb; color: #fff; border: none; padding: 8px 18px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; }
.stock-search button:hover { background: #1d4ed8; }

/* ── Summary cards ── */
.summary-row { display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
.summary-card { background: #fff; border-radius: 12px; padding: 18px 22px; flex: 1; min-width: 160px; box-shadow: 0 1px 4px rgba(0,0,0,.06); border-left: 4px solid; }
.summary-card.sc-total { border-left-color: #2563eb; }
.summary-card.sc-central { border-left-color: #8b5cf6; }
.summary-card.sc-low { border-left-color: #f59e0b; }
.summary-card.sc-out { border-left-color: #ef4444; }
.summary-card .sc-num { font-size: 26px; font-weight: 700; line-height: 1; }
.summary-card .sc-label { font-size: 12px; color: #6b7280; margin-top: 4px; text-transform: uppercase; letter-spacing: .5px; }

/* ── Item card ── */
.stock-item { background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.06); margin-bottom: 14px; overflow: hidden; transition: box-shadow .2s; }
.stock-item:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); }
.si-main { padding: 18px 24px; display: flex; align-items: center; gap: 20px; }
.si-info { flex: 1; }
.si-name { font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 4px; }
.si-meta { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.si-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .3px; }
.si-badge.drug { background: #eff6ff; color: #2563eb; }
.si-badge.consumable { background: #fef3c7; color: #b45309; }
.si-badge.product { background: #ecfdf5; color: #059669; }
.si-badge.surgical { background: #fce7f3; color: #be185d; }
.si-detail { font-size: 12px; color: #9ca3af; }

/* ── Stock numbers ── */
.si-stocks { display: flex; gap: 16px; align-items: center; }
.si-stock-box { text-align: center; min-width: 70px; padding: 8px 12px; border-radius: 8px; }
.si-stock-box.central { background: #f5f3ff; }
.si-stock-box.total-good { background: #ecfdf5; }
.si-stock-box.total-warn { background: #fffbeb; }
.si-stock-box.total-danger { background: #fef2f2; }
.si-stock-box .sb-num { font-size: 20px; font-weight: 800; line-height: 1; }
.si-stock-box.central .sb-num { color: #7c3aed; }
.si-stock-box.total-good .sb-num { color: #059669; }
.si-stock-box.total-warn .sb-num { color: #d97706; }
.si-stock-box.total-danger .sb-num { color: #dc2626; }
.si-stock-box .sb-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-top: 2px; }

/* ── Actions ── */
.si-actions { padding: 0 24px 14px; display: flex; gap: 8px; }
.si-actions .btn-act { padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; background: #fff; color: #374151; cursor: pointer; transition: all .15s; }
.si-actions .btn-act:hover { background: #f3f4f6; border-color: #9ca3af; }
.si-actions .btn-act.primary { background: #8b5cf6; color: #fff; border-color: #8b5cf6; }
.si-actions .btn-act.primary:hover { background: #7c3aed; }

/* ── Panel ── */
.si-panel { border-top: 1px solid #f3f4f6; background: #fafbfc; padding: 20px 24px; display: none; }
.si-panel.show { display: block; }
.si-panel h6 { font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin: 0 0 10px; }

/* ── Batch table ── */
.batch-tbl { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
.batch-tbl th { background: #f1f5f9; color: #475569; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; padding: 8px 12px; border-bottom: 2px solid #e2e8f0; }
.batch-tbl td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.batch-tbl tr:last-child td { border-bottom: none; }
.batch-tbl tr:hover td { background: #f8fafc; }
.loc-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
.loc-central { background: #ede9fe; color: #7c3aed; }
.loc-clinic { background: #dbeafe; color: #2563eb; }
.exp-warn { color: #dc2626; font-weight: 600; }

/* ── Add stock form ── */
.addstock-form { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; }
.addstock-form .af-group label { display: block; font-size: 11px; font-weight: 600; color: #6b7280; margin-bottom: 4px; text-transform: uppercase; letter-spacing: .3px; }
.addstock-form .af-group input { padding: 7px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; }
.addstock-form .af-group input:focus { outline: none; border-color: #8b5cf6; box-shadow: 0 0 0 2px rgba(139,92,246,.12); }
.addstock-form .btn-add { padding: 7px 16px; background: #8b5cf6; color: #fff; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
.addstock-form .btn-add:hover { background: #7c3aed; }

/* ── Alert ── */
.alert-stock { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
.alert-stock.success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }

/* ── Empty ── */
.empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
.empty-state p { font-size: 15px; }
</style>

{{-- Header --}}
<div class="stock-header">
    <div>
        <h2>Stock Management</h2>
        <p>Central warehouse stock overview. Use <strong>Clinic Inventory</strong> to view per-clinic stock.</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <a href="{{ route('organisation.inventory.quick-stock') }}" style="background:#8b5cf6;color:#fff;padding:9px 18px;border-radius:8px;font-weight:600;font-size:14px;text-decoration:none;display:inline-block;">Quick Stock Entry</a>
        <form method="GET" class="stock-search">
            <input type="text" name="q" placeholder="Search items..." value="{{ $search ?? '' }}">
            <button type="submit">Search</button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert-stock success">{{ session('success') }}</div>
@endif

{{-- Summary Cards --}}
@php
    $totalItems = $items->count();
    $centralStockItems = $items->where('central_qty', '>', 0)->count();
    $lowStockCount = $items->filter(fn($i) => $i->total_qty > 0 && $i->total_qty <= 5)->count();
    $outOfStockCount = $items->where('total_qty', '<=', 0)->count();
@endphp
<div class="summary-row">
    <div class="summary-card sc-total">
        <div class="sc-num" style="color:#2563eb;">{{ $totalItems }}</div>
        <div class="sc-label">Total Items</div>
    </div>
    <div class="summary-card sc-central">
        <div class="sc-num" style="color:#8b5cf6;">{{ $centralStockItems }}</div>
        <div class="sc-label">With Central Stock</div>
    </div>
    <div class="summary-card sc-low">
        <div class="sc-num" style="color:#f59e0b;">{{ $lowStockCount }}</div>
        <div class="sc-label">Low Stock</div>
    </div>
    <div class="summary-card sc-out">
        <div class="sc-num" style="color:#ef4444;">{{ $outOfStockCount }}</div>
        <div class="sc-label">Out of Stock</div>
    </div>
</div>

{{-- Item Cards --}}
@forelse($items as $item)
    @php
        $total = $item->total_qty;
        $central = $item->central_qty;
        $totalClass = $total <= 0 ? 'total-danger' : ($total <= 5 ? 'total-warn' : 'total-good');
    @endphp
    <div class="stock-item">
        <div class="si-main">
            {{-- Info --}}
            <div class="si-info">
                <h4 class="si-name">{{ $item->name }}</h4>
                <div class="si-meta">
                    <span class="si-badge {{ $item->item_type }}">{{ ucfirst($item->item_type) }}</span>
                    @if($item->strength_value)
                        <span class="si-detail">{{ $item->strength_value }} {{ $item->strength_unit }}</span>
                    @endif
                    @if($item->package_type)
                        <span class="si-detail">· {{ ucfirst($item->package_type) }}</span>
                    @endif
                </div>
            </div>

            {{-- Stock numbers --}}
            <div class="si-stocks">
                <div class="si-stock-box central">
                    <div class="sb-num">{{ $central > 0 ? number_format($central, 0) : '0' }}</div>
                    <div class="sb-label">Central</div>
                </div>
                <div class="si-stock-box {{ $totalClass }}">
                    <div class="sb-num">{{ number_format($total, 0) }}</div>
                    <div class="sb-label">Total</div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="si-actions">
            <button class="btn-act" onclick="togglePanel('batches-{{ $item->id }}', this)" data-label-show="Batches ({{ $item->allBatches->count() }})" data-label-hide="Batches ({{ $item->allBatches->count() }})">
                Batches ({{ $item->allBatches->count() }})
            </button>
            <button class="btn-act primary" onclick="togglePanel('addstock-{{ $item->id }}', this)" data-label-show="+ Add Central Stock" data-label-hide="Close">
                + Add Central Stock
            </button>
        </div>

        {{-- Panel: Batches --}}
        <div class="si-panel" id="batches-{{ $item->id }}">
            <h6>Batch Details</h6>
            @if($item->allBatches->count())
                <table class="batch-tbl">
                    <thead>
                        <tr>
                            <th>Batch #</th>
                            <th>Location</th>
                            <th style="text-align:right;">Quantity</th>
                            <th>Expiry Date</th>
                            <th>Purchase Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->allBatches as $batch)
                            <tr>
                                <td style="font-weight:600;">{{ $batch->batch_number ?: '—' }}</td>
                                <td>
                                    @if($batch->clinic_id)
                                        <span class="loc-badge loc-clinic">{{ $clinics->firstWhere('id', $batch->clinic_id)?->name ?? 'Clinic #'.$batch->clinic_id }}</span>
                                    @else
                                        <span class="loc-badge loc-central">Central</span>
                                    @endif
                                </td>
                                <td style="text-align:right; font-weight:700;">{{ number_format($batch->quantity, 1) }}</td>
                                <td>
                                    @if($batch->expiry_date)
                                        @php $exp = \Carbon\Carbon::parse($batch->expiry_date); @endphp
                                        <span class="{{ $exp->isPast() ? 'exp-warn' : '' }}">
                                            {{ $exp->format('d M Y') }}
                                            @if($exp->isPast()) (Expired) @elseif($exp->diffInDays(now()) < 90) <small style="color:#d97706;">({{ $exp->diffInDays(now()) }}d left)</small> @endif
                                        </span>
                                    @else
                                        <span style="color:#9ca3af;">No expiry</span>
                                    @endif
                                </td>
                                <td>
                                    @if($batch->purchase_price)
                                        ₹{{ number_format($batch->purchase_price, 2) }}
                                    @else
                                        <span style="color:#9ca3af;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color:#9ca3af; margin:0;">No active batches.</p>
            @endif
        </div>

        {{-- Panel: Add Central Stock --}}
        <div class="si-panel" id="addstock-{{ $item->id }}">
            <h6>Add Central Stock</h6>
            <form method="POST" action="{{ route('organisation.inventory.batch.store') }}" class="addstock-form">
                @csrf
                <input type="hidden" name="inventory_item_id" value="{{ $item->id }}">
                <div class="af-group">
                    <label>Batch #</label>
                    <input type="text" name="batch_number" placeholder="e.g. LOT-001" required style="width:130px;">
                </div>
                <div class="af-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" style="width:150px;">
                </div>
                <div class="af-group">
                    <label>Quantity</label>
                    <input type="number" step="0.001" name="quantity" placeholder="0" required style="width:90px;">
                </div>
                <div class="af-group">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" name="purchase_price" placeholder="0.00" style="width:100px;">
                </div>
                <button type="submit" class="btn-add">Add Stock</button>
            </form>
        </div>
    </div>
@empty
    <div class="stock-item">
        <div class="empty-state">
            <p>No inventory items found.</p>
        </div>
    </div>
@endforelse

<script>
function togglePanel(id, btn) {
    const panel = document.getElementById(id);
    const isOpen = panel.classList.contains('show');
    const card = panel.closest('.stock-item');
    card.querySelectorAll('.si-panel.show').forEach(p => p.classList.remove('show'));
    card.querySelectorAll('.si-actions .btn-act').forEach(b => b.textContent = b.dataset.labelShow);
    if (!isOpen) {
        panel.classList.add('show');
        btn.textContent = btn.dataset.labelHide;
    }
}
</script>

@endsection
