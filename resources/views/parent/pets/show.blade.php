@extends('layouts.parent')

@section('title', $pet->name)

@section('styles')
<style>
    .breadcrumb { font-size: 13px; color: #64748b; margin-bottom: 20px; }
    .breadcrumb a { color: #2563eb; }
    .pet-header { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    .pet-header h2 { font-size: 22px; font-weight: 700; color: #1e293b; }
    .pet-meta { display: flex; gap: 16px; flex-wrap: wrap; font-size: 13px; color: #64748b; }
    .pet-meta span { background: #f1f5f9; padding: 4px 10px; border-radius: 6px; }
    .tabs { display: flex; gap: 0; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; }
    .tab { padding: 10px 20px; font-size: 14px; font-weight: 600; color: #64748b; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; background: none; border-top: none; border-left: none; border-right: none; }
    .tab.active { color: #2563eb; border-bottom-color: #2563eb; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .record-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 12px; }
    .record-card:hover { border-color: #cbd5e1; }
    .record-date { font-size: 12px; color: #94a3b8; margin-bottom: 4px; }
    .record-title { font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 6px; }
    .record-detail { font-size: 13px; color: #64748b; margin-bottom: 2px; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-green { background: #d1fae5; color: #065f46; }
    .badge-blue { background: #dbeafe; color: #1e40af; }
    .badge-yellow { background: #fef9c3; color: #854d0e; }
    .badge-gray { background: #f1f5f9; color: #475569; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th { text-align: left; padding: 8px 10px; background: #f8fafc; color: #64748b; font-weight: 600; font-size: 12px; border-bottom: 1px solid #e2e8f0; }
    td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; }
    .empty-tab { text-align: center; padding: 40px; color: #94a3b8; font-size: 14px; }
</style>
@endsection

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('parent.dashboard') }}">My Pets</a> &rsaquo; {{ $pet->name }}
    </div>

    <div class="pet-header">
        <h2>{{ $pet->name }}</h2>
        <div class="pet-meta">
            <span>{{ ucfirst($pet->species) }}</span>
            <span>{{ $pet->breed ?? '—' }}</span>
            <span>Age: {{ $pet->current_age ?? '—' }}</span>
            <span>{{ ucfirst($pet->gender ?? '—') }}</span>
        </div>
    </div>

    <div class="tabs">
        <button class="tab active" onclick="switchTab('appointments')">Appointments</button>
        <button class="tab" onclick="switchTab('prescriptions')">Prescriptions</button>
        <button class="tab" onclick="switchTab('casesheets')">Case Sheets</button>
        <button class="tab" onclick="switchTab('bills')">Bills</button>
    </div>

    {{-- Appointments Tab --}}
    <div class="tab-content active" id="tab-appointments">
        @if($appointments->isEmpty())
            <div class="empty-tab">No appointments found.</div>
        @else
            @foreach($appointments as $appt)
                <a href="{{ route('parent.appointments.show', $appt) }}" class="record-card" style="display:block;text-decoration:none;color:inherit;">
                    <div class="record-date">{{ $appt->scheduled_at?->format('d M Y, h:i A') }}</div>
                    <div class="record-title">
                        {{ $appt->clinic->name ?? '' }}
                        @if($appt->vet) — Dr. {{ $appt->vet->name }} @endif
                    </div>
                    <div class="record-detail">
                        <span class="badge {{ $appt->status === 'completed' ? 'badge-green' : ($appt->status === 'scheduled' ? 'badge-blue' : 'badge-gray') }}">
                            {{ ucfirst($appt->status) }}
                        </span>
                        @if($appt->weight) &nbsp; Weight: {{ $appt->weight }} kg @endif
                    </div>
                </a>
            @endforeach
        @endif
    </div>

    {{-- Prescriptions Tab --}}
    <div class="tab-content" id="tab-prescriptions">
        @php $rxAppts = $appointments->filter(fn($a) => $a->prescription); @endphp
        @if($rxAppts->isEmpty())
            <div class="empty-tab">No prescriptions found.</div>
        @else
            @foreach($rxAppts as $appt)
                <div class="record-card">
                    <div class="record-date">{{ $appt->scheduled_at?->format('d M Y') }} — {{ $appt->clinic->name ?? '' }}</div>
                    <div class="record-title">Prescription @if($appt->vet) by Dr. {{ $appt->vet->name }} @endif</div>
                    @if($appt->prescription->items->isNotEmpty())
                        <table style="margin-top:8px;">
                            <thead><tr><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Duration</th></tr></thead>
                            <tbody>
                                @foreach($appt->prescription->items as $item)
                                    <tr>
                                        <td>{{ $item->medicine }}</td>
                                        <td>{{ $item->dosage ?? '—' }}</td>
                                        <td>{{ $item->frequency ?? '—' }}</td>
                                        <td>{{ $item->duration ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    @if($appt->prescription->notes)
                        <div style="font-size:12px;color:#64748b;margin-top:8px;font-style:italic;">{{ $appt->prescription->notes }}</div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    {{-- Case Sheets Tab --}}
    <div class="tab-content" id="tab-casesheets">
        @php $csAppts = $appointments->filter(fn($a) => $a->caseSheet); @endphp
        @if($csAppts->isEmpty())
            <div class="empty-tab">No case sheets found.</div>
        @else
            @foreach($csAppts as $appt)
                <div class="record-card">
                    <div class="record-date">{{ $appt->scheduled_at?->format('d M Y') }} — {{ $appt->clinic->name ?? '' }}</div>
                    <div class="record-title">Case Sheet @if($appt->vet) by Dr. {{ $appt->vet->name }} @endif</div>
                    @foreach([
                        'Complaint' => $appt->caseSheet->presenting_complaint,
                        'Diagnosis' => $appt->caseSheet->diagnosis,
                        'Treatment' => $appt->caseSheet->treatment_given,
                        'Advice' => $appt->caseSheet->advice,
                    ] as $label => $val)
                        @if($val)
                            <div style="margin-top:6px;">
                                <strong style="font-size:12px;color:#64748b;">{{ $label }}:</strong>
                                <span style="font-size:13px;">{{ $val }}</span>
                            </div>
                        @endif
                    @endforeach
                    @php
                        $vitals = collect([
                            'Temp' => $appt->caseSheet->temperature ? $appt->caseSheet->temperature . '°F' : null,
                            'HR' => $appt->caseSheet->heart_rate ? $appt->caseSheet->heart_rate . ' bpm' : null,
                            'RR' => $appt->caseSheet->respiratory_rate ? $appt->caseSheet->respiratory_rate . ' bpm' : null,
                            'CRT' => $appt->caseSheet->capillary_refill_time,
                            'MM' => $appt->caseSheet->mucous_membrane,
                            'Hydration' => $appt->caseSheet->hydration_status,
                        ])->filter();
                    @endphp
                    @if($vitals->isNotEmpty())
                        <div style="margin-top:6px;font-size:12px;color:#64748b;">
                            <strong>Vitals:</strong> {{ $vitals->map(fn($v, $k) => "$k: $v")->implode(' | ') }}
                        </div>
                    @endif
                    @if($appt->caseSheet->prognosis)
                        <div style="margin-top:6px;"><span class="badge badge-blue">Prognosis: {{ ucfirst($appt->caseSheet->prognosis) }}</span></div>
                    @endif
                    @if($appt->caseSheet->followup_date)
                        <div style="margin-top:4px;font-size:12px;color:#64748b;">Follow-up: {{ $appt->caseSheet->followup_date->format('d M Y') }}</div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    {{-- Bills Tab --}}
    <div class="tab-content" id="tab-bills">
        @php $billAppts = $appointments->filter(fn($a) => $a->bill); @endphp
        @if($billAppts->isEmpty())
            <div class="empty-tab">No bills found.</div>
        @else
            @foreach($billAppts as $appt)
                <div class="record-card">
                    <div class="record-date">{{ $appt->scheduled_at?->format('d M Y') }} — {{ $appt->clinic->name ?? '' }}</div>
                    <div class="record-title" style="display:flex;justify-content:space-between;align-items:center;">
                        <span>Invoice</span>
                        <span style="font-size:16px;font-weight:700;color:#1e293b;">&#8377;{{ number_format($appt->bill->total_amount, 2) }}</span>
                    </div>
                    <div style="margin-top:4px;">
                        <span class="badge {{ $appt->bill->status === 'confirmed' ? 'badge-green' : 'badge-yellow' }}">
                            {{ ucfirst($appt->bill->status) }}
                        </span>
                    </div>
                    @if($appt->bill->items->isNotEmpty())
                        <table style="margin-top:8px;">
                            <thead><tr><th>Item</th><th style="text-align:right;">Qty</th><th style="text-align:right;">Total</th></tr></thead>
                            <tbody>
                                @foreach($appt->bill->items->where('status', 'approved') as $item)
                                    <tr>
                                        <td>{{ $item->description ?? '—' }}</td>
                                        <td style="text-align:right;">{{ $item->quantity }}</td>
                                        <td style="text-align:right;">&#8377;{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    <script>
        function switchTab(name) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.getElementById('tab-' + name).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
@endsection
