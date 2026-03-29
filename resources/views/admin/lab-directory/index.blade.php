@extends('admin.layout')

@section('content')
<style>
.stat-row { display:flex;gap:12px;margin-bottom:16px; }
.stat-box { background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:14px;text-align:center;flex:1; }
.stat-box .val { font-size:24px;font-weight:700;color:#111; }
.stat-box .lbl { font-size:11px;color:#6b7280;text-transform:uppercase; }
.filters { display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap; }
.filters input, .filters select { padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px; }
.filters input { flex:1;min-width:200px; }
.badge-cat { background:#eff6ff;color:#1e40af;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600; }
.badge-custom { background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600; }
table { width:100%;border-collapse:collapse;font-size:13px; }
thead th { padding:8px 10px;text-align:left;font-size:10px;font-weight:600;color:#6b7280;text-transform:uppercase;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
tbody td { padding:8px 10px;border-bottom:1px solid #f3f4f6;vertical-align:top; }
tbody tr:hover td { background:#f9fafb; }
.btn-xs { padding:3px 8px;border-radius:4px;border:none;cursor:pointer;font-size:11px;font-weight:600; }
.btn-edit { background:#dbeafe;color:#1d4ed8; }
.btn-del { background:#fee2e2;color:#dc2626; }
.success-bar { background:#dcfce7;border:1px solid #bbf7d0;padding:10px 14px;border-radius:6px;margin-bottom:14px;color:#166534;font-size:14px; }
.error-bar { background:#fee2e2;border:1px solid #fca5a5;padding:10px 14px;border-radius:6px;margin-bottom:14px;color:#991b1b;font-size:14px; }
</style>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <h1 class="page-title" style="margin:0;">Lab Test Directory</h1>
    <button onclick="document.getElementById('add-modal').style.display='flex'" style="background:#2563eb;color:#fff;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">+ Add Test</button>
</div>

@if(session('success'))<div class="success-bar">✓ {{ session('success') }}</div>@endif
@if(session('error'))<div class="error-bar">{{ session('error') }}</div>@endif

<div class="stat-row">
    <div class="stat-box"><div class="val">{{ $totalTests }}</div><div class="lbl">Total Tests</div></div>
    <div class="stat-box"><div class="val">{{ $categories->count() }}</div><div class="lbl">Categories</div></div>
    <div class="stat-box"><div class="val" style="color:#f59e0b;">{{ $customTests }}</div><div class="lbl">Custom (Lab-submitted)</div></div>
</div>

{{-- Category pills --}}
<div style="display:flex;gap:6px;margin-bottom:14px;flex-wrap:wrap;">
    <a href="?category=" style="padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;text-decoration:none;{{ !$cat ? 'background:#2563eb;color:#fff;' : 'background:#f3f4f6;color:#374151;' }}">All</a>
    @foreach($categories as $c)
    <a href="?category={{ $c->category }}" style="padding:4px 10px;border-radius:12px;font-size:11px;font-weight:600;text-decoration:none;{{ $cat === $c->category ? 'background:#2563eb;color:#fff;' : 'background:#f3f4f6;color:#374151;' }}">{{ ucfirst(str_replace('_',' ',$c->category)) }} ({{ $c->cnt }})</a>
    @endforeach
</div>

<form method="GET" class="filters">
    <input type="text" name="q" value="{{ $q }}" placeholder="Search by name or code...">
    <input type="hidden" name="category" value="{{ $cat }}">
    <button type="submit" style="background:#2563eb;color:#fff;border:none;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">Search</button>
    @if($q)<a href="?category={{ $cat }}" style="padding:8px 12px;color:#6b7280;text-decoration:none;font-size:13px;">Clear</a>@endif
</form>

<div class="card" style="padding:0;overflow:auto;max-height:600px;">
<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Category</th>
            <th>Sample Type</th>
            <th>Preferred Sample</th>
            <th>TAT</th>
            <th>Parameters</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tests as $test)
        @php $isCustom = str_starts_with($test->code, 'CUST-'); @endphp
        <tr>
            <td style="font-weight:600;white-space:nowrap;">
                {{ $test->code }}
                @if($isCustom) <span class="badge-custom">Custom</span> @endif
            </td>
            <td>{{ $test->name }}</td>
            <td><span class="badge-cat">{{ ucfirst(str_replace('_',' ',$test->category)) }}</span></td>
            <td style="font-size:12px;color:#6b7280;">{{ ucfirst($test->sample_type) }}</td>
            <td style="font-size:11px;color:#6b7280;">{{ $test->preferred_sample ?? '—' }}</td>
            <td style="font-size:11px;color:#6b7280;">{{ $test->tat ?? '—' }}</td>
            <td style="font-size:11px;color:#6b7280;max-width:200px;">
                @if($test->default_parameters)
                    {{ implode(', ', json_decode($test->default_parameters, true)) }}
                @else — @endif
            </td>
            <td style="white-space:nowrap;">
                <button class="btn-xs btn-edit" onclick='editTest(@json($test))'>Edit</button>
                <form method="POST" action="{{ route('admin.lab-directory.destroy', $test->code) }}" style="display:inline;" onsubmit="return confirm('Delete {{ $test->code }}?')">
                    @csrf @method('DELETE')
                    <button class="btn-xs btn-del">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
<div style="margin-top:12px;">{{ $tests->appends(['q' => $q, 'category' => $cat])->links() }}</div>

{{-- Add/Edit Modal --}}
<div id="add-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:999;justify-content:center;align-items:center;">
    <div style="background:#fff;border-radius:12px;padding:24px;max-width:600px;width:95%;max-height:90vh;overflow-y:auto;">
        <h3 id="modal-title" style="margin:0 0 14px;font-size:16px;font-weight:700;">Add Test to Directory</h3>
        <form method="POST" action="{{ route('admin.lab-directory.store') }}" id="dir-form">
            @csrf
            <div id="method-field"></div>
            <div style="display:grid;grid-template-columns:1fr 2fr;gap:10px;margin-bottom:10px;">
                <div>
                    <label style="font-size:11px;font-weight:600;">Code *</label>
                    <input type="text" name="code" id="f-code" required style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;text-transform:uppercase;">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;">Test Name *</label>
                    <input type="text" name="name" id="f-name" required style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                <div>
                    <label style="font-size:11px;font-weight:600;">Category *</label>
                    <select name="category" id="f-cat" required style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                        @foreach(['biochemistry','hematology','coagulation','endocrinology','serology','microbiology','urinalysis','cytology','histopathology','pcr','rapid_test','cardiac','inflammation','tumor_markers','imaging','panel','other'] as $c)
                        <option value="{{ $c }}">{{ ucfirst(str_replace('_',' ',$c)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;">Sample Type *</label>
                    <select name="sample_type" id="f-sample" required style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                        @foreach(['blood','urine','swab','tissue','feces','fluid','mixed','other'] as $s)
                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="margin-bottom:10px;">
                <label style="font-size:11px;font-weight:600;">Aliases (comma-separated search terms)</label>
                <input type="text" name="aliases" id="f-aliases" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;" placeholder="CBC, Complete Blood Count, Hemogram">
            </div>
            <div style="margin-bottom:10px;">
                <label style="font-size:11px;font-weight:600;">Default Parameters (comma-separated)</label>
                <input type="text" name="default_parameters" id="f-params" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;" placeholder="RBC, WBC, Hemoglobin, Platelets">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
                <div>
                    <label style="font-size:11px;font-weight:600;">Preferred Sample</label>
                    <input type="text" name="preferred_sample" id="f-preferred-sample" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;" placeholder="2ml EDTA Blood">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;">TAT (Turnaround Time)</label>
                    <input type="text" name="tat" id="f-tat" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;" placeholder="2 Hrs">
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" style="background:#2563eb;color:#fff;padding:10px 20px;border-radius:6px;font-size:13px;font-weight:600;border:none;cursor:pointer;" id="modal-btn">Add Test</button>
                <button type="button" style="background:#e5e7eb;color:#374151;padding:10px 20px;border-radius:6px;font-size:13px;border:none;cursor:pointer;" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function editTest(test) {
    document.getElementById('modal-title').textContent = 'Edit Test: ' + test.code;
    document.getElementById('modal-btn').textContent = 'Save Changes';
    document.getElementById('f-code').value = test.code;
    document.getElementById('f-code').readOnly = true;
    document.getElementById('f-name').value = test.name;
    document.getElementById('f-cat').value = test.category;
    document.getElementById('f-sample').value = test.sample_type;
    document.getElementById('f-aliases').value = test.aliases ? JSON.parse(test.aliases).join(', ') : '';
    document.getElementById('f-params').value = test.default_parameters ? JSON.parse(test.default_parameters).join(', ') : '';
    document.getElementById('f-preferred-sample').value = test.preferred_sample || '';
    document.getElementById('f-tat').value = test.tat || '';

    // Switch form to PUT
    document.getElementById('dir-form').action = '/admin/lab-directory/' + test.code;
    document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';

    document.getElementById('add-modal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('add-modal').style.display = 'none';
    // Reset form
    document.getElementById('modal-title').textContent = 'Add Test to Directory';
    document.getElementById('modal-btn').textContent = 'Add Test';
    document.getElementById('dir-form').action = '{{ route("admin.lab-directory.store") }}';
    document.getElementById('method-field').innerHTML = '';
    document.getElementById('f-code').readOnly = false;
    document.getElementById('f-code').value = '';
    document.getElementById('f-name').value = '';
    document.getElementById('f-aliases').value = '';
    document.getElementById('f-params').value = '';
    document.getElementById('f-preferred-sample').value = '';
    document.getElementById('f-tat').value = '';
}
</script>
@endsection
