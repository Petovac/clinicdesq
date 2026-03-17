@extends('documents._base')
@section('doc-title', 'Prescription')

@section('doc-styles')
.doc { max-width: 700px; margin: 0 auto; padding: 20px 0; }
.header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1a1a1a; padding-bottom: 14px; margin-bottom: 16px; }
.header-logo img { max-height: 60px; max-width: 140px; }
.header-right { text-align: right; }
.header-right h1 { font-size: 18px; font-weight: 700; margin-bottom: 2px; }
.header-right p { font-size: 11px; color: #555; }
.doc-title { text-align: center; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin: 14px 0; border: 1px solid #ccc; padding: 6px; }
.info-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 13px; }
.info-row strong { color: #333; }
.divider { border: none; border-top: 1px solid #ccc; margin: 12px 0; }
.rx-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.rx-table th { background: #f5f5f5; border: 1px solid #ccc; padding: 8px 10px; text-align: left; font-size: 12px; font-weight: 600; text-transform: uppercase; }
.rx-table td { border: 1px solid #ccc; padding: 8px 10px; font-size: 13px; }
.rx-table tr:nth-child(even) { background: #fafafa; }
.footer { margin-top: 30px; display: flex; justify-content: space-between; font-size: 12px; }
.signature-line { border-top: 1px solid #555; width: 180px; text-align: center; padding-top: 4px; }
.notes { background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px; padding: 10px; margin-top: 10px; font-size: 12px; }
@endsection

@section('doc-content')
<div class="doc">
    <div class="header">
        <div class="header-logo">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $org->name }}">
            @endif
            <div style="font-weight:700;font-size:15px;margin-top:4px;">{{ $org->name }}</div>
        </div>
        <div class="header-right">
            <h1>{{ $clinic->name }}</h1>
            <p>{{ $clinic->address }}</p>
            <p>{{ $clinic->city }}{{ $clinic->state ? ', '.$clinic->state : '' }} {{ $clinic->pincode }}</p>
            @if($clinic->phone) <p>Ph: {{ $clinic->phone }}</p> @endif
        </div>
    </div>

    <div class="doc-title">Prescription</div>

    <div class="info-row"><span><strong>Patient:</strong> {{ $pet->name }} ({{ ucfirst($pet->species) }}{{ $pet->breed ? ' / '.$pet->breed : '' }})</span> <span><strong>Date:</strong> {{ $date }}</span></div>
    <div class="info-row"><span><strong>Age:</strong> {{ $appointment->calculated_age_at_visit ?? '—' }}</span> <span><strong>Weight:</strong> {{ $appointment->weight ? $appointment->weight.' kg' : '—' }}</span></div>
    <div class="info-row"><span><strong>Parent:</strong> {{ $parent->name }}</span> <span><strong>Phone:</strong> {{ $parent->phone ?? '—' }}</span></div>
    <div class="info-row"><span><strong>Doctor:</strong> {{ $vet->name ?? '—' }}</span></div>

    <hr class="divider">

    @if($prescription->notes)
        <div class="notes"><strong>Notes:</strong> {{ $prescription->notes }}</div>
    @endif

    <table class="rx-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Medicine</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Duration</th>
                <th>Instructions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->medicine }}</td>
                    <td>{{ $item->dosage ?? '—' }}</td>
                    <td>{{ $item->frequency ?? '—' }}</td>
                    <td>{{ $item->duration ?? '—' }}</td>
                    <td>{{ $item->instructions ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div style="font-size:11px;color:#777;">Generated on {{ now()->format('d M Y, h:i A') }}</div>
        @include('documents.partials.vet-stamp')
    </div>
</div>
@endsection
