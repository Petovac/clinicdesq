@extends('documents._base')
@section('doc-title', 'Bill / Invoice')

@section('doc-styles')
.doc { max-width: 700px; margin: 0 auto; padding: 20px 0; }
.header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #2563eb; padding-bottom: 16px; margin-bottom: 16px; }
.header-logo img, img.header-logo { max-height: 56px; max-width: 140px; }
.header-right { text-align: right; }
.header-right h1 { font-size: 17px; font-weight: 700; color: #2563eb; }
.header-right p { font-size: 11px; color: #6b7280; }
.badge { display: inline-block; background: #2563eb; color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; margin: 8px 0 12px; }
.info-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 6px; font-size: 13px; }
.bill-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.bill-table th { background: #2563eb; color: #fff; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; }
.bill-table th:first-child { border-radius: 8px 0 0 0; }
.bill-table th:last-child { border-radius: 0 8px 0 0; }
.bill-table td { padding: 10px 12px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
.bill-table .text-right { text-align: right; }
.total-bar { background: #1e40af; color: #fff; padding: 12px 14px; border-radius: 0 0 8px 8px; display: flex; justify-content: space-between; font-size: 15px; font-weight: 700; }
.source-badge { display: inline-block; background: #eff6ff; color: #2563eb; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }
.gst-box { margin-top: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: 12px; }
.footer { margin-top: 24px; display: flex; justify-content: space-between; align-items: flex-end; }
.signature-line { border-top: 2px solid #2563eb; width: 180px; text-align: center; padding-top: 4px; font-size: 12px; color: #2563eb; }
@endsection

@section('doc-content')
<div class="doc">
    <div class="header">
        <div>
            @if($logoUrl) <img src="{{ $logoUrl }}" alt="{{ $org->name }}" class="header-logo"> @endif
            <div style="font-weight:700;font-size:14px;margin-top:3px;">{{ $org->name }}</div>
        </div>
        <div class="header-right">
            <h1>{{ $clinic->name }}</h1>
            <p>{{ $clinic->address }}, {{ $clinic->city }} {{ $clinic->pincode }}</p>
        </div>
    </div>

    <span class="badge">INVOICE</span>

    <div class="info-card">
        <div><strong>Patient:</strong> {{ $pet->name }} ({{ ucfirst($pet->species) }})</div>
        <div><strong>Date:</strong> {{ $date }}</div>
        <div><strong>Parent:</strong> {{ $parent->name }}</div>
        <div><strong>Phone:</strong> {{ $parent->phone ?? '—' }}</div>
        <div><strong>Doctor:</strong> {{ $vet->name ?? '—' }}</div>
        <div><strong>Payment:</strong> {{ ucfirst($bill->payment_status ?? 'pending') }}</div>
    </div>

    <table class="bill-table">
        <thead><tr><th>#</th><th>Description</th><th>Type</th><th class="text-right">Qty</th><th class="text-right">Price</th><th class="text-right">Total</th></tr></thead>
        <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:600;">{{ $item->description ?? optional($item->priceItem)->name ?? '—' }}</td>
                    <td><span class="source-badge">{{ str_replace('_', ' ', $item->source) }}</span></td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="total-bar">
        <span>Total Amount</span>
        <span>{{ number_format($totalAmount, 2) }}</span>
    </div>

    @if($gstNumber)
        <div class="gst-box"><strong>GSTIN:</strong> {{ $gstNumber }}</div>
    @endif

    <div class="footer">
        <div style="font-size:11px;color:#94a3b8;">{{ now()->format('d M Y, h:i A') }}</div>
        <div class="signature-line">Authorised Signature</div>
    </div>
</div>
@endsection
