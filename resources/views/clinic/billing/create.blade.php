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
.section { background: var(--card); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 16px; overflow: hidden; }
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
</style>

<div class="bill-wrap">

    <div class="bill-header">
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

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">⚠ {{ session('error') }}</div>
    @endif

    {{-- VISIT FEE --}}
    @php $visitItems = $bill->items->where('source','visit_fee'); @endphp
    @if($visitItems->count())
    <div class="section">
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
    <div class="section">
        <div class="section-title">Injectable Drugs (administered)</div>
        <table>
            <thead><tr><th>Drug</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
            <tbody>
            @foreach($injections as $item)
            <tr>
                <td>{{ $item->description ?? optional($item->priceItem)->name ?? '—' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->price, 2) }}</td>
                <td>₹{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- PROCEDURES --}}
    @php $procedures = $bill->items->where('source','procedure'); @endphp
    @if($procedures->count())
    <div class="section">
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
    <div class="section">
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
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Status</th>
                    @if($bill->isDraft())<th>Action</th>@endif
                </tr>
            </thead>
            <tbody>
            @foreach($rxItems as $item)
            <tr id="rx-row-{{ $item->id }}">
                <td>{{ $item->description ?? '—' }}</td>
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
    <div class="section">
        <div class="section-title">Additional Items</div>
        <table>
            <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
            <tbody>
            @foreach($manualItems as $item)
            <tr>
                <td>{{ $item->description ?? optional($item->priceItem)->name ?? '—' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->price, 2) }}</td>
                <td>₹{{ number_format($item->total, 2) }}</td>
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
        <form method="POST" action="{{ route('billing.item.add', $bill->id) }}">
            @csrf
            <div class="add-item-row">
                <select name="price_list_item_id" required>
                    <option value="">Select from price list…</option>
                    @foreach($priceItems as $pi)
                        <option value="{{ $pi->id }}">{{ $pi->name }} — ₹{{ number_format($pi->price, 2) }}</option>
                    @endforeach
                </select>
                <input type="number" name="quantity" value="1" min="0.001" step="0.001" placeholder="Qty">
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
        <form method="POST" action="{{ route('billing.confirm', $bill->id) }}">
            @csrf
            <button type="submit" class="btn btn-primary">
                Confirm Bill &amp; Update Inventory →
            </button>
        </form>
        @else
        <div style="font-size:14px;color:#065f46;font-weight:600;">✅ Bill confirmed</div>
        @endif
    </div>

</div>

<script>
const _csrf = '{{ csrf_token() }}';

async function approveItem(id) {
    const qty = parseFloat(document.getElementById('qty-' + id)?.value || 1);
    const res  = await fetch('/bill-items/' + id, {
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
    const res  = await fetch('/bill-items/' + id, {
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
</script>

@endsection
