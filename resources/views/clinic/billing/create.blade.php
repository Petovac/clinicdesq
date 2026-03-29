@extends('clinic.layout')

@section('content')

<style>
:root {
    --primary: #2563eb;
    --success: #16a34a;
    --danger:  #dc2626;
    --warn:    #f59e0b;
    --border:  #e5e7eb;
    --bg:      #f4f6f9;
    --card:    #ffffff;
    --muted:   #6b7280;
}
body { background: var(--bg); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; color: #111827; }
.bill-wrap { max-width: 860px; margin: 32px auto; padding: 0 16px 60px; }
.bill-header { background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 20px 24px; margin-bottom: 20px; }
.bill-header h2 { margin: 0 0 6px; font-size: 22px; font-weight: 700; }
.bill-header p  { margin: 0; font-size: 14px; color: var(--muted); }
.section { background: var(--card); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 16px; }
.section-clip { overflow: hidden; }
.section-title { padding: 12px 20px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); background: #f9fafb; border-bottom: 1px solid var(--border); }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px 20px; font-size: 14px; text-align: left; }
th { font-weight: 600; font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: .04em; border-bottom: 1px solid var(--border); }
tr + tr td { border-top: 1px solid #f3f4f6; }
.badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
.badge-pending  { background: #fef3c7; color: #92400e; }
.badge-approved { background: #d1fae5; color: #065f46; }
.badge-rejected { background: #fee2e2; color: #991b1b; }
.btn { padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; border: none; transition: opacity .15s; }
.btn:hover { opacity: .85; }
.btn-success { background: #d1fae5; color: #065f46; }
.btn-danger  { background: #fee2e2; color: #991b1b; }
.btn-primary { background: var(--primary); color: #fff; padding: 10px 24px; font-size: 15px; font-weight: 600; }
.btn-grey    { background: #f3f4f6; color: #374151; border: 1px solid var(--border); }
.total-bar { background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 20px 24px; margin-top: 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.total-amount { font-size: 28px; font-weight: 800; color: #111827; }
.total-label  { font-size: 13px; color: var(--muted); margin-bottom: 2px; }
.qty-input { width: 70px; padding: 4px 8px; border: 1px solid var(--border); border-radius: 6px; font-size: 14px; text-align: center; }
.add-item-row { display: flex; gap: 10px; align-items: center; padding: 14px 20px; }
.add-item-row select { flex: 1; padding: 9px 12px; border: 1px solid var(--border); border-radius: 7px; font-size: 14px; }
.add-item-row input  { width: 80px; padding: 9px 10px; border: 1px solid var(--border); border-radius: 7px; font-size: 14px; text-align: center; }
.alert-success { background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:14px 20px;color:#065f46;margin-bottom:20px;font-size:14px;font-weight:600; }
.alert-error   { background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:14px 20px;color:#991b1b;margin-bottom:20px;font-size:14px; }
.stock-badge { display:inline-block;padding:1px 7px;border-radius:999px;font-size:11px;font-weight:600;margin-left:6px; }
.stock-badge-ok { background:#d1fae5;color:#065f46; }
.stock-badge-low { background:#fef3c7;color:#92400e; }
.stock-badge-out { background:#fee2e2;color:#991b1b; }
.price-input { width:80px;padding:4px 8px;border:1px solid var(--border);border-radius:6px;font-size:14px;text-align:right; }
</style>

<div class="bill-wrap">

    <div class="bill-header" style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div>
            <h2>
                Bill
                @if($bill->isConfirmed())
                    <span class="badge badge-approved" style="font-size:14px;margin-left:8px;">Confirmed</span>
                @else
                    <span class="badge badge-pending" style="font-size:14px;margin-left:8px;">Draft</span>
                @endif
            </h2>
            <p>
                <strong>{{ $appointment->pet->name }}</strong>
                &nbsp;·&nbsp;{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}
                &nbsp;·&nbsp;{{ optional($appointment->vet)->name ?? '—' }}
                &nbsp;·&nbsp;{{ optional($appointment->clinic)->name ?? '—' }}
            </p>
        </div>
        @if($appointment->prescription && $appointment->prescription->items->count())
        <button type="button" class="btn btn-grey" onclick="document.getElementById('rxModal').style.display='flex'" style="display:flex;align-items:center;gap:6px;white-space:nowrap;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            Prescription
        </button>
        @endif
    </div>

    {{-- Prescription Preview Modal --}}
    @if($appointment->prescription && $appointment->prescription->items->count())
    <div id="rxModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:999;align-items:center;justify-content:center;" onclick="if(event.target===this)this.style.display='none'">
        <div style="background:#fff;border-radius:14px;max-width:700px;width:95%;max-height:80vh;overflow-y:auto;padding:24px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="margin:0;font-size:18px;">Prescription — {{ $appointment->pet->name }}</h3>
                <button onclick="document.getElementById('rxModal').style.display='none'" style="background:none;border:none;font-size:22px;cursor:pointer;color:#6b7280;">&times;</button>
            </div>
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:2px solid #e5e7eb;">
                        <th style="text-align:left;padding:8px 10px;font-size:12px;color:#6b7280;">Medicine</th>
                        <th style="text-align:left;padding:8px 10px;font-size:12px;color:#6b7280;">Dosage</th>
                        <th style="text-align:left;padding:8px 10px;font-size:12px;color:#6b7280;">Frequency</th>
                        <th style="text-align:left;padding:8px 10px;font-size:12px;color:#6b7280;">Duration</th>
                        <th style="text-align:left;padding:8px 10px;font-size:12px;color:#6b7280;">Instructions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($appointment->prescription->items as $rxItem)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:10px;font-size:14px;font-weight:500;">{{ $rxItem->medicine }}</td>
                        <td style="padding:10px;font-size:14px;">{{ $rxItem->dosage ?: '—' }}</td>
                        <td style="padding:10px;font-size:14px;">{{ $rxItem->frequency ?: '—' }}</td>
                        <td style="padding:10px;font-size:14px;">{{ $rxItem->duration ?: '—' }}</td>
                        <td style="padding:10px;font-size:13px;color:#6b7280;">{{ $rxItem->instructions ?: '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if($appointment->prescription->notes)
            <div style="margin-top:14px;padding:10px 12px;background:#f9fafb;border-radius:8px;font-size:13px;color:#6b7280;">
                <strong>Notes:</strong> {{ $appointment->prescription->notes }}
            </div>
            @endif
        </div>
    </div>
    @endif

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">⚠ {{ session('error') }}</div>
    @endif

    {{-- VISIT FEE --}}
    @php $visitItems = $bill->items->where('source','visit_fee'); @endphp
    @if($visitItems->count())
    <div class="section section-clip">
        <div class="section-title">Consultation / Visit Fee</div>
        <table>
            <thead><tr><th>Item</th><th>Amount</th><th>Status</th>@if($bill->isDraft())<th></th>@endif</tr></thead>
            <tbody>
            @foreach($visitItems as $item)
            <tr>
                <td>{{ $item->description ?? optional($item->priceItem)->name ?? '—' }}</td>
                <td>₹{{ number_format($item->total, 2) }}</td>
                <td><span class="badge badge-{{ $item->status }}" id="badge-{{ $item->id }}">{{ ucfirst($item->status) }}</span></td>
                @if($bill->isDraft())
                <td><button class="btn btn-danger" onclick="rejectItem({{ $item->id }}, this)">Remove</button></td>
                @endif
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- INJECTIONS --}}
    @php $injections = $bill->items->where('source','injection'); @endphp
    @if($injections->count())
    <div class="section section-clip">
        <div class="section-title">Injectable Drugs (administered)</div>
        <table>
            <thead><tr><th>Drug</th><th>Stock</th><th>Volume</th><th>Drug Cost</th><th>Route Fee</th><th>Total</th></tr></thead>
            <tbody>
            @foreach($injections as $item)
            @php
                $drugCost = round($item->price * $item->quantity, 2);
                $routeFee = round($item->total - $drugCost, 2);
                $invId = optional($item->priceItem)->inventory_item_id;
                $stock = $invId ? ($stockMap[$invId] ?? 0) : null;
            @endphp
            <tr>
                <td>{{ $item->description ?? optional($item->priceItem)->name ?? '—' }}</td>
                <td>
                    @if($stock === null)
                        <span class="stock-badge stock-badge-out">No link</span>
                    @elseif($stock > 10)
                        <span class="stock-badge stock-badge-ok">{{ $stock }} ml</span>
                    @elseif($stock > 0)
                        <span class="stock-badge stock-badge-low">{{ $stock }} ml</span>
                    @else
                        <span class="stock-badge stock-badge-out">Out</span>
                    @endif
                </td>
                <td>{{ number_format($item->quantity, 1) }} ml</td>
                <td>₹{{ number_format($drugCost, 2) }}</td>
                <td>{{ $routeFee > 0 ? '₹' . number_format($routeFee, 2) : '—' }}</td>
                <td style="font-weight:600;">₹{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- PROCEDURES --}}
    @php $procedures = $bill->items->where('source','procedure'); @endphp
    @if($procedures->count())
    <div class="section section-clip">
        <div class="section-title">Procedures</div>
        <table>
            <thead><tr><th>Procedure</th><th>Amount</th></tr></thead>
            <tbody>
            @foreach($procedures as $item)
            <tr>
                <td>{{ $item->description ?? optional($item->priceItem)->name ?? '—' }}</td>
                <td>₹{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- PRESCRIPTION REVIEW --}}
    @php $rxItems = $bill->items->where('source','prescription'); @endphp
    @if($rxItems->count())
    <div class="section section-clip">
        <div class="section-title">
            Prescription Items — Staff Review
            @if($bill->isDraft())
                <span style="font-weight:400;color:#92400e;margin-left:6px;text-transform:none;">
                    Physically verify each item, then approve or reject
                </span>
            @endif
        </div>
        <table>
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Stock</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Status</th>
                    @if($bill->isDraft())<th>Action</th>@endif
                </tr>
            </thead>
            <tbody>
            @foreach($rxItems as $item)
            @php
                $rxInvId = optional($item->prescriptionItem)->inventory_item_id;
                $rxStock = $rxInvId ? ($stockMap[$rxInvId] ?? 0) : null;
            @endphp
            <tr id="rx-row-{{ $item->id }}">
                <td>{{ $item->description ?? '—' }}</td>
                <td>
                    @if($rxStock === null)
                        <span class="stock-badge stock-badge-out">N/A</span>
                    @elseif($rxStock > 10)
                        <span class="stock-badge stock-badge-ok">{{ number_format($rxStock, 0) }}</span>
                    @elseif($rxStock > 0)
                        <span class="stock-badge stock-badge-low">{{ number_format($rxStock, 0) }}</span>
                    @else
                        <span class="stock-badge stock-badge-out">Out</span>
                    @endif
                </td>
                <td>
                    @if($bill->isDraft() && $item->status === 'pending')
                        <input type="number" class="qty-input" id="qty-{{ $item->id }}"
                               value="{{ $item->quantity }}" min="1" step="1">
                    @else
                        {{ $item->quantity }}
                    @endif
                </td>
                <td>₹{{ number_format($item->price, 2) }}</td>
                <td id="total-{{ $item->id }}">₹{{ number_format($item->total, 2) }}</td>
                <td>
                    <span class="badge badge-{{ $item->status }}" id="badge-{{ $item->id }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>
                @if($bill->isDraft())
                <td id="action-{{ $item->id }}">
                    @if($item->status === 'pending')
                        <button class="btn btn-success" onclick="approveItem({{ $item->id }})">✓ Approve</button>
                        <button class="btn btn-danger"  onclick="rejectItem({{ $item->id }}, this)" style="margin-left:4px;">✗ Reject</button>
                    @elseif($item->status === 'rejected')
                        <span style="font-size:13px;color:var(--muted);">Not available</span>
                    @else
                        <span style="font-size:13px;color:#065f46;">Verified ✓</span>
                    @endif
                </td>
                @endif
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- MANUAL / ADDITIONAL ITEMS --}}
    @php $manualItems = $bill->items->where('source','manual'); @endphp
    @if($manualItems->count())
    <div class="section section-clip">
        <div class="section-title">Additional Items</div>
        <table>
            <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th>@if($bill->isDraft())<th></th>@endif</tr></thead>
            <tbody>
            @foreach($manualItems as $item)
            <tr id="manual-row-{{ $item->id }}">
                <td>{{ $item->description ?? optional($item->priceItem)->name ?? '—' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->price, 2) }}</td>
                <td>₹{{ number_format($item->total, 2) }}</td>
                @if($bill->isDraft())
                <td><button class="btn btn-danger" onclick="rejectItem({{ $item->id }})">Remove</button></td>
                @endif
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- ADD ITEM --}}
    @if($bill->isDraft())
    <div class="section">
        <div class="section-title">Add Item</div>
        <form method="POST" action="{{ route('clinic.billing.item.add', $bill->id) }}">
            @csrf
            <input type="hidden" name="price_list_item_id" id="addItemId">
            <div class="add-item-row" style="position:relative;">
                <div style="flex:1;position:relative;">
                    <input type="text" id="addItemSearch" class="form-control" placeholder="Type to search items..." autocomplete="off"
                           style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:7px;font-size:14px;">
                    <div id="addItemDropdown" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid var(--border);border-radius:8px;max-height:250px;overflow-y:auto;z-index:50;box-shadow:0 8px 24px rgba(0,0,0,.12);margin-top:4px;"></div>
                </div>
                <input type="number" name="quantity" value="1" min="0.001" step="0.001" placeholder="Qty"
                       style="width:80px;padding:9px 10px;border:1px solid var(--border);border-radius:7px;font-size:14px;text-align:center;">
                <button type="submit" class="btn btn-grey">+ Add</button>
            </div>
        </form>
    </div>
    @endif

    {{-- TOTAL + CONFIRM --}}
    <div class="total-bar">
        <div>
            <div class="total-label">Total (approved items)</div>
            <div class="total-amount" id="bill-total">₹{{ number_format($bill->total_amount, 2) }}</div>
        </div>

        @if($bill->isDraft())
        <form method="POST" action="{{ route('clinic.billing.confirm', $bill->id) }}">
            @csrf
            <button type="submit" class="btn btn-primary">
                Confirm Bill &amp; Update Inventory →
            </button>
        </form>
        @else
        <div style="font-size:14px;color:#065f46;font-weight:600;margin-bottom:10px;">✅ Bill confirmed</div>
        <button type="button" onclick="sendBillWhatsApp({{ $bill->id }})" id="waBillBtn" class="btn" style="background:#25D366;color:#fff;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;font-weight:600;font-size:13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:4px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Send Bill via WhatsApp
        </button>
        @endif
    </div>

</div>

@php
    $priceItemsJson = $priceItems->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'price' => $p->price, 'type' => $p->item_type])->values();
@endphp
<script>
const _csrf = '{{ csrf_token() }}';

async function sendBillWhatsApp(billId) {
    const btn = document.getElementById('waBillBtn');
    btn.disabled = true;
    btn.textContent = 'Sending...';
    try {
        const res = await fetch('/clinic/whatsapp/send/bill/' + billId, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': _csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
            btn.textContent = '✓ Sent via WhatsApp';
            btn.style.background = '#16a34a';
        } else {
            alert(data.message || 'Failed to send');
            btn.textContent = 'Retry';
            btn.disabled = false;
        }
    } catch(e) {
        alert('Error: ' + e.message);
        btn.textContent = 'Retry';
        btn.disabled = false;
    }
}

async function approveItem(id) {
    const qty = parseFloat(document.getElementById('qty-' + id)?.value || 1);
    const res  = await fetch('/clinic/bill-items/' + id, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf },
        body: JSON.stringify({ status: 'approved', quantity: qty })
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('badge-' + id).className   = 'badge badge-approved';
        document.getElementById('badge-' + id).textContent = 'Approved';
        document.getElementById('bill-total').textContent  = '₹' + data.total;
        document.getElementById('action-' + id).innerHTML  = '<span style="font-size:13px;color:#065f46;">Verified ✓</span>';
    }
}

async function rejectItem(id) {
    const res  = await fetch('/clinic/bill-items/' + id, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf },
        body: JSON.stringify({ status: 'rejected' })
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('badge-' + id).className   = 'badge badge-rejected';
        document.getElementById('badge-' + id).textContent = 'Rejected';
        document.getElementById('bill-total').textContent  = '₹' + data.total;
        const actionCell = document.getElementById('action-' + id);
        if (actionCell) actionCell.innerHTML = '<span style="font-size:13px;color:var(--muted);">Removed</span>';
    }
}

// Searchable Add Item dropdown
const priceItems = @json($priceItemsJson);
const searchInput = document.getElementById('addItemSearch');
const dropdown = document.getElementById('addItemDropdown');
const hiddenId = document.getElementById('addItemId');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        if (q.length < 1) { dropdown.style.display = 'none'; return; }
        const matches = priceItems.filter(p => p.name.toLowerCase().includes(q)).slice(0, 15);
        if (matches.length === 0) {
            dropdown.innerHTML = '<div style="padding:10px 14px;color:#9ca3af;font-size:13px;">No items found</div>';
        } else {
            dropdown.innerHTML = matches.map(p =>
                '<div class="add-item-option" data-id="'+p.id+'" data-name="'+p.name+'" style="padding:10px 14px;cursor:pointer;font-size:14px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;" onmouseover="this.style.background=\'#f0f7ff\'" onmouseout="this.style.background=\'#fff\'">' +
                '<span>' + p.name + '</span><span style="color:#6b7280;font-size:13px;">₹' + parseFloat(p.price).toFixed(2) + '</span></div>'
            ).join('');
        }
        dropdown.style.display = 'block';
    });

    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 1) this.dispatchEvent(new Event('input'));
    });

    dropdown.addEventListener('click', function(e) {
        const opt = e.target.closest('.add-item-option');
        if (!opt) return;
        hiddenId.value = opt.dataset.id;
        searchInput.value = opt.dataset.name;
        dropdown.style.display = 'none';
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}
</script>

@endsection
