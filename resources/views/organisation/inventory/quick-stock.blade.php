@extends('organisation.layout')

@section('content')
<style>
.qs-page { max-width: 1100px; margin: auto; padding: 10px 5px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
.qs-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.qs-header h2 { font-size: 22px; font-weight: 700; margin: 0; }
.qs-header p { color: #6b7280; font-size: 13px; margin: 2px 0 0; }
.qs-back { color: #6b7280; text-decoration: none; font-size: 13px; }
.qs-back:hover { color: #2563eb; }

.qs-alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
.qs-alert.success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }

.qs-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 4px rgba(0,0,0,.04); overflow: hidden; }

.qs-toolbar { display: flex; gap: 10px; align-items: center; padding: 16px 20px; background: #f8fafc; border-bottom: 1px solid #e5e7eb; flex-wrap: wrap; }
.qs-toolbar input { padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; width: 260px; }
.qs-toolbar input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,.12); }
.qs-toolbar .qs-count { font-size: 13px; color: #6b7280; margin-left: auto; }

.qs-table { width: 100%; border-collapse: collapse; }
.qs-table th { background: #f1f5f9; padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .4px; border-bottom: 2px solid #e2e8f0; position: sticky; top: 0; }
.qs-table td { padding: 8px 14px; border-bottom: 1px solid #f1f5f9; font-size: 13px; vertical-align: middle; }
.qs-table tr:hover td { background: #fefce8; }
.qs-table tr.has-entry td { background: #f0fdf4; }
.qs-table tr.hidden-row { display: none; }

.qs-item-name { font-weight: 600; color: #1e293b; font-size: 13px; }
.qs-item-meta { font-size: 11px; color: #9ca3af; margin-top: 2px; }
.qs-badge { display: inline-block; padding: 1px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; text-transform: uppercase; }
.qs-badge.drug { background: #eff6ff; color: #2563eb; }
.qs-badge.consumable { background: #fef3c7; color: #b45309; }
.qs-badge.product { background: #ecfdf5; color: #059669; }
.qs-badge.surgical { background: #fce7f3; color: #be185d; }
.qs-badge.vaccine { background: #ede9fe; color: #6d28d9; }

.qs-stock { font-weight: 700; font-size: 13px; }
.qs-stock.zero { color: #ef4444; }
.qs-stock.low { color: #f59e0b; }
.qs-stock.ok { color: #16a34a; }

.qs-input { padding: 6px 8px; border: 1px solid #e5e7eb; border-radius: 5px; font-size: 13px; background: #fff; transition: all .15s; }
.qs-input:focus { outline: none; border-color: #8b5cf6; box-shadow: 0 0 0 2px rgba(139,92,246,.12); }
.qs-input.filled { border-color: #86efac; background: #f0fdf4; }

.qs-footer { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: #f8fafc; border-top: 1px solid #e5e7eb; position: sticky; bottom: 0; }
.qs-footer .summary { font-size: 14px; color: #374151; }
.qs-footer .summary strong { color: #8b5cf6; }
.qs-btn-submit { padding: 10px 28px; background: #8b5cf6; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all .15s; }
.qs-btn-submit:hover { background: #7c3aed; }
.qs-btn-submit:disabled { background: #d1d5db; cursor: not-allowed; }

.qs-toggle-btns { display: flex; gap: 6px; }
.qs-toggle-btn { padding: 5px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer; background: #fff; color: #374151; }
.qs-toggle-btn.active { background: #8b5cf6; color: #fff; border-color: #8b5cf6; }
</style>

<div class="qs-page">
    <div class="qs-header">
        <div>
            <a href="{{ route('organisation.inventory.stock') }}" class="qs-back">&larr; Back to Stock Management</a>
            <h2>Quick Stock Entry</h2>
            <p>Fill in batch, quantity, and price for items you received. Only filled rows will be submitted.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="qs-alert success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('organisation.inventory.quick-stock.store') }}" id="quickStockForm">
        @csrf

        <div class="qs-card">
            <div class="qs-toolbar">
                <input type="text" id="qsSearch" placeholder="Filter items..." oninput="filterItems()">
                <div class="qs-toggle-btns">
                    <button type="button" class="qs-toggle-btn active" onclick="showFilter('all', this)">All</button>
                    <button type="button" class="qs-toggle-btn" onclick="showFilter('out', this)">Out of Stock</button>
                    <button type="button" class="qs-toggle-btn" onclick="showFilter('filled', this)">Filled Only</button>
                </div>
                <div class="qs-count"><span id="filledCount">0</span> / {{ $items->count() }} items filled</div>
            </div>

            <div style="max-height: 65vh; overflow-y: auto;">
                <table class="qs-table">
                    <thead>
                        <tr>
                            <th style="width:30%;">Item</th>
                            <th style="width:8%;">Stock</th>
                            <th style="width:16%;">Batch #</th>
                            <th style="width:14%;">Expiry</th>
                            <th style="width:12%;">Qty</th>
                            <th style="width:12%;">Price (Rs)</th>
                        </tr>
                    </thead>
                    <tbody id="qsBody">
                        @foreach($items as $item)
                        @php
                            $stockClass = $item->central_qty <= 0 ? 'zero' : ($item->central_qty <= 5 ? 'low' : 'ok');
                        @endphp
                        <tr data-name="{{ strtolower($item->name) }}" data-stock="{{ $item->central_qty }}" class="qs-row">
                            <td>
                                <div class="qs-item-name">{{ $item->name }}</div>
                                <div class="qs-item-meta">
                                    <span class="qs-badge {{ $item->item_type }}">{{ ucfirst($item->item_type) }}</span>
                                    @if($item->strength_value)
                                        {{ $item->strength_value }} {{ $item->strength_unit }}
                                    @endif
                                    @if($item->package_type)
                                        &middot; {{ ucfirst(str_replace('_', ' ', $item->package_type)) }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="qs-stock {{ $stockClass }}">{{ number_format($item->central_qty, 0) }}</span>
                            </td>
                            <td>
                                <input type="text" class="qs-input qs-entry" data-item="{{ $item->id }}" placeholder="LOT-001" style="width:100%;">
                            </td>
                            <td>
                                <input type="date" class="qs-input qs-entry" data-item="{{ $item->id }}" style="width:100%;">
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" class="qs-input qs-qty" data-item="{{ $item->id }}" placeholder="0" style="width:100%;" oninput="onQtyChange(this)">
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" class="qs-input qs-entry" data-item="{{ $item->id }}" placeholder="0.00" style="width:100%;">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="qs-footer">
                <div class="summary"><strong id="submitCount">0</strong> items will be submitted</div>
                <button type="submit" class="qs-btn-submit" id="submitBtn" disabled>Add Stock for All Filled Items</button>
            </div>
        </div>
    </form>
</div>

<script>
let currentFilter = 'all';

function onQtyChange(input) {
    const row = input.closest('tr');
    const val = parseFloat(input.value) || 0;

    if (val > 0) {
        row.classList.add('has-entry');
        input.classList.add('filled');
    } else {
        row.classList.remove('has-entry');
        input.classList.remove('filled');
    }

    updateCounts();
}

function updateCounts() {
    const filled = document.querySelectorAll('tr.has-entry').length;
    document.getElementById('filledCount').textContent = filled;
    document.getElementById('submitCount').textContent = filled;
    document.getElementById('submitBtn').disabled = filled === 0;
}

function filterItems() {
    const search = document.getElementById('qsSearch').value.toLowerCase();
    document.querySelectorAll('.qs-row').forEach(row => {
        const name = row.dataset.name;
        const stock = parseFloat(row.dataset.stock);
        const hasFill = row.classList.contains('has-entry');

        let visible = name.includes(search);

        if (currentFilter === 'out') visible = visible && stock <= 0;
        if (currentFilter === 'filled') visible = visible && hasFill;

        row.classList.toggle('hidden-row', !visible);
    });
}

function showFilter(filter, btn) {
    currentFilter = filter;
    document.querySelectorAll('.qs-toggle-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filterItems();
}

// Before submit, remove disabled entries so only filled rows are sent
document.getElementById('quickStockForm').addEventListener('submit', function(e) {
    const entries = [];
    document.querySelectorAll('tr.has-entry').forEach(row => {
        const itemId = row.querySelector('.qs-qty').dataset.item;
        entries.push({
            inventory_item_id: itemId,
            batch_number: row.querySelector('[name*="batch_number"]').value,
            expiry_date: row.querySelector('[name*="expiry_date"]').value,
            quantity: row.querySelector('[name*="quantity"]').value,
            purchase_price: row.querySelector('[name*="purchase_price"]').value,
        });
    });

    if (entries.length === 0) {
        e.preventDefault();
        return;
    }

    // Clear all named inputs first
    document.querySelectorAll('.qs-table input[name]').forEach(i => i.removeAttribute('name'));

    // Add hidden inputs for the entries array
    entries.forEach((entry, idx) => {
        Object.keys(entry).forEach(key => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = `entries[${idx}][${key}]`;
            hidden.value = entry[key];
            this.appendChild(hidden);
        });
    });
});
</script>

@endsection
