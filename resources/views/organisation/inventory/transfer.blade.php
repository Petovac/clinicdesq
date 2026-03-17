@extends('organisation.layout')

@section('content')
<style>
.tf-wrap { max-width: 720px; }
.tf-header h2 { font-size: 22px; font-weight: 700; margin: 0 0 4px; }
.tf-header p { color: #6b7280; font-size: 14px; margin: 0 0 20px; }
.tf-card { background: #fff; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,.06); margin-bottom: 20px; }
.tf-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
.tf-input, .tf-select { width: 100%; padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 14px; box-sizing: border-box; }
.tf-input:focus, .tf-select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,.15); }
.tf-row { display: flex; gap: 16px; margin-bottom: 16px; }
.tf-row > div { flex: 1; }
.btn { padding: 9px 22px; border-radius: 7px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; }
.btn-blue { background: #2563eb; color: #fff; }
.btn-blue:hover { background: #1d4ed8; }
.alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
.alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }

/* Searchable item picker */
.item-picker { position: relative; }
.item-search { width: 100%; padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 14px; box-sizing: border-box; }
.item-search:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,.15); }
.item-dd { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #d1d5db; border-radius: 7px; max-height: 220px; overflow-y: auto; z-index: 50; display: none; box-shadow: 0 4px 12px rgba(0,0,0,.1); }
.item-dd .dd-opt { padding: 8px 12px; font-size: 13px; cursor: pointer; }
.item-dd .dd-opt:hover { background: #eff6ff; }
.item-dd .dd-empty { padding: 8px 12px; font-size: 13px; color: #9ca3af; }

/* Batch info */
.batch-info { margin-top: 12px; padding: 12px 16px; background: #f9fafb; border-radius: 8px; font-size: 13px; }
.batch-info .bi-row { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #e5e7eb; }
.batch-info .bi-row:last-child { border-bottom: none; }
.batch-info .bi-label { color: #6b7280; }
.batch-info .bi-val { font-weight: 600; }
</style>

<div class="tf-wrap">
    <div class="tf-header">
        <h2>Transfer Stock to Clinic</h2>
        <p>Transfer items between central stock and clinics.</p>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-danger">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('organisation.inventory.transfer.store') }}">
        @csrf

        <div class="tf-card">
            {{-- Clinic --}}
            <div style="margin-bottom:16px;">
                <label class="tf-label">Target Clinic</label>
                <select name="clinic_id" class="tf-select" required>
                    <option value="">— Select Clinic —</option>
                    @foreach($clinics as $c)
                        <option value="{{ $c->id }}" {{ old('clinic_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} — {{ $c->city }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Item --}}
            <div style="margin-bottom:16px;">
                <label class="tf-label">Inventory Item</label>
                <div class="item-picker">
                    <input type="hidden" name="inventory_item_id" id="itemHidden" value="{{ old('inventory_item_id') }}">
                    <input type="text" class="item-search" id="itemSearch" placeholder="Type to search items…" autocomplete="off">
                    <div class="item-dd" id="itemDd"></div>
                </div>
            </div>

            {{-- Batch selector (loaded via AJAX) --}}
            <div style="margin-bottom:16px;" id="batchWrap" style="display:none;">
                <label class="tf-label">Source Batch</label>
                <select name="batch_id" id="batchSelect" class="tf-select" required>
                    <option value="">— Select item first —</option>
                </select>
                <div class="batch-info" id="batchInfo" style="display:none;"></div>
            </div>

            {{-- Quantity --}}
            <div>
                <label class="tf-label">Transfer Quantity</label>
                <input type="number" name="quantity" class="tf-input" min="0.001" step="any" value="{{ old('quantity') }}" required placeholder="Enter qty to transfer">
            </div>
        </div>

        <button type="submit" class="btn btn-blue">Transfer Stock</button>
    </form>
</div>

@php
    $itemsJson = $items->map(fn($i) => [
        'id' => $i->id,
        'name' => $i->name,
        'type' => $i->item_type,
        'strength' => trim(($i->strength_value ?? '') . ' ' . ($i->strength_unit ?? '')),
        'totalQty' => $i->allBatches->sum('quantity'),
    ]);
@endphp

<script>
const items = @json($itemsJson);

// Searchable item picker
const searchEl = document.getElementById('itemSearch');
const ddEl = document.getElementById('itemDd');
const hiddenEl = document.getElementById('itemHidden');

searchEl.addEventListener('input', filterItems);
searchEl.addEventListener('focus', filterItems);

function filterItems() {
    const val = searchEl.value.toLowerCase();
    const filtered = items.filter(i => {
        const label = i.name + ' ' + (i.strength || '');
        return label.toLowerCase().includes(val);
    }).slice(0, 30);

    if (filtered.length === 0) {
        ddEl.innerHTML = '<div class="dd-empty">No items found</div>';
    } else {
        ddEl.innerHTML = filtered.map(i => {
            const strength = i.strength ? ` <span style="color:#6b7280;">(${i.strength})</span>` : '';
            const qty = `<span style="color:#2563eb;font-size:11px;margin-left:6px;">${i.totalQty} in stock</span>`;
            return `<div class="dd-opt" data-id="${i.id}" data-name="${i.name}" data-strength="${i.strength || ''}">${i.name}${strength} ${qty}</div>`;
        }).join('');
    }
    ddEl.style.display = 'block';
}

ddEl.addEventListener('click', function(e) {
    const opt = e.target.closest('.dd-opt');
    if (!opt) return;
    hiddenEl.value = opt.dataset.id;
    searchEl.value = opt.dataset.name + (opt.dataset.strength ? ' (' + opt.dataset.strength + ')' : '');
    ddEl.style.display = 'none';
    loadBatches(opt.dataset.id);
});

document.addEventListener('click', function(e) {
    if (!ddEl.contains(e.target) && e.target !== searchEl) {
        ddEl.style.display = 'none';
    }
});

// Load central batches via AJAX
function loadBatches(itemId) {
    const batchSelect = document.getElementById('batchSelect');
    const batchInfo = document.getElementById('batchInfo');
    batchSelect.innerHTML = '<option value="">Loading…</option>';
    batchInfo.style.display = 'none';

    fetch(`/organisation/inventory-transfer/${itemId}/batches`)
        .then(r => r.json())
        .then(batches => {
            if (batches.length === 0) {
                batchSelect.innerHTML = '<option value="">No stock available</option>';
                return;
            }
            batchSelect.innerHTML = '<option value="">— Select Batch —</option>' +
                batches.map(b => {
                    const expiry = b.expiry_date || 'No expiry';
                    const loc = b.location || 'Central';
                    return `<option value="${b.id}" data-qty="${b.quantity}" data-expiry="${expiry}" data-batch="${b.batch_number || 'N/A'}" data-location="${loc}">
                        [${loc}] ${b.batch_number || 'No batch#'} — Qty: ${b.quantity} — Exp: ${expiry}
                    </option>`;
                }).join('');
        });
}

// Show batch details on selection
document.getElementById('batchSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const batchInfo = document.getElementById('batchInfo');
    if (!this.value) { batchInfo.style.display = 'none'; return; }
    batchInfo.innerHTML = `
        <div class="bi-row"><span class="bi-label">Location</span><span class="bi-val">${opt.dataset.location}</span></div>
        <div class="bi-row"><span class="bi-label">Batch #</span><span class="bi-val">${opt.dataset.batch}</span></div>
        <div class="bi-row"><span class="bi-label">Available Qty</span><span class="bi-val">${opt.dataset.qty}</span></div>
        <div class="bi-row"><span class="bi-label">Expiry</span><span class="bi-val">${opt.dataset.expiry}</span></div>
    `;
    batchInfo.style.display = 'block';
});

// Pre-select if old values exist
if (hiddenEl.value) {
    const item = items.find(i => i.id == hiddenEl.value);
    if (item) {
        searchEl.value = item.name + (item.strength ? ' (' + item.strength + ')' : '');
        loadBatches(hiddenEl.value);
    }
}
</script>
@endsection
