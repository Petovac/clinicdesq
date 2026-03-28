@extends('layouts.lab')

@section('content')
<h1 class="page-title">Dashboard</h1>

<div style="display:flex;gap:16px;margin-bottom:24px;">
    @foreach(['pending' => ['Pending', '#f59e0b'], 'processing' => ['Processing', '#2563eb'], 'uploaded' => ['Submitted', '#065f46'], 'completed' => ['Completed', '#166534'], 'retest' => ['Retest', '#dc2626']] as $key => [$label, $color])
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px;flex:1;text-align:center;">
        <div style="font-size:28px;font-weight:700;color:{{ $color }};">{{ $counts[$key] }}</div>
        <div style="font-size:12px;color:var(--text-muted);font-weight:600;">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- Connection Requests from Organisations --}}
@if(isset($pendingRequests) && $pendingRequests->count())
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px;margin-bottom:16px;">
    <h3 style="font-size:15px;font-weight:700;color:#92400e;margin:0 0 10px;">🔔 Connection Requests ({{ $pendingRequests->count() }})</h3>
    @foreach($pendingRequests as $req)
    <div style="display:flex;justify-content:space-between;align-items:center;background:#fff;border:1px solid #fde68a;border-radius:8px;padding:12px;margin-bottom:8px;">
        <div>
            <div style="font-weight:700;font-size:14px;color:#111;">{{ $req->org_name }}</div>
            <div style="font-size:12px;color:#6b7280;">{{ $req->primary_phone ?? '' }} {{ $req->primary_email ? '· '.$req->primary_email : '' }}</div>
            <div style="font-size:11px;color:#9ca3af;">Requested {{ \Carbon\Carbon::parse($req->requested_at)->diffForHumans() }}</div>
        </div>
        <div style="display:flex;gap:6px;">
            <form method="POST" action="{{ route('lab.accept-org') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="organisation_id" value="{{ $req->organisation_id }}">
                <button type="submit" style="background:#16a34a;color:#fff;border:none;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">Accept</button>
            </form>
            <form method="POST" action="{{ route('lab.reject-org') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="organisation_id" value="{{ $req->organisation_id }}">
                <button type="submit" style="background:#ef4444;color:#fff;border:none;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">Decline</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

<h2 style="font-size:16px;font-weight:700;margin-bottom:14px;">Active Orders</h2>
@if($recentOrders->isEmpty())
    <div class="card" style="text-align:center;padding:40px;color:var(--text-muted);">No active orders.</div>
@else
    <div class="card" style="padding:0;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f9fafb;border-bottom:1px solid var(--border);">
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Order #</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Pet</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Clinic</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Tests</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Status</th>
                    <th style="padding:10px 14px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:10px 14px;font-weight:600;color:var(--primary);">{{ $order->order_number }}</td>
                    <td style="padding:10px 14px;">{{ $order->pet->name ?? '—' }}</td>
                    <td style="padding:10px 14px;">{{ $order->clinic->name ?? '—' }}</td>
                    <td style="padding:10px 14px;">
                        @foreach($order->tests as $t)
                            <span style="background:#eff6ff;color:#1e40af;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;margin:1px;">{{ $t->test_name }}</span>
                        @endforeach
                    </td>
                    <td style="padding:10px 14px;"><span class="status-badge status-{{ $order->status }}">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span></td>
                    <td style="padding:10px 14px;"><a href="{{ route('lab.orders.show', $order) }}" class="btn btn-outline btn-sm">View</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@if(isset($allDirectoryTests))
<h2 style="font-size:16px;font-weight:700;margin-top:28px;margin-bottom:14px;">Manage In-House Tests</h2>
<p style="font-size:13px;color:var(--text-muted);margin-bottom:12px;">Select which tests your clinic offers. Set pricing and mark availability.</p>

{{-- Search/Filter --}}
<div style="display:flex;gap:10px;margin-bottom:14px;">
    <input type="text" id="test-search" placeholder="Search tests..." style="flex:1;padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-size:13px;" oninput="filterTests()">
    <select id="test-cat-filter" style="padding:10px;border:1px solid var(--border);border-radius:8px;font-size:13px;" onchange="filterTests()">
        <option value="">All Categories</option>
        @php $cats = $allDirectoryTests->pluck('category')->unique()->sort(); @endphp
        @foreach($cats as $cat)<option value="{{ $cat }}">{{ ucfirst(str_replace('_',' ',$cat)) }}</option>@endforeach
    </select>
    <select id="test-status-filter" style="padding:10px;border:1px solid var(--border);border-radius:8px;font-size:13px;" onchange="filterTests()">
        <option value="">All Tests</option>
        <option value="enabled">Enabled Only</option>
        <option value="disabled">Not Enabled</option>
    </select>
