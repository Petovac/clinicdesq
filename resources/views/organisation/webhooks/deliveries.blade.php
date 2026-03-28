@extends('organisation.layout')

@section('content')
<style>
.card { background:#fff; border-radius:10px; padding:24px; border:1px solid #e5e7eb; margin-bottom:20px; }
.table { width:100%; border-collapse:collapse; }
.table th { background:#f9fafb; padding:8px 10px; text-align:left; font-size:12px; font-weight:600; color:#374151; border-bottom:1px solid #e5e7eb; }
.table td { padding:8px 10px; border-top:1px solid #f1f5f9; font-size:13px; }
.badge { padding:2px 8px; border-radius:10px; font-size:10px; font-weight:600; }
.badge-success { background:#dcfce7; color:#166534; }
.badge-failed { background:#fee2e2; color:#991b1b; }
.badge-pending { background:#fef3c7; color:#92400e; }
.btn-back { background:#f3f4f6; color:#374151; padding:8px 16px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600; border:1px solid #d1d5db; }
</style>

<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
    <a href="{{ route('organisation.webhooks.index') }}" class="btn-back">← Back</a>
    <h1 style="font-size:20px;font-weight:700;margin:0;">
        Delivery Log: {{ $endpoint->label ?: 'Webhook #' . $endpoint->id }}
    </h1>
</div>

<div style="font-family:monospace;font-size:12px;color:#6b7280;margin-bottom:16px;background:#f9fafb;padding:8px 12px;border-radius:6px;">
    {{ $endpoint->url }}
</div>

<div class="card">
@if($deliveries->count())
<table class="table">
    <thead>
        <tr>
            <th>Event</th>
            <th>Status</th>
            <th>HTTP</th>
            <th>Error</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody>
        @foreach($deliveries as $d)
        <tr>
            <td style="font-weight:600;">{{ $d->event }}</td>
            <td><span class="badge badge-{{ $d->status }}">{{ ucfirst($d->status) }}</span></td>
            <td>{{ $d->http_status ?? '—' }}</td>
            <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:11px;color:#6b7280;">
                {{ $d->error_message ?? '—' }}
            </td>
            <td style="font-size:12px;color:#6b7280;">{{ $d->created_at->diffForHumans() }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:14px;">{{ $deliveries->links() }}</div>
@else
<div style="text-align:center;padding:40px;color:#9ca3af;">No deliveries yet.</div>
@endif
</div>
@endsection
