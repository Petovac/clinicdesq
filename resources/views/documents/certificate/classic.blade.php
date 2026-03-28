<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
@page { margin: 20mm 15mm; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #222; line-height: 1.5; }

.header { display: table; width: 100%; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 15px; }
.header-left { display: table-cell; width: 60px; vertical-align: middle; }
.header-logo { max-height: 50px; max-width: 50px; }
.header-right { display: table-cell; vertical-align: middle; padding-left: 12px; }
.org-name { font-size: 16px; font-weight: bold; color: #1e3a5f; }
.clinic-name { font-size: 12px; font-weight: bold; color: #374151; }
.clinic-details { font-size: 9px; color: #6b7280; }

.cert-title { text-align: center; font-size: 18px; font-weight: bold; color: #1e3a5f; margin: 15px 0 5px; text-transform: uppercase; letter-spacing: 1px; }
.cert-number { text-align: center; font-size: 9px; color: #6b7280; margin-bottom: 15px; }

.info-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
.info-table td { padding: 4px 8px; font-size: 11px; border: 1px solid #e5e7eb; }
.info-table .label { font-weight: bold; color: #374151; background: #f9fafb; width: 35%; }

.section { margin-bottom: 12px; }
.section-title { font-size: 11px; font-weight: bold; color: #1e3a5f; margin-bottom: 4px; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px; }
.section-content { font-size: 11px; color: #374151; white-space: pre-wrap; line-height: 1.6; }

.vacc-table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 10px; }
.vacc-table th { background: #1e3a5f; color: #fff; padding: 5px 6px; text-align: left; font-size: 9px; text-transform: uppercase; }
.vacc-table td { padding: 4px 6px; border: 1px solid #e5e7eb; }
.vacc-table tr:nth-child(even) { background: #f9fafb; }

.footer { margin-top: 30px; display: table; width: 100%; }
.footer-left { display: table-cell; width: 50%; vertical-align: bottom; }
.footer-right { display: table-cell; width: 50%; vertical-align: bottom; text-align: right; }
.signature-box { text-align: right; }
.signature-img { max-height: 50px; margin-bottom: 2px; }
.vet-name { font-size: 12px; font-weight: bold; color: #111; }
.vet-details { font-size: 9px; color: #6b7280; }
.date-box { font-size: 10px; color: #374151; }

.watermark { position: fixed; top: 40%; left: 20%; font-size: 60px; color: rgba(0,0,0,0.03); transform: rotate(-30deg); z-index: -1; font-weight: bold; }
.border-frame { border: 2px solid #2563eb; padding: 15px; min-height: 800px; position: relative; }
</style>
</head>
<body>

@if(!($isPreview ?? false))
<div class="watermark">{{ strtoupper($certificate->certificate_type) }} CERTIFICATE</div>
@endif

<div class="border-frame">

{{-- Header --}}
<div class="header">
    <div class="header-left">
        @if($org->logo_path)
        <img src="{{ public_path('storage/' . $org->logo_path) }}" class="header-logo">
        @endif
    </div>
    <div class="header-right">
        <div class="org-name">{{ $org->name }}</div>
        <div class="clinic-name">{{ $clinic->name }}</div>
        <div class="clinic-details">
            {{ $clinic->address }}{{ $clinic->city ? ', ' . $clinic->city : '' }}{{ $clinic->state ? ', ' . $clinic->state : '' }} {{ $clinic->pincode }}
            <br>
            @if($clinic->phone) Ph: {{ $clinic->phone }} @endif
            @if($clinic->email) | {{ $clinic->email }} @endif
            @if($clinic->gst_number ?? $org->gst_number) | GST: {{ $clinic->gst_number ?? $org->gst_number }} @endif
        </div>
    </div>
</div>

{{-- Title --}}
<div class="cert-title">{{ $certificate->title }}</div>
<div class="cert-number">
    Certificate No: {{ $certificate->certificate_number }}
    &nbsp;&nbsp;|&nbsp;&nbsp;
    Date: {{ $certificate->issued_date?->format('d M Y') ?? now()->format('d M Y') }}
    @if($certificate->valid_until)
    &nbsp;&nbsp;|&nbsp;&nbsp;
    Valid Until: {{ $certificate->valid_until->format('d M Y') }}
    @endif
</div>

{{-- Pet & Parent Details --}}
<table class="info-table">
    <tr>
        <td class="label">Pet Name</td>
        <td>{{ $pet->name }}</td>
        <td class="label">Species</td>
        <td>{{ ucfirst($pet->species ?? '—') }}</td>
    </tr>
    <tr>
        <td class="label">Breed</td>
        <td>{{ $pet->breed ?? '—' }}</td>
        <td class="label">Age</td>
        <td>{{ $pet->current_age ?? ($pet->age ? $pet->age . 'y' : '—') }}</td>
    </tr>
    <tr>
        <td class="label">Gender</td>
        <td>{{ ucfirst($pet->gender ?? '—') }}</td>
        <td class="label">Color/Markings</td>
        <td>{{ $pet->color ?? '—' }}</td>
    </tr>
    <tr>
        <td class="label">Owner Name</td>
        <td>{{ $parent->name ?? '—' }}</td>
        <td class="label">Contact</td>
        <td>{{ $parent->phone ?? '—' }}</td>
    </tr>
    @if($parent && $parent->address)
    <tr>
        <td class="label">Owner Address</td>
        <td colspan="3">{{ $parent->address }}</td>
    </tr>
    @endif
</table>

{{-- Certificate Content —- Template Fields --}}
@php $content = $certificate->content ?? []; @endphp
@if($certificate->template)
    @foreach($certificate->template->getFields() as $field)
        @if($field['type'] === 'auto_vaccinations')
            {{-- Auto-generate vaccination table --}}
            @if($vaccinations->count())
            <div class="section">
                <div class="section-title">{{ $field['label'] }}</div>
                <table class="vacc-table">
                    <thead>
                        <tr>
                            <th>Vaccine</th>
                            <th>Brand</th>
                            <th>Dose</th>
                            <th>Date</th>
                            <th>Batch No.</th>
                            <th>Next Due</th>
                            <th>Vet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vaccinations as $v)
                        <tr>
                            <td>{{ $v->vaccine_name }}</td>
                            <td>{{ $v->brand_name ?? '—' }}</td>
                            <td>{{ $v->dose_number }}</td>
                            <td>{{ $v->administered_date->format('d/m/Y') }}</td>
                            <td>{{ $v->batch_number ?? '—' }}</td>
                            <td>{{ $v->next_due_date?->format('d/m/Y') ?? '—' }}</td>
                            <td>{{ $v->vet->name ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        @else
            @php $val = $content[$field['key']] ?? $field['default'] ?? ''; @endphp
            @if($val)
            <div class="section">
                <div class="section-title">{{ $field['label'] }}</div>
                <div class="section-content">{{ $val }}</div>
            </div>
            @endif
        @endif
    @endforeach
@endif

{{-- Footer: Signature --}}
<div class="footer">
    <div class="footer-left">
        <div class="date-box">
            <strong>Place:</strong> {{ $clinic->city ?? '—' }}<br>
            <strong>Date:</strong> {{ $certificate->issued_date?->format('d M Y') ?? now()->format('d M Y') }}
        </div>
    </div>
    <div class="footer-right">
        <div class="signature-box">
            @if($vet->signature_path)
            <img src="{{ public_path('storage/' . $vet->signature_path) }}" class="signature-img"><br>
            @else
            <div style="height:40px;"></div>
            @endif
            <div class="vet-name">{{ str_starts_with($vet->name, 'Dr') ? $vet->name : 'Dr. ' . $vet->name }}</div>
            <div class="vet-details">
                @if($vet->degree) {{ $vet->degree }} <br> @endif
                @if($vet->registration_number) Reg. No: {{ $vet->registration_number }} <br> @endif
                {{ $clinic->name }}
            </div>
        </div>
    </div>
</div>

</div>

</body>
</html>