</div>

<div style="display:flex;gap:12px;margin-bottom:14px;">
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 14px;font-size:13px;color:#1e40af;flex:1;">
        <strong>{{ $clinicTests->where('is_available', true)->count() }}</strong> tests enabled out of <strong>{{ $allDirectoryTests->count() }}</strong> in directory
    </div>
    <button onclick="document.getElementById('ih-custom-modal').style.display='flex'" style="background:#7c3aed;color:#fff;border:none;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;">+ Add Custom Test</button>
</div>

<div class="card" style="padding:0;max-height:600px;overflow-y:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead style="position:sticky;top:0;background:#f9fafb;z-index:1;">
            <tr style="border-bottom:1px solid var(--border);">
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Test</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Collection</th>
                <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Price (₹)</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Parameters</th>
                <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Status</th>
                <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allDirectoryTests as $test)
            @php
                $ct = $clinicTests[$test->code] ?? null;
                $isEnabled = $ct !== null;
                $isAvail = $isEnabled && $ct->is_available;
                $ctParams = $ct && $ct->parameters ? json_decode($ct->parameters, true) : [];
                $defParams = $test->default_parameters ? json_decode($test->default_parameters, true) : [];
            @endphp
            <tr class="test-row" data-name="{{ strtolower($test->name) }}" data-code="{{ strtolower($test->code) }}" data-cat="{{ $test->category }}" data-enabled="{{ $isEnabled ? '1' : '0' }}"
                style="border-bottom:1px solid #f3f4f6;{{ $isEnabled ? 'background:#f0fdf4;' : '' }}">
                <td style="padding:10px 14px;">
                    <div style="font-weight:600;">{{ $test->name }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ $test->code }} · <span style="background:#eff6ff;color:#1e40af;padding:1px 6px;border-radius:8px;font-size:10px;">{{ ucfirst(str_replace('_',' ',$test->category)) }}</span></div>
                </td>
                <td style="padding:10px 14px;font-size:11px;color:var(--text-muted);">
                    @if($test->container_type)
                        {{ $test->container_type }}
                    @else
                        {{ ucfirst($test->sample_type) }}
                    @endif
                </td>
                <td style="padding:10px 14px;text-align:center;">
                    @if($isEnabled)
                    <form method="POST" action="{{ route('lab.toggle-availability') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="test_code" value="{{ $test->code }}">
                        <input type="hidden" name="action" value="set_price">
                        <input type="number" name="price" value="{{ $ct->price }}" step="1" min="0" style="width:80px;padding:4px 6px;border:1px solid #d1d5db;border-radius:4px;font-size:12px;text-align:right;" onchange="this.form.submit()">
                    </form>
                    @else
                    <span style="color:#d1d5db;">—</span>
                    @endif
                </td>
                <td style="padding:10px 14px;font-size:11px;color:#6b7280;max-width:220px;">
                    @if($isEnabled)
                        @if(!empty($ctParams))
                            <span style="cursor:pointer;color:#2563eb;" onclick="editParams('{{ $test->code }}', '{{ implode(', ', $ctParams) }}')">{{ implode(', ', $ctParams) }}</span>
                        @elseif(!empty($defParams))
                            <span style="cursor:pointer;color:#9ca3af;font-style:italic;" onclick="editParams('{{ $test->code }}', '{{ implode(', ', $defParams) }}')" title="Default — click to customize">{{ implode(', ', $defParams) }}</span>
                        @else
                            <span style="cursor:pointer;color:#d1d5db;" onclick="editParams('{{ $test->code }}', '')">[+ add params]</span>
                        @endif
                    @else
                        @if(!empty($defParams))
                            <span style="color:#d1d5db;font-style:italic;">{{ implode(', ', $defParams) }}</span>
                        @else — @endif
                    @endif
                </td>
                <td style="padding:10px 14px;text-align:center;">
                    @if($isEnabled)
                        <span style="background:{{ $isAvail ? '#dcfce7' : '#fef3c7' }};color:{{ $isAvail ? '#166534' : '#92400e' }};padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600;">
                            {{ $isAvail ? 'Available' : 'Paused' }}
                        </span>
                    @else
                        <span style="color:#d1d5db;font-size:11px;">Not offered</span>
                    @endif
                </td>
                <td style="padding:10px 14px;text-align:center;">
                    @if(!$isEnabled)
                    <form method="POST" action="{{ route('lab.toggle-availability') }}" style="display:inline-flex;align-items:center;gap:4px;">
                        @csrf
                        <input type="hidden" name="test_code" value="{{ $test->code }}">
                        <input type="hidden" name="action" value="enable">
                        <input type="number" name="price" placeholder="₹" step="1" min="0" style="width:65px;padding:4px 6px;border:1px solid #d1d5db;border-radius:4px;font-size:11px;" required>
                        <button type="submit" class="btn btn-sm btn-primary" style="font-size:11px;">+ Enable</button>
                    </form>
                    @elseif($isAvail)
                    <form method="POST" action="{{ route('lab.toggle-availability') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="test_code" value="{{ $test->code }}">
                        <input type="hidden" name="action" value="disable">
                        <button type="submit" class="btn btn-sm btn-outline" style="font-size:11px;">Pause</button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('lab.toggle-availability') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="test_code" value="{{ $test->code }}">
                        <input type="hidden" name="action" value="enable">
                        <input type="hidden" name="price" value="{{ $ct->price }}">
                        <button type="submit" class="btn btn-sm btn-primary" style="font-size:11px;">Resume</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Hidden param edit modal --}}
