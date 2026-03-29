@extends('organisation.layout')

@section('content')
<style>
.bt-page { max-width: 1100px; margin: auto; padding: 10px 5px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
.bt-header h2 { font-size: 22px; font-weight: 700; margin: 0; }
.bt-header p { color: #6b7280; font-size: 13px; margin: 2px 0 0; }
.bt-back { color: #6b7280; text-decoration: none; font-size: 13px; }
.bt-back:hover { color: #2563eb; }

.bt-alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
.bt-alert.success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.bt-alert.danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

.bt-card { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 4px rgba(0,0,0,.04); overflow: hidden; }

.bt-clinic-bar { padding: 16px 20px; background: #f0fdf4; border-bottom: 1px solid #bbf7d0; display: flex; gap: 14px; align-items: center; flex-wrap: wrap; }
.bt-clinic-bar label { font-size: 13px; font-weight: 700; color: #166534; }
.bt-clinic-bar select { padding: 8px 12px; border: 1px solid #86efac; border-radius: 6px; font-size: 13px; background: #fff; min-width: 280px; }
.bt-clinic-bar select:focus { outline: none; border-color: #16a34a; box-shadow: 0 0 0 2px rgba(22,163,74,.15); }

.bt-toolbar { display: flex; gap: 10px; align-items: center; padding: 12px 20px; background: #f8fafc; border-bottom: 1px solid #e5e7eb; flex-wrap: wrap; }
.bt-toolbar input { padding: 7px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; width: 240px; }
.bt-toolbar input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,.12); }
.bt-toolbar .bt-count { font-size: 13px; color: #6b7280; margin-left: auto; }

.bt-table { width: 100%; border-collapse: collapse; }
.bt-table th { background: #f1f5f9; padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .4px; border-bottom: 2px solid #e2e8f0; position: sticky; top: 0; }
.bt-table td { padding: 8px 14px; border-bottom: 1px solid #f1f5f9; font-size: 13px; vertical-align: middle; }
.bt-table tr:hover td { background: #fefce8; }
.bt-table tr.has-qty td { background: #f0fdf4; }
.bt-table tr.no-stock td { opacity: 0.45; }
.bt-table tr.hidden-row { display: none; }

.bt-item-name { font-weight: 600; color: #1e293b; font-size: 13px; }
.bt-item-meta { font-size: 11px; color: #9ca3af; margin-top: 1px; }
.bt-badge { display: inline-block; padding: 1px 7px; border-radius: 10px; font-size: 10px; font-weight: 600; text-transform: uppercase; }
.bt-badge.drug { background: #eff6ff; color: #2563eb; }
.bt-badge.consumable { background: #fef3c7; color: #b45309; }
.bt-badge.product { background: #ecfdf5; color: #059669; }
.bt-badge.surgical { background: #fce7f3; color: #be185d; }
.bt-badge.vaccine { background: #ede9fe; color: #6d28d9; }

.bt-stock { font-weight: 700; font-size: 13px; }
.bt-stock.zero { color: #ef4444; }
.bt-stock.ok { color: #16a34a; }

.bt-input { padding: 6px 8px; border: 1px solid #e5e7eb; border-radius: 5px; font-size: 13px; width: 80px; background: #fff; }
.bt-input:focus { outline: none; border-color: #16a34a; box-shadow: 0 0 0 2px rgba(22,163,74,.12); }
.bt-input.filled { border-color: #86efac; background: #f0fdf4; }

.bt-footer { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: #f8fafc; border-top: 1px solid #e5e7eb; position: sticky; bottom: 0; }
.bt-footer .summary { font-size: 14px; color: #374151; }
.bt-footer .summary strong { color: #16a34a; }
.bt-btn-submit { padding: 10px 28px; background: #16a34a; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; }
.bt-btn-submit:hover { background: #15803d; }
.bt-btn-submit:disabled { background: #d1d5db; cursor: not-allowed; }
</style>

<div class="bt-page">
    <div class="bt-header" style="margin-bottom:20px;">
        <a href="{{ route('organisation.inventory.stock') }}" class="bt-back">&larr; Back to Stock Management</a>
        <h2>Bulk Transfer to Clinic</h2>
        <p>Enter quantity for each item you want to transfer. Auto-picks nearest expiry batch.</p>
    </div>

    @if(session('success'))
        <div class="bt-alert success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bt-alert danger">
            @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('organisation.inventory.transfer.store') }}" id="bulkTransferForm">
        @csrf
        <input type="hidden" name="bulk_transfer" value="1">

        <div class="bt-card">
            <div class="bt-clinic-bar">
                <label>Transfer To:</label>
                <select name="clinic_id" id="clinicSelect" required>
                    @foreach($clinics as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->city }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bt-toolbar">
                <input type="text" id="btSearch" placeholder="Filter items..." oninput="btFilter()">
                <div class="bt-count"><span id="btFilled">0</span> items to transfer</div>
            </div>

            <div style="max-height: 60vh; overflow-y: auto;">
                <table class="bt-table">
                    <thead>
                        <tr>
                            <th style="width:35%;">Item</th>
                            <th style="width:12%;">Central Stock</th>
                            <th style="width:20%;">Batch (auto)</th>
                            <th style="width:13%;">Expiry</th>
                            <th style="width:12%;">Transfer Qty</th>
                        </tr>
                    </thead>
                    <tbody id="btBody">
                        @foreach($items as $item)
                        @php
                            $centralBatches = $item->allBatches->whereNull('clinic_id')->sortBy('expiry_date');
                            $centralQty = $centralBatches->sum('quantity');
                            $nearestBatch = $centralBatches->first();
                        @endphp
                        <tr data-name="{{ strtolower($item->name) }}"
                            data-central="{{ $centralQty }}"
                            data-batch-id="{{ $nearestBatch->id ?? '' }}"
                            data-batch-num="{{ $nearestBatch->batch_number ?? '' }}"
                            data-batch-exp="{{ $nearestBatch ? \Carbon\Carbon::parse($nearestBatch->expiry_date)->format('d/m/Y') : '' }}"
                            data-max="{{ $nearestBatch->quantity ?? 0 }}"
                            class="bt-row {{ $centralQty <= 0 ? 'no-stock' : '' }}">
                            <td>
                                <div class="bt-item-name">{{ $item->name }}</div>
                                <div class="bt-item-meta">
                                    <span class="bt-badge {{ $item->item_type }}">{{ ucfirst($item->item_type) }}</span>
                                    @if($item->strength_value) {{ $item->strength_value }} {{ $item->strength_unit }} @endif
                                    @if($item->package_type) &middot; {{ ucfirst(str_replace('_',' ',$item->package_type)) }} @endif
                                </div>
                            </td>
                            <td>
                                <span class="bt-stock {{ $centralQty <= 0 ? 'zero' : 'ok' }}">{{ number_format($centralQty, 0) }}</span>
                            </td>
                            <td style="font-size:12px;color:#6b7280;">
                                {{ $nearestBatch->batch_number ?? '—' }}
                            </td>
                            <td style="font-size:12px;color:#6b7280;">
                                @if($nearestBatch && $nearestBatch->expiry_date)
                                    {{ \Carbon\Carbon::parse($nearestBatch->expiry_date)->format('d M Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($centralQty > 0)
                                    <input type="number" step="1" min="0" max="{{ $nearestBatch->quantity ?? 0 }}"
                                           class="bt-input bt-qty" data-item="{{ $item->id }}"
                                           placeholder="0" oninput="btQtyChange(this)">
                                @else
                                    <span style="font-size:11px;color:#d1d5db;">No stock</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bt-footer">
                <div class="summary"><strong id="btSubmitCount">0</strong> items will be transferred</div>
                <button type="submit" class="bt-btn-submit" id="btSubmitBtn" disabled>Transfer All</button>
            </div>
        </div>
    </form>
</div>

<script>
function btQtyChange(input) {
    const row = input.closest('tr');
    const val = parseFloat(input.value) || 0;
    const max = parseFloat(row.dataset.max) || 0;

    if (val > max) {
        input.value = max;
    }

    if (val > 0) {
        row.classList.add('has-qty');
        input.classList.add('filled');
    } else {
        row.classList.remove('has-qty');
        input.classList.remove('filled');
    }

    const filled = document.querySelectorAll('tr.has-qty').length;
    document.getElementById('btFilled').textContent = filled;
    document.getElementById('btSubmitCount').textContent = filled;
    document.getElementById('btSubmitBtn').disabled = filled === 0;
}

function btFilter() {
    const search = document.getElementById('btSearch').value.toLowerCase();
    document.querySelectorAll('.bt-row').forEach(row => {
        row.classList.toggle('hidden-row', !row.dataset.name.includes(search));
    });
}

// On submit, collect filled rows into hidden inputs
document.getElementById('bulkTransferForm').addEventListener('submit', function(e) {
    const entries = [];
    document.querySelectorAll('tr.has-qty').forEach(row => {
        const qty = row.querySelector('.bt-qty').value;
        const batchId = row.dataset.batchId;
        const itemId = row.querySelector('.bt-qty').dataset.item;
        if (qty > 0 && batchId) {
            entries.push({ inventory_item_id: itemId, batch_id: batchId, quantity: qty });
        }
    });

    if (entries.length === 0) {
        e.preventDefault();
        alert('Enter quantity for at least one item.');
        return;
    }

    // Append hidden inputs
    const form = this;
    entries.forEach((entry, idx) => {
        Object.keys(entry).forEach(key => {
            const h = document.createElement('input');
            h.type = 'hidden';
            h.name = `transfers[${idx}][${key}]`;
            h.value = entry[key];
            form.appendChild(h);
        });
    });
});
</script>
@endsection
