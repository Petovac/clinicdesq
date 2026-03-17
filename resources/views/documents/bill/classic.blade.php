@extends('documents._base')
@section('doc-title', 'Bill / Invoice')

@section('doc-styles')
.doc { max-width: 700px; margin: 0 auto; padding: 20px 0; }
.header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1a1a1a; padding-bottom: 14px; margin-bottom: 16px; }
.header-logo img { max-height: 60px; max-width: 140px; }
.header-right { text-align: right; }
.header-right h1 { font-size: 18px; font-weight: 700; }
.header-right p { font-size: 11px; color: #555; }
.doc-title { text-align: center; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin: 14px 0; border: 1px solid #ccc; padding: 6px; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 20px; font-size: 13px; margin-bottom: 12px; }
.divider { border: none; border-top: 1px solid #ccc; margin: 12px 0; }
.bill-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.bill-table th { background: #f5f5f5; border: 1px solid #ccc; padding: 8px 10px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; }
.bill-table td { border: 1px solid #ccc; padding: 8px 10px; font-size: 13px; }
.bill-table tr:nth-child(even) { background: #fafafa; }
.bill-table .text-right { text-align: right; }
.total-row { font-weight: 700; background: #f0f0f0 !important; }
.source-badge { display: inline-block; background: #e5e5e5; color: #555; padding: 1px 6px; border-radius: 3px; font-size: 10px; text-transform: uppercase; }
.gst-info { margin-top: 14px; font-size: 12px; color: #555; border-top: 1px solid #ccc; padding-top: 8px; }
.footer { margin-top: 24px; display: flex; justify-content: space-between; }
.signature-line { border-top: 1px solid #555; width: 180px; text-align: center; padding-top: 4px; font-size: 12px; }
@endsection

@section('doc-content')
<div class="doc">
    <div class="header">
        <div class="header-logo">
            @if($logoUrl) <img src="{{ $logoUrl }}" alt="{{ $org->name }}"> @endif
            <div style="font-weight:700;font-size:15px;margin-top:4px;">{{ $org->name }}</div>
        </div>
        <div class="header-right">
            <h1>{{ $clinic->name }}</h1>
            <p>{{ $clinic->address }}, {{ $clinic->city }} {{ $clinic->pincode }}</p>
            @if($clinic->phone) <p>Ph: {{ $clinic->phone }}</p> @endif
        </div>
    </div>

    <div class="doc-title">Bill / Invoice</div>

    <div class="info-grid">
        <div><strong>Patient:</strong> {{ $pet->name }} ({{ ucfirst($pet->species) }})</div>
        <div><strong>Date:</strong> {{ $date }}</div>
        <div><strong>Parent:</strong> {{ $parent->name }}</div>
        <div><strong>Phone:</strong> {{ $parent->phone ?? '—' }}</div>
        <div><strong>Doctor:</strong> {{ $vet->name ?? '—' }}</div>
        <div><strong>Status:</strong> {{ ucfirst($bill->status) }}</div>
    </div>

    <hr class="divider">

    <table class="bill-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Source</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->description ?? optional($item->priceItem)->name ?? '—' }}</td>
                    <td><span class="source-badge">{{ str_replace('_', ' ', $item->source) }}</span></td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($gstNumber)
        <div class="gst-info">
            <strong>GSTIN:</strong> {{ $gstNumber }}
        </div>
    @endif

    <div class="footer">
        <div style="font-size:11px;color:#777;">{{ now()->format('d M Y, h:i A') }}</div>
        <div class="signature-line">Authorised Signature</div>
    </div>
</div>
@endsection