<div id="param-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:999;justify-content:center;align-items:center;">
    <div style="background:#fff;border-radius:12px;padding:24px;max-width:480px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <h3 style="margin:0 0 8px;font-size:15px;font-weight:700;">Edit Test Parameters</h3>
        <p style="font-size:12px;color:#6b7280;margin:0 0 12px;">Comma-separated list of parameters included in this test (e.g. RBC, WBC, Hemoglobin, Platelets)</p>
        <form method="POST" action="{{ route('lab.toggle-availability') }}" id="param-form">
            @csrf
            <input type="hidden" name="test_code" id="param-code">
            <input type="hidden" name="action" value="set_params">
            <textarea name="parameters" id="param-input" rows="3" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;resize:vertical;"></textarea>
            <div style="display:flex;gap:8px;margin-top:12px;">
                <button type="submit" class="btn btn-sm btn-primary">Save Parameters</button>
                <button type="button" class="btn btn-sm btn-outline" onclick="document.getElementById('param-modal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Custom Test Modal --}}
<div id="ih-custom-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:999;justify-content:center;align-items:center;">
    <div style="background:#fff;border-radius:12px;padding:24px;max-width:520px;width:90%;">
        <h3 style="margin:0 0 4px;font-size:15px;font-weight:700;">Add Custom Test</h3>
        <p style="font-size:12px;color:#6b7280;margin:0 0 14px;">Test not in the directory? Add it here and it will be submitted for review.</p>
        <form method="POST" action="{{ route('lab.toggle-availability') }}">
            @csrf
            <input type="hidden" name="action" value="custom">
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px;margin-bottom:10px;">
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">Test Name *</label>
                    <input type="text" name="custom_name" required style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">Price (₹) *</label>
                    <input type="number" name="price" required min="0" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">Category</label>
                    <select name="custom_category" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                        @foreach(['biochemistry','hematology','coagulation','endocrinology','serology','microbiology','urinalysis','cytology','histopathology','pcr','rapid_test','cardiac','inflammation','tumor_markers','imaging','other'] as $c)
                        <option value="{{ $c }}">{{ ucfirst(str_replace('_',' ',$c)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">Sample Type</label>
                    <select name="custom_sample" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                        @foreach(['blood','urine','swab','tissue','feces','fluid','other'] as $s)
                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="margin-bottom:10px;">
                <label style="font-size:11px;font-weight:600;color:#374151;">Parameters (comma-separated)</label>
                <input type="text" name="parameters" placeholder="e.g., RBC, WBC, Hemoglobin" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" style="background:#7c3aed;color:#fff;padding:10px 20px;border-radius:6px;font-size:13px;font-weight:600;border:none;cursor:pointer;">Add Test</button>
                <button type="button" style="background:#e5e7eb;color:#374151;padding:10px 20px;border-radius:6px;font-size:13px;border:none;cursor:pointer;" onclick="document.getElementById('ih-custom-modal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function editParams(code, current) {
    document.getElementById('param-code').value = code;
    document.getElementById('param-input').value = current;
    document.getElementById('param-modal').style.display = 'flex';
    document.getElementById('param-input').focus();
}

function filterTests() {
    const q = document.getElementById('test-search').value.toLowerCase();
    const cat = document.getElementById('test-cat-filter').value;
    const status = document.getElementById('test-status-filter').value;
    document.querySelectorAll('.test-row').forEach(row => {
        const matchName = !q || row.dataset.name.includes(q) || row.dataset.code.includes(q);
        const matchCat = !cat || row.dataset.cat === cat;
        const matchStatus = !status || (status === 'enabled' && row.dataset.enabled === '1') || (status === 'disabled' && row.dataset.enabled === '0');
        row.style.display = matchName && matchCat && matchStatus ? '' : 'none';
    });
}
</script>
@endif
@endsection
