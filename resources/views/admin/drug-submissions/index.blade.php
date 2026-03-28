@extends('admin.layout')

@section('content')
<style>
.tabs { display:flex; gap:8px; margin-bottom:20px; }
.tab { padding:8px 16px; border-radius:6px; text-decoration:none; font-size:14px; font-weight:500; color:#374151; background:#f3f4f6; border:1px solid #e5e7eb; }
.tab:hover { background:#e5e7eb; }
.tab.active { background:#2563eb; color:#fff; border-color:#2563eb; }
.tab .count { background:rgba(255,255,255,0.2); padding:1px 6px; border-radius:8px; font-size:11px; margin-left:4px; }
.tab.active .count { background:rgba(255,255,255,0.3); }
.table { width:100%; border-collapse:collapse; }
.table th { background:#f9fafb; padding:10px 12px; text-align:left; font-size:13px; font-weight:600; color:#374151; border-bottom:1px solid #e5e7eb; }
.table td { padding:10px 12px; border-top:1px solid #f1f5f9; font-size:14px; }
.table tr:hover { background:#f9fafb; }
.badge { padding:2px 10px; border-radius:12px; font-size:11px; font-weight:600; }
.badge-pending { background:#fef3c7; color:#92400e; }
.badge-approved { background:#dcfce7; color:#166534; }
.badge-rejected { background:#fee2e2; color:#991b1b; }
.btn-sm { padding:5px 12px; border-radius:5px; border:none; cursor:pointer; font-size:12px; font-weight:600; text-decoration:none; }
.btn-view { background:#dbeafe; color:#1d4ed8; }
.btn-view:hover { background:#bfdbfe; }
.empty-state { text-align:center; padding:40px; color:#9ca3af; }
</style>

<h1 class="page-title">Drug Submissions</h1>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;padding:12px;border-radius:6px;margin-bottom:15px;color:#166534;">
✓ {{ session('success') }}
</div>
@endif

<div class="tabs">
    <a href="?status=pending" class="tab {{ $status === 'pending' ? 'active' : '' }}">
        Pending <span class="count">{{ $counts['pending'] }}</span>
    </a>
    <a href="?status=approved" class="tab {{ $status === 'approved' ? 'active' : '' }}">
        Approved <span class="count">{{ $counts['approved'] }}</span>
    </a>
    <a href="?status=rejected" class="tab {{ $status === 'rejected' ? 'active' : '' }}">
        Rejected <span class="count">{{ $counts['rejected'] }}</span>
    </a>
    <a href="?status=all" class="tab {{ $status === 'all' ? 'active' : '' }}">
        All
    </a>
</div>

<div class="card">
@if($submissions->count())
<table class="table">
    <thead>
        <tr>
            <th>Type</th>
            <th>Brand Name</th>
            <th>Generic</th>
            <th>Organisation</th>
            <th>Submitted By</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($submissions as $sub)
        <tr>
            <td><span class="badge" style="background:#ede9fe;color:#6d28d9;">{{ ucfirst($sub->type) }}</span></td>
            <td style="font-weight:600;">{{ $sub->brand_name ?: '—' }}</td>
            <td>
                @if($sub->generic)
                    {{ $sub->generic->name }}
                @elseif($sub->submitted_generic_name)
                    <span style="color:#f59e0b;">{{ $sub->submitted_generic_name }} (new)</span>
                @else
                    —
                @endif
            </td>
            <td>{{ $sub->organisation->name ?? '—' }}</td>
            <td>{{ $sub->submittedBy->name ?? '—' }}</td>
            <td style="color:#6b7280;font-size:13px;">{{ $sub->created_at->format('d M Y') }}</td>
            <td>
                <span class="badge badge-{{ $sub->status }}">{{ ucfirst($sub->status) }}</span>
            </td>
            <td>
                <a href="{{ route('admin.drug-submissions.show', $sub) }}" class="btn-sm btn-view">Review</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:16px;">
    {{ $submissions->appends(['status' => $status])->links() }}
</div>
@else
<div class="empty-state">No {{ $status !== 'all' ? $status : '' }} submissions found.</div>
@endif
</div>
@endsection
