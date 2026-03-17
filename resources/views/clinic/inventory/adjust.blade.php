@extends('clinic.layout')

@section('content')
<style>
.adj-wrap { max-width: 600px; }
.adj-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:28px; }
.adj-card h2 { margin:0 0 20px; font-size:20px; font-weight:700; }
.form-group { margin-bottom:16px; }
.form-group label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-group input, .form-group select, .form-group textarea { width:100%; padding:9px 12px; border:1px solid #e5e7eb; border-radius:7px; font-size:14px; box-sizing:border-box; }
.form-group textarea { resize:vertical; min-height:60px; }
.btn { padding:10px 24px; border-radius:8px; font-size:14px; font-weight:600; border:none; cursor:pointer; }
.btn-blue { background:#2563eb; color:#fff; }
.btn-blue:hover { background:#1d4ed8; }
.alert-error { background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;color:#991b1b;margin-bottom:16px;font-size:14px; }
.back-link { color:#2563eb; text-decoration:none; font-size:14px; display:inline-block; margin-bottom:16px; }
/* Searchable dropdown */
.search-wrap { position:relative; }
.item-dropdown { display:none; position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #e5e7eb; border-radius:8px; max-height:220px; overflow-y:auto; z-index:50; box-shadow:0 8px 24px rgba(0,0,0,.12); margin-top:2px; }
.item-dropdown div { padding:8px 14px; cursor:pointer; font-size:14px; border-bottom:1px solid #f3f4f6; }
.item-dropdown div:hover { background:#f0f7ff; }
</style>

<div class="adj-wrap">
    <a href="{{ route('clinic.inventory.index') }}" class="back-link">← Back to Inventory</a>

    <div class="adj-card">
        <h2>Manual Stock Adjustment</h2>

        @if($errors->any())
            <div class="alert-error">
                @foreach($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('clinic.inventory.adjust') }}">
            @csrf

            <div class="form-group search-wrap">
                <label>Item</label>
                <input type="text" id="adj-item-search" placeholder="Type to search items..." autocomplete="off">
                <input type="hidden" name="inventory_item_id" id="adj-item-id">
                <div id="adj-item-dropdown" class="item-dropdown"></div>
            </div>

            <div class="form-group">
                <label>Batch</label>
                <select name="batch_id" id="adj-batch-select" required>
                    <option value="">— select item first —</option>
                </select>
            </div>

            <div class="form-group">
                <label>Adjustment Quantity (negative to reduce)</label>
                <input type="number" name="adjustment" step="0.001" required placeholder="e.g. -5 or +10">
            </div>

            <div class="form-group">
                <label>Reason</label>
                <select name="reason" required>
                    <option value="">Select reason…</option>
                    <option value="wastage">Wastage</option>
                    <option value="damage">Damage</option>
                    <option value="expired">Expired</option>
                    <option value="correction">Stock Correction</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label>Notes (optional)</label>
                <textarea name="notes" placeholder="Additional details..."></textarea>
            </div>

            <button type="submit" class="btn btn-blue">Submit Adjustment</button>
        </form>
    </div>
</div>

@php
    $itemsJson = $items->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'type' => $i->item_type])->values();
@endphp
<script>
const adjItems = @json($itemsJson);
const adjSearch = document.getElementById('adj-item-search');
const adjDropdown = document.getElementById('adj-item-dropdown');
const adjHiddenId = document.getElementById('adj-item-id');
const batchSelect = document.getElementById('adj-batch-select');

adjSearch.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    if (q.length < 1) { adjDropdown.style.display = 'none'; return; }
    const matches = adjItems.filter(i => i.name.toLowerCase().includes(q)).slice(0, 12);
    adjDropdown.innerHTML = matches.length === 0
        ? '<div style="color:#9ca3af;cursor:default;">No items found</div>'
        : matches.map(i => '<div data-id="'+i.id+'" data-name="'+i.name+'">'+i.name+' <span style="color:#9ca3af;font-size:12px;">('+i.type+')</span></div>').join('');
    adjDropdown.style.display = 'block';
});

adjDropdown.addEventListener('click', function(e) {
    const opt = e.target.closest('div[data-id]');
    if (!opt) return;
    adjHiddenId.value = opt.dataset.id;
    adjSearch.value = opt.dataset.name;
    adjDropdown.style.display = 'none';
    loadBatches(opt.dataset.id);
});

document.addEventListener('click', function(e) {
    if (!adjSearch.contains(e.target) && !adjDropdown.contains(e.target)) adjDropdown.style.display = 'none';
});

function loadBatches(itemId) {
    batchSelect.innerHTML = '<option value="">Loading...</option>';
    fetch('/clinic/inventory/' + itemId + '/batches')
        .then(r => r.json())
        .then(data => {
            if (data.length === 0) {
                batchSelect.innerHTML = '<option value="">No batches available</option>';
                return;
            }
            batchSelect.innerHTML = '<option value="">Select batch…</option>' +
                data.map(b => '<option value="'+b.id+'">' +
                    (b.batch_number || 'No batch#') +
                    ' — Qty: ' + parseFloat(b.quantity).toFixed(1) +
                    (b.expiry_date ? ' — Exp: ' + b.expiry_date : '') +
                '</option>').join('');
        });
}
</script>
@endsection
