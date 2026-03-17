@extends('documents._base')
@section('doc-title', 'Bill / Invoice')

@section('doc-styles')
.doc { max-width: 660px; margin: 0 auto; padding: 20px 0; }
.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
.header-logo img, img.header-logo { max-height: 50px; max-width: 120px; }
.header-right { text-align: right; }
.header-right h1 { font-size: 16px; font-weight: 600; }
.header-right p { font-size: 11px; color: #888; }
.title { font-size: 13px; font-weight: 500; color: #888; text-transform: uppercase; letter-spacing: 2px; margin: 16px 0 12px; }
.thin-line { border: none; border-top: 1px solid #eee; margin: 10px 0; }
.info { font-size: 12px; color: #555; margin-bottom: 3px; }
.bill-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.bill-table th { border-bottom: 1px solid #ddd; padding: 8px 6px; text-align: left; font-size: 11px; color: #888; font-weight: 500; text-transform: uppercase; }
.bill-table td { padding: 10px 6px; font-size: 13px; border-bottom: 1px solid #f3f3f3; }
.bill-table .text-right { text-align: right; }
.total-row td { border-top: 2px solid #333; font-weight: 700; font-size: 14px; padding-top: 10px; }
.gst { margin-top: 12px; font-size: 11px; color: #999; }
.footer { margin-top: 40px; display: flex; justify-content: space-between; }
.signature-line { border-top: 1px solid #ccc; width: 160px; text-align: center; padding-top: 4px; font-size: 11px; color: #999; }
@endsection

@section('doc-content')
<div class="doc">
    <div class="header">
        <div>
            @if($logoUrl) <img src="{{ $logoUrl }}" alt="{{ $org->name }}" class="header-logo"> @endif
            <div style="font-weight:600;font-size:14px;margin-top:3px;">{{ $org->name }}</div>
        </div>
        <div class="header-right">
            <h1>{{ $clinic->name }}</h1>
            <p>{{ $clinic->address }}, {{ $clinic->city }} {{ $clinic->pincode }}</p>
        </div>
    </div>

    <hr class="thin-line">
    <div class="title">Invoice</div>

    <div class="info"><strong>{{ $pet->name }}</strong> &middot; {{ ucfirst($pet->species) }} &middot; {{ $parent->name }} &middot; {{ $parent->phone ?? '' }}</div>
    <div class="info">{{ $vet->name ?? '' }} &middot; {{ $date }}</div>

    <table class="bill-table">
        <thead><tr><th>#</th><th>Description</th><th class="text-right">Qty</th><th class="text-right">Price</th><th class="text-right">Total</th></tr></thead>
        <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td style="color:#aaa;">{{ $i + 1 }}</td>
                    <td>{{ $item->description ?? optional($item->priceItem)->name ?? '—' }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">Total</td>
                <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($gstNumber)
        <div class="gst">GSTIN: {{ $gstNumber }}</div>
    @endif

    <div class="footer">
        <div style="font-size:10px;color:#bbb;">{{ now()->format('d M Y') }}</div>
        <div class="signature-line">Signature</div>
    </div>
</div>
@endsection
