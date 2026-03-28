@extends('pdf.layout')

@section('content')

<div class="doc-badge">INVOICE</div>

{{-- Patient Info --}}
<div class="info-box">
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell"><span class="info-label">Patient:</span> {{ $pet->name ?? '—' }} ({{ ucfirst($pet->species ?? '') }})</div>
            <div class="info-cell"><span class="info-label">Bill #:</span> {{ $bill->id }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><span class="info-label">Parent:</span> {{ $parent->name ?? '—' }} ({{ $parent->phone ?? '' }})</div>
            <div class="info-cell"><span class="info-label">Date:</span> {{ $bill->created_at->format('d M Y') }}</div>
        </div>
    </div>
</div>

{{-- Bill Items --}}
<table class="data-table" style="margin-top:6px;">
    <thead>
        <tr>
            <th style="width:5%;">#</th>
            <th style="width:45%;">Description</th>
            <th style="width:10%;" class="text-center">Qty</th>
            <th style="width:18%;" class="text-right">Price</th>
            <th style="width:22%;" class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $i => $item)
        @if($item->status !== 'rejected')
        <tr>
            <td>{{ $i + 1 }}</td>
            <td style="font-weight:600;">
                {{ $item->description ?? ($item->priceItem->name ?? '—') }}
                @if($item->source)
                    <span style="font-weight:normal;font-size:9px;color:#6b7280;">({{ ucfirst($item->source) }})</span>
                @endif
            </td>
            <td class="text-center">{{ $item->quantity }}</td>
            <td class="text-right">{{ number_format($item->price, 2) }}</td>
            <td class="text-right font-bold">{{ number_format($item->total, 2) }}</td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>

{{-- Total --}}
<div style="text-align:right;margin-top:6px;padding:10px 0;border-top:2px solid #2563eb;">
    <span style="font-size:14px;font-weight:bold;color:#1a1a1a;">
        Total: &#8377;{{ number_format($bill->total_amount, 2) }}
    </span>
</div>

@if($bill->notes)
<div class="section" style="margin-top:10px;">
    <div class="section-title">Notes</div>
    <div class="section-content">{!! nl2br(e($bill->notes)) !!}</div>
</div>
@endif

@if($clinic->gst_number ?? $org->gst_number ?? null)
<div style="margin-top:14px;font-size:9px;color:#6b7280;">
    GSTIN: {{ $clinic->gst_number ?? $org->gst_number }}
</div>
@endif

<div class="footer" style="margin-top:20px;">
    <div class="footer-left">
        Status: {{ ucfirst($bill->payment_status) }}<br>
        {{ $bill->created_at->format('d M Y, h:i A') }}
    </div>
    <div class="footer-right">
        <div style="font-size:11px;font-weight:bold;">{{ $clinic->name }}</div>
        <div style="font-size:9px;color:#6b7280;">Authorised Signatory</div>
    </div>
</div>

@endsection
