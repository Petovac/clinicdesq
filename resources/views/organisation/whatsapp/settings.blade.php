@extends('organisation.layout')

@section('content')
<style>
.wa-card { background:#fff; border-radius:10px; padding:24px; border:1px solid #e5e7eb; margin-bottom:20px; }
.wa-title { font-size:18px; font-weight:700; margin-bottom:16px; }
.form-group { margin-bottom:16px; }
.form-group label { display:block; font-size:13px; font-weight:600; margin-bottom:4px; color:#374151; }
.form-group input, .form-group select { width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:6px; font-size:14px; }
.form-group input:focus, .form-group select:focus { outline:none; border-color:#25D366; box-shadow:0 0 0 2px rgba(37,211,102,0.15); }
.form-group .help { font-size:11px; color:#6b7280; margin-top:3px; }
.btn-wa { background:#25D366; color:#fff; padding:10px 24px; border:none; border-radius:6px; font-weight:600; font-size:14px; cursor:pointer; }
.btn-wa:hover { background:#1da851; }
.toggle-group { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.toggle-group label { margin:0; font-size:14px; font-weight:500; color:#374151; }
.toggle { position:relative; width:44px; height:24px; }
.toggle input { opacity:0; width:0; height:0; }
.toggle .slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#d1d5db; border-radius:12px; transition:0.3s; }
.toggle .slider:before { content:''; position:absolute; width:18px; height:18px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:0.3s; }
.toggle input:checked + .slider { background:#25D366; }
.toggle input:checked + .slider:before { transform:translateX(20px); }
.stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px; }
.stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:14px; text-align:center; }
.stat-value { font-size:22px; font-weight:700; color:#111827; }
.stat-label { font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; }
.msg-table { width:100%; border-collapse:collapse; }
.msg-table th { background:#f9fafb; padding:8px 10px; text-align:left; font-size:12px; font-weight:600; color:#374151; border-bottom:1px solid #e5e7eb; }
.msg-table td { padding:8px 10px; border-top:1px solid #f1f5f9; font-size:13px; }
.status-badge { padding:2px 8px; border-radius:10px; font-size:10px; font-weight:600; }
.status-sent { background:#dbeafe; color:#1d4ed8; }
.status-delivered { background:#dcfce7; color:#166534; }
.status-read { background:#f0fdf4; color:#15803d; }
.status-failed { background:#fee2e2; color:#991b1b; }
.status-queued { background:#fef3c7; color:#92400e; }
.setup-steps { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:16px; margin-bottom:20px; }
.setup-steps h4 { margin:0 0 10px; color:#166534; font-size:14px; }
.setup-steps ol { margin:0; padding-left:18px; font-size:13px; color:#374151; line-height:2; }
</style>

<h1 style="font-size:22px;font-weight:700;margin-bottom:20px;">
    <span style="color:#25D366;">WhatsApp</span> Integration
</h1>

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #bbf7d0;padding:12px;border-radius:6px;margin-bottom:15px;color:#166534;">
    {{ session('success') }}
</div>
@endif

{{-- Setup Guide --}}
<div class="setup-steps">
    <h4>Setup Guide — MSG91 WhatsApp</h4>
    <ol>
        <li>Create an account at <strong>msg91.com</strong></li>
        <li>Go to <strong>WhatsApp</strong> section → Add your business WhatsApp number</li>
        <li>Get your <strong>Auth Key</strong> from Settings → API Keys</li>
        <li>Copy the <strong>Integrated Number ID</strong> from WhatsApp → Numbers</li>
        <li>Create message templates (Meta approval required) for: case_sheet, prescription, bill, lab_report</li>
        <li>Paste the credentials below and activate</li>
    </ol>
</div>

{{-- Stats --}}
@if($stats['total'] > 0)
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Sent</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color:#2563eb;">{{ $stats['sent'] }}</div>
        <div class="stat-label">Sent</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color:#16a34a;">{{ $stats['delivered'] }}</div>
        <div class="stat-label">Delivered</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color:#ef4444;">{{ $stats['failed'] }}</div>
        <div class="stat-label">Failed</div>
    </div>
</div>
@endif

{{-- Config Form --}}
<div class="wa-card">
    <div class="wa-title">MSG91 Configuration</div>

    <form method="POST" action="{{ url('/organisation/whatsapp/settings') }}">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label>MSG91 Auth Key</label>
                <input type="password" name="api_key" value="{{ $config->exists ? '••••••••' : '' }}" placeholder="Paste your MSG91 Auth Key" required>
                <div class="help">Settings → API Keys in MSG91 dashboard</div>
            </div>
            <div class="form-group">
                <label>Integrated Number ID</label>
                <input name="integrated_number_id" value="{{ $config->integrated_number_id ?? '' }}" placeholder="e.g., 2348XXXXXX" required>
                <div class="help">WhatsApp → Numbers → Your number's ID</div>
            </div>
        </div>

        <div class="form-group" style="max-width:300px;">
            <label>WhatsApp Number (display)</label>
            <input name="whatsapp_number" value="{{ $config->whatsapp_number ?? '' }}" placeholder="+91 98765 43210" required>
            <div class="help">Your business WhatsApp number</div>
        </div>

        <hr style="border:none;border-top:1px solid #e5e7eb;margin:20px 0;">

        <h4 style="font-size:14px;font-weight:600;margin-bottom:14px;">Auto-send Settings</h4>
        <p style="font-size:12px;color:#6b7280;margin-bottom:14px;">Choose which documents to auto-send via WhatsApp when ready</p>

        <div class="toggle-group">
            <div class="toggle">
                <input type="checkbox" name="send_case_sheet" value="1" id="t1" {{ ($config->send_case_sheet ?? true) ? 'checked' : '' }}>
                <label class="slider" for="t1"></label>
            </div>
            <label for="t1">Case Sheet</label>
        </div>

        <div class="toggle-group">
            <div class="toggle">
                <input type="checkbox" name="send_prescription" value="1" id="t2" {{ ($config->send_prescription ?? true) ? 'checked' : '' }}>
                <label class="slider" for="t2"></label>
            </div>
            <label for="t2">Prescription</label>
        </div>

        <div class="toggle-group">
            <div class="toggle">
                <input type="checkbox" name="send_bill" value="1" id="t3" {{ ($config->send_bill ?? true) ? 'checked' : '' }}>
                <label class="slider" for="t3"></label>
            </div>
            <label for="t3">Invoice / Bill</label>
        </div>

        <div class="toggle-group">
            <div class="toggle">
                <input type="checkbox" name="send_lab_report" value="1" id="t4" {{ ($config->send_lab_report ?? true) ? 'checked' : '' }}>
                <label class="slider" for="t4"></label>
            </div>
            <label for="t4">Lab / Diagnostic Reports</label>
        </div>

        <div class="toggle-group" style="margin-top:10px;">
            <div class="toggle">
                <input type="checkbox" name="is_active" value="1" id="t5" {{ ($config->is_active ?? false) ? 'checked' : '' }}>
                <label class="slider" for="t5"></label>
            </div>
            <label for="t5" style="font-weight:700;">Enable WhatsApp Sending</label>
        </div>

        <button type="submit" class="btn-wa" style="margin-top:16px;">Save Configuration</button>
    </form>
</div>

{{-- Recent Messages --}}
@if($recentMessages->count())
<div class="wa-card">
    <div class="wa-title">Recent Messages</div>
    <table class="msg-table">
        <thead>
            <tr>
                <th>To</th>
                <th>Type</th>
                <th>Template</th>
                <th>Status</th>
                <th>Sent</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentMessages as $msg)
            <tr>
                <td>
                    <div style="font-weight:600;">{{ $msg->recipient_name }}</div>
                    <div style="font-size:11px;color:#6b7280;">{{ $msg->recipient_phone }}</div>
                </td>
                <td>{{ str_replace('_', ' ', ucfirst($msg->message_type)) }}</td>
                <td style="font-size:12px;color:#6b7280;">{{ $msg->template_name }}</td>
                <td><span class="status-badge status-{{ $msg->status }}">{{ ucfirst($msg->status) }}</span></td>
                <td style="font-size:12px;color:#6b7280;">{{ $msg->created_at->diffForHumans() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Template Guide --}}
<div class="wa-card">
    <div class="wa-title">Message Templates to Create in MSG91</div>
    <p style="font-size:12px;color:#6b7280;margin-bottom:14px;">Create these templates in MSG91 dashboard → WhatsApp → Templates. Meta will approve them in 24-48 hours.</p>

    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">
        <div style="font-weight:700;font-size:13px;">clinicdesq_case_sheet</div>
        <div style="font-size:12px;color:#374151;margin-top:4px;">
            Header: DOCUMENT<br>
            Body: "Hi {{1}}, here is the case sheet for {{2}} from {{3}} on {{4}}. Please review the attached report."
        </div>
    </div>

    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">
        <div style="font-weight:700;font-size:13px;">clinicdesq_prescription</div>
        <div style="font-size:12px;color:#374151;margin-top:4px;">
            Header: DOCUMENT<br>
            Body: "Hi {{1}}, here is the prescription for {{2}} by Dr. {{3}} from {{4}}. Please follow the medication schedule as prescribed."
        </div>
    </div>

    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">
        <div style="font-weight:700;font-size:13px;">clinicdesq_bill</div>
        <div style="font-size:12px;color:#374151;margin-top:4px;">
            Header: DOCUMENT<br>
            Body: "Hi {{1}}, here is the invoice for {{2}}'s visit. Amount: {{3}}. Clinic: {{4}}. Thank you for choosing us."
        </div>
    </div>

    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;">
        <div style="font-weight:700;font-size:13px;">clinicdesq_lab_report</div>
        <div style="font-size:12px;color:#374151;margin-top:4px;">
            Header: DOCUMENT<br>
            Body: "Hi {{1}}, the {{3}} report for {{2}} from {{4}} is ready. Please find the attached report."
        </div>
    </div>
</div>

@endsection
