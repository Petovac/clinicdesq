@extends('admin.layout')

@section('content')
<style>
.detail-card { background:#fff; border-radius:10px; padding:24px; border:1px solid #e5e7eb; margin-bottom:20px; max-width:800px; }
.detail-row { display:flex; padding:10px 0; border-bottom:1px solid #f3f4f6; }
.detail-label { width:180px; font-weight:600; font-size:13px; color:#6b7280; flex-shrink:0; }
.detail-value { font-size:14px; color:#111827; flex:1; }
.section-title { font-size:16px; font-weight:700; margin:20px 0 12px; color:#111827; }
.badge { padding:3px 12px; border-radius:12px; font-size:12px; font-weight:600; }
.badge-pending { background:#fef3c7; color:#92400e; }
.badge-approved { background:#dcfce7; color:#166534; }
.badge-rejected { background:#fee2e2; color:#991b1b; }
.action-card { background:#f8fafc; border:1px solid #e5e7eb; border-radius:10px; padding:20px; max-width:800px; }
.form-group { margin-bottom:14px; }
.form-group label { display:block; font-size:13px; font-weight:600; margin-bottom:4px; color:#374151; }
.form-group input, .form-group select, .form-group textarea { width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:14px; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 2px rgba(37,99,235,0.15); }
.btn { padding:10px 20px; border-radius:6px; border:none; cursor:pointer; font-size:14px; font-weight:600; }
.btn-approve { background:#16a34a; color:#fff; }
.btn-approve:hover { background:#15803d; }
.btn-reject { background:#ef4444; color:#fff; }
.btn-reject:hover { background:#dc2626; }
.btn-back { background:#e5e7eb; color:#374151; text-decoration:none; }
.btn-back:hover { background:#d1d5db; }
.dup-card { background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:12px; margin-bottom:8px; font-size:13px; }
</style>

<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
    <a href="{{ route('admin.drug-submissions.index') }}" class="btn btn-back">← Back</a>
    <h1 style="font-size:22px;font-weight:700;margin:0;">Review Submission #{{ $submission->id }}</h1>
    <span class="badge badge-{{ $submission->status }}">{{ ucfirst($submission->status) }}</span>
</div>

{{-- Submission Details --}}
<div class="detail-card">
    <h3 style="margin:0 0 14px;font-size:16px;font-weight:700;">Submission Details</h3>

    <div class="detail-row">
        <div class="detail-label">Type</div>
        <div class="detail-value">{{ ucfirst($submission->type) }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Organisation</div>
        <div class="detail-value">{{ $submission->organisation->name ?? '—' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Submitted By</div>
        <div class="detail-value">{{ $submission->submittedBy->name ?? '—' }} ({{ $submission->submittedBy->phone ?? '' }})</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Date</div>
        <div class="detail-value">{{ $submission->created_at->format('d M Y, h:i A') }}</div>
    </div>

    @if($submission->brand_name)
    <div class="detail-row">
        <div class="detail-label">Brand Name</div>
        <div class="detail-value" style="font-weight:600;">{{ $submission->brand_name }}</div>
    </div>
    @endif

    @if($submission->manufacturer)
    <div class="detail-row">
        <div class="detail-label">Manufacturer</div>
        <div class="detail-value">{{ $submission->manufacturer }}</div>
    </div>
    @endif

    <div class="detail-row">
        <div class="detail-label">Generic</div>
        <div class="detail-value">
            @if($submission->generic)
                <span style="color:#16a34a;">{{ $submission->generic->name }} (KB #{{ $submission->drug_generic_id }})</span>
            @elseif($submission->submitted_generic_name)
                <span style="color:#f59e0b;">{{ $submission->submitted_generic_name }} — NEW (not in KB)</span>
            @elseif($submission->generic_name)
                <span style="color:#6b7280;">{{ $submission->generic_name }}</span>
            @else
                —
            @endif
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Form</div>
        <div class="detail-value">{{ $submission->form ?? '—' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Strength</div>
        <div class="detail-value">{{ $submission->strength_value }} {{ $submission->strength_unit }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Pack Size</div>
        <div class="detail-value">{{ $submission->pack_size }} {{ $submission->pack_unit }}</div>
    </div>
</div>

{{-- Duplicate Check --}}
@if($duplicateBrands->count())
<div style="max-width:800px;margin-bottom:20px;">
    <h3 class="section-title" style="color:#f59e0b;">⚠ Possible Duplicates in KB</h3>
    @foreach($duplicateBrands as $dup)
    <div class="dup-card">
        <strong>{{ $dup->brand_name }}</strong> — {{ $dup->generic->name ?? 'No generic' }}
        <span style="color:#6b7280;">({{ $dup->form }}, {{ $dup->strength_value }}{{ $dup->strength_unit }}, {{ $dup->pack_size }} {{ $dup->pack_unit }})</span>
    </div>
    @endforeach
</div>
@endif

{{-- Action Forms --}}
@if($submission->isPending())
<div class="action-card">
    <h3 style="margin:0 0 16px;font-size:16px;font-weight:700;">Take Action</h3>

    <form method="POST" action="{{ route('admin.drug-submissions.approve', $submission) }}" style="margin-bottom:20px;">
        @csrf

        {{-- If no generic linked, let admin pick or create one --}}
        @if(!$submission->drug_generic_id)
        <div style="background:#fefce8;border:1px solid #fde68a;border-radius:8px;padding:14px;margin-bottom:14px;">
            <div style="font-weight:600;font-size:13px;color:#92400e;margin-bottom:8px;">No generic linked — select or create one:</div>

            @if($possibleGenerics->count())
            <div class="form-group">
                <label>Match to existing generic</label>
                <select name="generic_id">
                    <option value="">— Create new generic —</option>
                    @foreach($possibleGenerics as $g)
                    <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->drug_class }})</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="form-group">
                <label>Or create new generic name</label>
                <input name="new_generic_name" value="{{ $submission->submitted_generic_name }}" placeholder="Generic name">
            </div>

            <div class="form-group">
                <label>Drug Class</label>
                <input name="drug_class" value="{{ $submission->drug_class }}" placeholder="e.g., Antibiotic, NSAID, Vaccine">
            </div>
        </div>
        @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="form-group">
                <label>Brand Name (editable)</label>
                <input name="brand_name" value="{{ $submission->brand_name }}">
            </div>
            <div class="form-group">
                <label>Manufacturer</label>
                <input name="manufacturer" value="{{ $submission->manufacturer }}">
            </div>
        </div>

        <div class="form-group">
            <label>Admin Notes (optional)</label>
            <textarea name="review_notes" rows="2" placeholder="Optional notes..."></textarea>
        </div>

        <button type="submit" class="btn btn-approve">✓ Approve & Add to KB</button>
    </form>

    <hr style="border:none;border-top:1px solid #e5e7eb;margin:16px 0;">

    <form method="POST" action="{{ route('admin.drug-submissions.reject', $submission) }}">
        @csrf
        <div class="form-group">
            <label>Rejection Reason</label>
            <textarea name="review_notes" rows="2" placeholder="Why is this being rejected?"></textarea>
        </div>
        <button type="submit" class="btn btn-reject">✕ Reject</button>
    </form>
</div>
@else
{{-- Already reviewed --}}
<div class="detail-card">
    <h3 style="margin:0 0 14px;font-size:16px;font-weight:700;">Review Result</h3>
    <div class="detail-row">
        <div class="detail-label">Status</div>
        <div class="detail-value"><span class="badge badge-{{ $submission->status }}">{{ ucfirst($submission->status) }}</span></div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Reviewed By</div>
        <div class="detail-value">{{ $submission->reviewedBy->name ?? '—' }}</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Reviewed At</div>
        <div class="detail-value">{{ $submission->reviewed_at?->format('d M Y, h:i A') ?? '—' }}</div>
    </div>
    @if($submission->review_notes)
    <div class="detail-row">
        <div class="detail-label">Notes</div>
        <div class="detail-value">{{ $submission->review_notes }}</div>
    </div>
    @endif
    @if($submission->created_brand_id)
    <div class="detail-row">
        <div class="detail-label">Created Brand</div>
        <div class="detail-value"><a href="/admin/drugs/{{ $submission->created_generic_id ?? $submission->drug_generic_id }}/edit" style="color:#2563eb;">View in KB →</a></div>
    </div>
    @endif
</div>
@endif

@endsection
