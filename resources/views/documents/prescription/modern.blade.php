@extends('documents._base')
@section('doc-title', 'Prescription')

@section('doc-styles')
.doc { max-width: 700px; margin: 0 auto; padding: 20px 0; }
.header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; margin-bottom: 16px; border-bottom: 3px solid #2563eb; }
.header-logo img { max-height: 56px; max-width: 130px; }
.header-right { text-align: right; }
.header-right h1 { font-size: 17px; font-weight: 700; color: #2563eb; }
.header-right p { font-size: 11px; color: #6b7280; }
.badge { display: inline-block; background: #2563eb; color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; letter-spacing: .5px; margin: 10px 0 14px; }
.info-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 6px; font-size: 13px; }
.info-card strong { color: #334155; }
.rx-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 8px; }
.rx-table th { background: #2563eb; color: #fff; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; }
.rx-table th:first-child { border-radius: 8px 0 0 0; }
.rx-table th:last-child { border-radius: 0 8px 0 0; }
.rx-table td { padding: 10px 12px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
.rx-table tr:last-child td:first-child { border-radius: 0 0 0 8px; }
.rx-table tr:last-child td:last-child { border-radius: 0 0 8px 0; }
.footer { margin-top: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
.signature-line { border-top: 2px solid #2563eb; width: 180px; text-align: center; padding-top: 4px; font-size: 12px; color: #2563eb; }
.notes { background: #eff6ff; border-left: 3px solid #2563eb; padding: 10px 14px; margin-bottom: 10px; font-size: 12px; border-radius: 0 6px 6px 0; }
@endsection

@section('doc-content')
<div class="doc">
    <div class="header">
        <div class="header-logo">
            @if($logoUrl) <img src="{{ $logoUrl }}" alt="{{ $org->name }}"> @endif
            <div style="font-weight:700;font-size:14px;margin-top:3px;">{{ $org->name }}</div>
        </div>
        <div class="header-right">
            <h1>{{ $clinic->name }}</h1>
            <p>{{ $clinic->address }}, {{ $clinic->city }} {{ $clinic->pincode }}</p>
            @if($clinic->phone) <p>{{ $clinic->phone }}</p> @endif
        </div>
    </div>

    <span class="badge">PRESCRIPTION</span>

    <div class="info-card">
        <div><strong>Patient:</strong> {{ $pet->name }} ({{ ucfirst($pet->species) }})</div>
        <div><strong>Date:</strong> {{ $date }}</div>
        <div><strong>Breed:</strong> {{ $pet->breed ?? '—' }}</div>
        <div><strong>Weight:</strong> {{ $appointment->weight ? $appointment->weight.' kg' : '—' }}</div>
        <div><strong>Parent:</strong> {{ $parent->name }}</div>
        <div><strong>Doctor:</strong> {{ $vet->name ?? '—' }}</div>
    </div>

    @if($prescription->notes)
        <div class="notes"><strong>Notes:</strong> {{ $prescription->notes }}</div>
    @endif

    <table class="rx-table">
        <thead><tr><th>#</th><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Instructions</th></tr></thead>
        <tbody>
            @foreach($items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:600;">{{ $item->medicine }}</td>
                    <td>{{ $item->dosage ?? '—' }}</td>
                    <td>{{ $item->frequency ?? '—' }}</td>
                    <td>{{ $item->duration ?? '—' }}</td>
                    <td>{{ $item->instructions ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div style="font-size:11px;color:#94a3b8;">{{ now()->format('d M Y, h:i A') }}</div>
        @include('documents.partials.vet-stamp')
    </div>
</div>
@endsection
