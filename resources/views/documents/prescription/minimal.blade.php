@extends('documents._base')
@section('doc-title', 'Prescription')

@section('doc-styles')
.doc { max-width: 660px; margin: 0 auto; padding: 20px 0; }
.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
.header-logo img, img.header-logo { max-height: 50px; max-width: 120px; }
.header-right { text-align: right; }
.header-right h1 { font-size: 16px; font-weight: 600; color: #1a1a1a; }
.header-right p { font-size: 11px; color: #888; }
.title { font-size: 13px; font-weight: 500; color: #888; text-transform: uppercase; letter-spacing: 2px; margin: 16px 0 12px; }
.thin-line { border: none; border-top: 1px solid #eee; margin: 10px 0; }
.info { font-size: 12px; color: #555; margin-bottom: 3px; }
.info strong { color: #1a1a1a; }
.rx-table { width: 100%; border-collapse: collapse; margin-top: 14px; }
.rx-table th { border-bottom: 1px solid #ddd; padding: 8px 6px; text-align: left; font-size: 11px; color: #888; font-weight: 500; text-transform: uppercase; }
.rx-table td { padding: 10px 6px; font-size: 13px; border-bottom: 1px solid #f3f3f3; }
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
            <p>{{ $clinic->address }}</p>
            <p>{{ $clinic->city }} {{ $clinic->pincode }}</p>
        </div>
    </div>

    <hr class="thin-line">
    <div class="title">Prescription</div>

    <div class="info"><strong>Patient:</strong> {{ $pet->name }} &middot; {{ ucfirst($pet->species) }} &middot; {{ $pet->breed ?? '' }} &middot; {{ $appointment->weight ? $appointment->weight.' kg' : '' }}</div>
    <div class="info"><strong>Parent:</strong> {{ $parent->name }} &middot; {{ $parent->phone ?? '' }}</div>
    <div class="info"><strong>Doctor:</strong> {{ $vet->name ?? '—' }} &middot; <strong>Date:</strong> {{ $date }}</div>

    @if($prescription->notes)
        <div style="margin-top:10px;font-size:12px;color:#555;font-style:italic;">{{ $prescription->notes }}</div>
    @endif

    <table class="rx-table">
        <thead><tr><th>#</th><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Instructions</th></tr></thead>
        <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td style="color:#aaa;">{{ $i + 1 }}</td>
                    <td>{{ $item->medicine }}</td>
                    <td>{{ $item->dosage ?? '—' }}</td>
                    <td>{{ $item->frequency ?? '—' }}</td>
                    <td>{{ $item->duration ?? '—' }}</td>
                    <td style="color:#666;">{{ $item->instructions ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div style="font-size:10px;color:#bbb;">{{ now()->format('d M Y') }}</div>
        @include('documents.partials.vet-stamp')
    </div>
</div>
@endsection
