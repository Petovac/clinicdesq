@extends('clinic.layout')

@section('content')
<style>
.oc-wrap { max-width: 800px; }
.oc-header { margin-bottom: 24px; }
.oc-header h2 { font-size: 22px; font-weight: 700; margin: 0 0 4px; }
.oc-header p { color: #6b7280; font-size: 14px; margin: 0; }
.oc-card { background: #fff; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,.06); margin-bottom: 20px; }
.oc-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
.oc-input, .oc-select, .oc-textarea { width: 100%; padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 14px; box-sizing: border-box; }
.oc-input:focus, .oc-select:focus, .oc-textarea:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,.15); }
.oc-row { display: flex; gap: 16px; margin-bottom: 16px; }
.oc-row > div { flex: 1; }

/* Items section */
.oc-items-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.oc-items-header h3 { font-size: 16px; font-weight: 600; margin: 0; }
.oc-item-row { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 10px; }
.oc-item-row .item-picker { flex: 3; position: relative; }
.oc-item-row .item-qty { flex: 1; }
.oc-item-row .item-remove { flex: 0 0 36px; padding-top: 2px; }
.btn { padding: 8px 18px; border-radius: 7px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
.btn-blue { background: #2563eb; color: #fff; }
.btn-blue:hover { background: #1d4ed8; }
.btn-outline { background: #fff; color: #374151; border: 1px solid #d1d5db; }
.btn-outline:hover { background: #f9fafb; }
.btn-red-sm { background: none; border: none; color: #ef4444; font-size: 18px; cursor: pointer; padding: 4px 8px; }
.btn-red-sm:hover { color: #dc2626; }
.btn-back { color: #6b7280; text-decoration: none; font-size: 14px; }
.btn-back:hover { color: #374151; }

/* Searchable dropdown */
.item-search-input { width: 100%; padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 14px; box-sizing: border-box; }
.item-search-input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,.15); }
.item-dropdown { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #d1d5db; border-radius: 7px; max-height: 200px; overflow-y: auto; z-index: 50; display: none; box-shadow: 0 4px 12px rgba(0,0,0,.1); }
.item-dropdown .dd-option { padding: 8px 12px; font-size: 13px; cursor: pointer; }
.item-dropdown .dd-option:hover { background: #eff6ff; }
.item-dropdown .dd-empty { padding: 8px 12px; font-size: 13px; color: #9ca3af; }

.alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
</style>

<div class="oc-wrap">
    <div class="oc-header">
        <a href="{{ route('clinic.orders.index') }}" class="btn-back">← Back to Orders</a>
        <h2 style="margin-top:10px;">New Order Request</h2>
        <p>Create an order to request items from a vendor or your organisation.</p>
    </div>

    @if($errors->any())
        <div class="alert-danger">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('clinic.orders.store') }}" id="orderForm">
        @csrf

        <div class="oc-card">
            <div class="oc-row">
                <div>
                    <label class="oc-label">Order Type</label>
                    <select name="order_type" id="orderType" class="oc-select" required>
                        <option value="vendor" {{ old('order_type') === 'organisation' ? '' : 'selected' }}>Vendor (External)</option>
                        <option value="organisation" {{ old('order_type') === 'organisation' ? 'selected' : '' }}>Organisation (Internal)</option>
                    </select>
                </div>
                <div id="vendorNameWrap">
                    <label class="oc-label">Vendor Name</label>
                    <input type="text" name="vendor_name" class="oc-input" value="{{ old('vendor_name') }}" placeholder="e.g. Medline Supplies">
                </div>
            </div>

            <div>
                <label class="oc-label">Notes (optional)</label>
                <textarea name="notes" class="oc-textarea" rows="2" placeholder="Any special instructions…">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="oc-card">
            <div class="oc-items-header">
                <h3>Items</h3>
                <button type="button" class="btn btn-outline" onclick="addItemRow()">+ Add Item</button>
            </div>

            <div id="itemRows">
                {{-- One default row --}}
            </div>
        </div>

        <div style="display:flex;gap:12px;">
            <button type="submit" class="btn btn-blue">Create Order</button>
            <a href="{{ route('clinic.orders.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

@php
    $itemsJson = $items->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'type' => $i->item_type, 'strength' => $i->strength]);
@endphp

<script>
const inventoryItems = @json($itemsJson);
let rowIndex = 0;

function addItemRow(preselectedId = '', preselectedQty = '') {
    const idx = rowIndex++;
    const container = document.getElementById('itemRows');
    const row = document.createElement('div');
    row.className = 'oc-item-row';
    row.id = 'itemRow-' + idx;
    row.innerHTML = `
        <div class="item-picker">
            <input type="hidden" name="items[${idx}][inventory_item_id]" id="itemHidden-${idx}" value="${preselectedId}">
            <input type="text" class="item-search-input" id="itemSearch-${idx}" placeholder="Search item…"
                   autocomplete="off" oninput="filterItems(${idx})" onfocus="filterItems(${idx})">
            <div class="item-dropdown" id="itemDd-${idx}"></div>
        </div>
        <div class="item-qty">
            <input type="number" name="items[${idx}][quantity]" class="oc-input" placeholder="Qty" min="0.001" step="any" required value="${preselectedQty}">
        </div>
        <div class="item-remove">
            <button type="button" class="btn-red-sm" onclick="removeItemRow(${idx})">✕</button>
        </div>
    `;
    container.appendChild(row);

    // Close dropdown on outside click
    document.addEventListener('click', function handler(e) {
        const dd = document.getElementById('itemDd-' + idx);
        const search = document.getElementById('itemSearch-' + idx);
        if (dd && !dd.contains(e.target) && e.target !== search) {
            dd.style.display = 'none';
        }
    });

    if (preselectedId) {
        const item = inventoryItems.find(i => i.id == preselectedId);
        if (item) document.getElementById('itemSearch-' + idx).value = item.name + (item.strength ? ' (' + item.strength + ')' : '');
    }
}

function removeItemRow(idx) {
    const row = document.getElementById('itemRow-' + idx);
    if (row) row.remove();
}

function filterItems(idx) {
    const search = document.getElementById('itemSearch-' + idx);
    const dd = document.getElementById('itemDd-' + idx);
    const val = search.value.toLowerCase();

    const filtered = inventoryItems.filter(i => {
        const label = i.name + (i.strength ? ' ' + i.strength : '');
        return label.toLowerCase().includes(val);
    }).slice(0, 30);

    if (filtered.length === 0) {
        dd.innerHTML = '<div class="dd-empty">No items found</div>';
    } else {
        dd.innerHTML = filtered.map(i => {
            const label = i.name + (i.strength ? ' <span style="color:#6b7280;">(' + i.strength + ')</span>' : '');
            const typeBadge = i.type === 'drug'
                ? '<span style="background:#dbeafe;color:#1e40af;padding:1px 6px;border-radius:4px;font-size:11px;margin-left:6px;">Drug</span>'
                : '<span style="background:#fef3c7;color:#92400e;padding:1px 6px;border-radius:4px;font-size:11px;margin-left:6px;">Consumable</span>';
            return `<div class="dd-option" onclick="selectItem(${idx}, ${i.id}, '${i.name.replace(/'/g, "\\'")}', '${(i.strength || '').replace(/'/g, "\\'")}')">${label}${typeBadge}</div>`;
        }).join('');
    }
    dd.style.display = 'block';
}

function selectItem(idx, id, name, strength) {
    document.getElementById('itemHidden-' + idx).value = id;
    document.getElementById('itemSearch-' + idx).value = name + (strength ? ' (' + strength + ')' : '');
    document.getElementById('itemDd-' + idx).style.display = 'none';
}

// Toggle vendor name field
document.getElementById('orderType').addEventListener('change', function () {
    document.getElementById('vendorNameWrap').style.display = this.value === 'vendor' ? '' : 'none';
});
if (document.getElementById('orderType').value === 'organisation') {
    document.getElementById('vendorNameWrap').style.display = 'none';
}

// Start with one row
addItemRow();
</script>
@endsection
