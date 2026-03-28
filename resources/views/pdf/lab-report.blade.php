@extends('pdf.layout')

@section('content')

<div class="doc-badge">{{ strtoupper($report->type ?? 'LAB') }} REPORT</div>

{{-- Patient Info --}}
<div class="info-box">
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell"><span class="info-label">Patient:</span> {{ $pet->name ?? '—' }} ({{ ucfirst($pet->species ?? '') }})</div>
            <div class="info-cell"><span class="info-label">Date:</span> {{ $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('d M Y') : $report->created_at->format('d M Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><span class="info-label">Parent:</span> {{ $parent->name ?? '—' }}</div>
            <div class="info-cell"><span class="info-label">Lab/Center:</span> {{ $report->lab_or_center ?? '—' }}</div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-title">{{ $report->title }}</div>
    @if($report->summary)
        <div class="section-content">{!! nl2br(e($report->summary)) !!}</div>
    @endif
</div>

@if($files->count())
<div class="section">
    <div class="section-title">Attached Files</div>
    @foreach($files as $file)
    <div style="padding:4px 0;font-size:11px;">
        {{ $file->display_name ?? $file->original_filename }}
        @if($file->ai_summary)
            <div style="font-size:10px;color:#6b7280;margin-top:2px;">{{ $file->ai_summary }}</div>
        @endif
    </div>
    @endforeach
</div>
@endif

<div class="footer">
    <div class="footer-left">
        {{ $report->created_at->format('d M Y, h:i A') }}
    </div>
    <div class="footer-right">
        <div style="font-size:11px;font-weight:bold;">{{ $clinic->name ?? '' }}</div>
    </div>
</div>

@endsection
