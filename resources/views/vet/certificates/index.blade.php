@extends('layouts.vet')

@section('content')
<style>
.cert-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.cert-header h2 { font-size:20px; font-weight:700; margin:0; }
.cert-actions { display:flex; gap:8px; }
.btn { padding:8px 16px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; text-decoration:none; border:none; display:inline-flex; align-items:center; gap:4px; }
.btn-primary { background:var(--primary); color:#fff; }
.btn-sm { padding:5px 10px; font-size:12px; }
.btn-outline { background:#fff; color:var(--primary); border:1px solid var(--primary); }
.card { background:#fff; border-radius:10px; padding:18px; border:1px solid var(--border); margin-bottom:14px; }
.cert-table { width:100%; border-collapse:collapse; }
.cert-table th { text-align:left; padding:8px 10px; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; background:#f9fafb; border-bottom:1px solid #e5e7eb; }
.cert-table td { padding:10px; font-size:13px; border-bottom:1px solid #f3f4f6; }
.cert-table tr:hover td { background:#f9fafb; }
.badge { padding:2px 8px; border-radius:10px; font-size:10px; font-weight:600; }
.badge-draft { background:#fef3c7; color:#92400e; }
.badge-issued { background:#dcfce7; color:#166534; }
.type-badge { background:#dbeafe; color:#1d4ed8; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:600; }
.empty { text-align:center; padding:30px; color:#9ca3af; font-size:14px; }
.pet-bar { background:var(--primary-soft); border:1px solid var(--primary-border); border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:13px; }
.pet-bar strong { color:var(--text-dark); }
</style>

<div class="cert-header">
    <h2>Certificates — {{ $pet->name }}</h2>
    <div class="cert-actions">
        <a href="{{ route('vet.pet.show', $pet) }}" class="btn btn-outline">← Back to Profile</a>
        <a href="{{ route('vet.certificates.create', $pet) }}" class="btn btn-primary">+ Issue Certificate</a>
    </div>
</div>

<div class="pet-bar">
    <strong>{{ $pet->name }}</strong> · {{ ucfirst($pet->species) }} · {{ $pet->breed }} · {{ $pet->current_age ?? $pet->age.'y' }} · {{ ucfirst($pet->gender) }}
    @if($pet->petParent) · Owner: {{ $pet->petParent->name }} @endif
</div>

<div class="card">
@if($certificates->count())
<table class="cert-table">
    <thead>
        <tr>
            <th>Certificate #</th>
            <th>Type</th>
            <th>Title</th>
            <th>Issued Date</th>
            <th>Valid Until</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($certificates as $cert)
        <tr>
            <td style="font-weight:600;">{{ $cert->certificate_number }}</td>
            <td><span class="type-badge">{{ ucfirst($cert->certificate_type) }}</span></td>
            <td>{{ $cert->title }}</td>
            <td>{{ $cert->issued_date->format('d M Y') }}</td>
            <td>{{ $cert->valid_until?->format('d M Y') ?? '—' }}</td>
            <td><span class="badge badge-{{ $cert->status }}">{{ ucfirst($cert->status) }}</span></td>
            <td>
                @if($cert->status === 'draft')
                    <a href="{{ route('vet.certificates.edit', $cert) }}" class="btn btn-sm btn-outline">Edit</a>
                @endif
                @if($cert->status === 'issued')
                    <a href="{{ route('vet.certificates.download', $cert) }}" class="btn btn-sm btn-primary">Download</a>
                @endif
                <a href="{{ route('vet.certificates.preview', $cert) }}" class="btn btn-sm btn-outline" target="_blank">Preview</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="empty">No certificates issued yet for {{ $pet->name }}.</div>
@endif
</div>
@endsection
