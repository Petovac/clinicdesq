<div style="font-size:14px;">

<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;margin-bottom:14px;">
    <strong style="color:#92400e;">IPD Admission</strong>
    <span style="float:right;font-size:12px;padding:2px 8px;border-radius:10px;font-weight:600;{{ $admission->status === 'discharged' ? 'background:#dcfce7;color:#166534;' : 'background:#dbeafe;color:#1d4ed8;' }}">
        {{ ucfirst(str_replace('_', ' ', $admission->status)) }}
    </span>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:14px;">
    <div><strong>Admitted:</strong> {{ \Carbon\Carbon::parse($admission->admission_date)->format('d M Y, h:i A') }}</div>
    @if($admission->discharged_at)
    <div><strong>Discharged:</strong> {{ \Carbon\Carbon::parse($admission->discharged_at)->format('d M Y, h:i A') }}</div>
    @endif
    @if($admission->cage_number)<div><strong>Cage:</strong> {{ $admission->cage_number }}</div>@endif
    @if($admission->ward)<div><strong>Ward:</strong> {{ $admission->ward }}</div>@endif
</div>

@if($admission->admission_reason)
<div style="margin-bottom:10px;"><strong>Reason:</strong> {{ $admission->admission_reason }}</div>
@endif

@if($admission->tentative_diagnosis)
<div style="margin-bottom:10px;"><strong>Tentative Diagnosis:</strong> {{ $admission->tentative_diagnosis }}</div>
@endif

{{-- Treatments --}}
@if($admission->treatments->count())
<hr style="border:none;border-top:1px solid #f3f4f6;margin:12px 0;">
<h4 style="margin:0 0 8px;font-size:14px;">Treatments ({{ $admission->treatments->count() }})</h4>
<table style="width:100%;border-collapse:collapse;font-size:13px;">
    <thead>
        <tr style="background:#f9fafb;">
            <th style="padding:6px 8px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;">Time</th>
            <th style="padding:6px 8px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;">Type</th>
            <th style="padding:6px 8px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;">Drug/Details</th>
            <th style="padding:6px 8px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;">Dose</th>
            <th style="padding:6px 8px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;">Route</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admission->treatments->sortByDesc('administered_at') as $tx)
        <tr style="border-bottom:1px solid #f3f4f6;">
            <td style="padding:6px 8px;color:#6b7280;font-size:12px;">{{ $tx->administered_at ? \Carbon\Carbon::parse($tx->administered_at)->format('d/m H:i') : '—' }}</td>
            <td style="padding:6px 8px;">
                <span style="font-size:10px;padding:1px 6px;border-radius:4px;font-weight:600;{{ $tx->treatment_type === 'injection' ? 'background:#dbeafe;color:#1d4ed8;' : ($tx->treatment_type === 'procedure' ? 'background:#fef3c7;color:#92400e;' : 'background:#f3f4f6;color:#374151;') }}">
                    {{ ucfirst($tx->treatment_type) }}
                </span>
            </td>
            <td style="padding:6px 8px;font-weight:600;">{{ $tx->drug_name ?: ($tx->procedure_name ?? '—') }}</td>
            <td style="padding:6px 8px;">
                @if($tx->dose_mg){{ $tx->dose_mg }}mg @endif
                @if($tx->dose_volume_ml)({{ $tx->dose_volume_ml }}ml)@endif
            </td>
            <td style="padding:6px 8px;">{{ $tx->route ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Clinical Notes --}}
@if($admission->notes->count())
<hr style="border:none;border-top:1px solid #f3f4f6;margin:12px 0;">
<h4 style="margin:0 0 8px;font-size:14px;">Clinical Notes ({{ $admission->notes->count() }})</h4>
@foreach($admission->notes->sortByDesc('created_at') as $note)
<div style="background:#f9fafb;border-radius:6px;padding:8px 10px;margin-bottom:6px;">
    <strong style="font-size:12px;color:#374151;">{{ ucfirst($note->note_type ?? 'Note') }}</strong>
    <span style="float:right;font-size:11px;color:#9ca3af;">{{ $note->created_at->format('d M, h:i A') }}</span>
    <p style="margin:4px 0 0;font-size:13px;">{{ $note->content }}</p>
</div>
@endforeach
@endif

{{-- Discharge Summary --}}
@if($admission->discharge_summary)
<hr style="border:none;border-top:1px solid #f3f4f6;margin:12px 0;">
<h4 style="margin:0 0 8px;font-size:14px;">Discharge Summary</h4>
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:10px;font-size:13px;">
    {{ $admission->discharge_summary }}
</div>
@endif

@if($admission->discharge_notes)
<div style="margin-top:6px;font-size:13px;"><strong>Discharge Notes:</strong> {{ $admission->discharge_notes }}</div>
@endif

</div>
