@extends('organisation.layout')

@section('content')
<style>
.wh-card { background:#fff; border-radius:10px; padding:24px; border:1px solid #e5e7eb; margin-bottom:20px; }
.wh-title { font-size:18px; font-weight:700; margin-bottom:6px; }
.wh-subtitle { font-size:13px; color:#6b7280; margin-bottom:16px; }
.form-row { display:grid; grid-template-columns:2fr 1fr; gap:14px; margin-bottom:14px; }
.form-group { margin-bottom:14px; }
.form-group label { display:block; font-size:13px; font-weight:600; margin-bottom:4px; color:#374151; }
.form-group input { width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:6px; font-size:14px; }
.form-group input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 2px rgba(37,99,235,0.15); }
.event-grid { display:grid; grid-template-columns:1fr 1fr; gap:6px; }
.event-checkbox { display:flex; align-items:center; gap:8px; padding:6px 10px; background:#f9fafb; border-radius:6px; font-size:13px; cursor:pointer; }
.event-checkbox:hover { background:#f3f4f6; }
.event-checkbox input { margin:0; }
.btn { padding:10px 20px; border-radius:6px; border:none; cursor:pointer; font-size:14px; font-weight:600; }
.btn-primary { background:#2563eb; color:#fff; }
.btn-primary:hover { background:#1d4ed8; }
.btn-sm { padding:5px 12px; font-size:12px; border-radius:5px; border:none; cursor:pointer; font-weight:600; }
.btn-danger { background:#fee2e2; color:#dc2626; }
.btn-success { background:#dcfce7; color:#16a34a; }
.btn-outline { background:#f3f4f6; color:#374151; border:1px solid #d1d5db; }
.endpoint-card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:18px; margin-bottom:12px; }
.endpoint-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; }
.endpoint-url { font-family:monospace; font-size:13px; background:#f3f4f6; padding:6px 10px; border-radius:4px; word-break:break-all; }
.endpoint-events { display:flex; flex-wrap:wrap; gap:4px; margin-top:8px; }
.event-tag { background:#dbeafe; color:#1d4ed8; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:600; }
.secret-box { font-family:monospace; font-size:12px; background:#fefce8; border:1px solid #fde68a; padding:8px 10px; border-radius:4px; margin-top:8px; word-break:break-all; }
.status-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:6px; }
.status-dot.active { background:#16a34a; }
.status-dot.inactive { background:#ef4444; }
.status-dot.warning { background:#f59e0b; }
.info-box { background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:16px; margin-bottom:20px; }
.info-box h4 { margin:0 0 8px; color:#1d4ed8; font-size:14px; }
.info-box p { margin:0; font-size:13px; color:#374151; line-height:1.7; }
.info-box code { background:#dbeafe; padding:1px 6px; border-radius:3px; font-size:12px; }
</style>

<h1 style="font-size:22px;font-weight:700;margin-bottom:20px;">Webhooks & API Integration</h1>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;padding:12px;border-radius:6px;margin-bottom:15px;color:#166534;">
    {{ session('success') }}
</div>
@endif

{{-- Info Box --}}
<div class="info-box">
    <h4>Push Data to Your App or Website</h4>
    <p>
        Register a webhook endpoint to automatically receive real-time data when events happen in ClinicDesq.
        Each request includes an HMAC-SHA256 signature in the <code>X-Webhook-Signature</code> header so you can verify authenticity.
        Data is sent as JSON POST to your endpoint URL.
    </p>
</div>

{{-- Existing Endpoints --}}
@if($endpoints->count())
<div class="wh-card">
    <div class="wh-title">Active Endpoints</div>

    @foreach($endpoints as $ep)
    <div class="endpoint-card">
        <div class="endpoint-header">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <span class="status-dot {{ $ep->is_active ? ($ep->failure_count >= 5 ? 'warning' : 'active') : 'inactive' }}"></span>
                    <strong>{{ $ep->label ?: 'Webhook #' . $ep->id }}</strong>
                    @if($ep->failure_count >= 5)
                        <span style="font-size:11px;color:#f59e0b;font-weight:600;">{{ $ep->failure_count }} failures</span>
                    @endif
                </div>
                <div class="endpoint-url">{{ $ep->url }}</div>
                <div class="endpoint-events">
                    @foreach($ep->events as $evt)
                        <span class="event-tag">{{ $evt }}</span>
                    @endforeach
                </div>
                @if($ep->wasRecentlyCreated)
                <div class="secret-box">
                    <strong>Signing Secret (copy now!):</strong> {{ $ep->secret }}
                </div>
                @endif
                @if($ep->last_triggered_at)
                <div style="font-size:11px;color:#6b7280;margin-top:6px;">
                    Last triggered: {{ $ep->last_triggered_at->diffForHumans() }}
                    · {{ $ep->deliveries_count }} deliveries
                </div>
                @endif
            </div>
            <div style="display:flex;gap:6px;">
                <form method="POST" action="{{ route('organisation.webhooks.test', $ep) }}" style="display:inline;">
                    @csrf
                    <button class="btn-sm btn-outline" title="Send test ping">Test</button>
                </form>
                <form method="POST" action="{{ route('organisation.webhooks.toggle', $ep) }}" style="display:inline;">
                    @csrf
                    <button class="btn-sm {{ $ep->is_active ? 'btn-danger' : 'btn-success' }}">
                        {{ $ep->is_active ? 'Disable' : 'Enable' }}
                    </button>
                </form>
                <a href="{{ route('organisation.webhooks.deliveries', $ep) }}" class="btn-sm btn-outline">Logs</a>
                <form method="POST" action="{{ route('organisation.webhooks.destroy', $ep) }}" style="display:inline;" onsubmit="return confirm('Delete this webhook?');">
                    @csrf @method('DELETE')
                    <button class="btn-sm btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Add New Endpoint --}}
<div class="wh-card">
    <div class="wh-title">Add Webhook Endpoint</div>
    <div class="wh-subtitle">Register a URL to receive event data. We'll POST JSON payloads with an HMAC signature.</div>

    <form method="POST" action="{{ route('organisation.webhooks.store') }}">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label>Endpoint URL</label>
                <input name="url" type="url" placeholder="https://your-app.com/api/clinicdesq/webhook" required>
            </div>
            <div class="form-group">
                <label>Label (optional)</label>
                <input name="label" placeholder="e.g., My Brand App, CRM">
            </div>
        </div>

        <div class="form-group">
            <label>Subscribe to Events</label>
            <div class="event-grid">
                <label class="event-checkbox" style="background:#dbeafe;">
                    <input type="checkbox" name="events[]" value="*" onchange="toggleAllEvents(this)">
                    <strong>All Events</strong>
                </label>
                @foreach($availableEvents as $key => $desc)
                <label class="event-checkbox">
                    <input type="checkbox" name="events[]" value="{{ $key }}" class="event-cb">
                    {{ $desc }}
                </label>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Create Webhook</button>
    </form>
</div>

{{-- API Documentation --}}
<div class="wh-card">
    <div class="wh-title">Webhook Payload Format</div>
    <div class="wh-subtitle">Every webhook POST includes these headers and this JSON body structure:</div>

    <div style="background:#1e293b;color:#e2e8f0;padding:16px;border-radius:8px;font-family:monospace;font-size:12px;line-height:1.8;overflow-x:auto;">
        <div style="color:#94a3b8;">// Headers</div>
        Content-Type: application/json<br>
        X-Webhook-Signature: &lt;HMAC-SHA256 of body using your secret&gt;<br>
        X-Webhook-Event: case_sheet.saved<br>
        X-Webhook-Id: &lt;unique UUID&gt;<br>
        <br>
        <div style="color:#94a3b8;">// Body (JSON)</div>
        {<br>
        &nbsp;&nbsp;"event": "case_sheet.saved",<br>
        &nbsp;&nbsp;"timestamp": "2026-03-22T10:30:00+05:30",<br>
        &nbsp;&nbsp;"webhook_id": "550e8400-e29b-41d4-a716-...",<br>
        &nbsp;&nbsp;"organisation_id": 1,<br>
        &nbsp;&nbsp;"data": {<br>
        &nbsp;&nbsp;&nbsp;&nbsp;"appointment_id": 42,<br>
        &nbsp;&nbsp;&nbsp;&nbsp;"pet": { "id": 15, "name": "Bruno", "species": "dog", ... },<br>
        &nbsp;&nbsp;&nbsp;&nbsp;"pet_parent": { "id": 8, "name": "Raj", "phone": "9876543210" },<br>
        &nbsp;&nbsp;&nbsp;&nbsp;"case_sheet": { "presenting_complaint": "...", "diagnosis": "...", ... },<br>
        &nbsp;&nbsp;&nbsp;&nbsp;"vet": { "id": 3, "name": "Dr. Sharma", ... },<br>
        &nbsp;&nbsp;&nbsp;&nbsp;"clinic": { "id": 1, "name": "PawCare Clinic", "city": "Bangalore" }<br>
        &nbsp;&nbsp;}<br>
        }
    </div>

    <div style="margin-top:14px;font-size:13px;color:#374151;">
        <strong>Verify signature (PHP example):</strong>
    </div>
    <div style="background:#1e293b;color:#e2e8f0;padding:12px;border-radius:8px;font-family:monospace;font-size:11px;margin-top:6px;overflow-x:auto;">
        $signature = hash_hmac('sha256', $requestBody, $yourSecret);<br>
        $valid = hash_equals($signature, $request->header('X-Webhook-Signature'));
    </div>
</div>

<script>
function toggleAllEvents(cb) {
    document.querySelectorAll('.event-cb').forEach(c => { c.checked = cb.checked; });
}
</script>

@endsection
