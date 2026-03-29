@extends('layouts.lab')

@section('content')
<h1 class="page-title">Test Catalog</h1>
<p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">Select tests your lab offers, set B2B pricing, and specify parameters. Organisations see this when browsing your catalog.</p>

{{-- Search/Filter --}}
<div style="display:flex;gap:10px;margin-bottom:14px;">
    <input type="text" id="cat-search" placeholder="Search tests..." style="flex:1;padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-size:13px;" oninput="filterCatalog()">
    <select id="cat-filter" style="padding:10px;border:1px solid var(--border);border-radius:8px;font-size:13px;" onchange="filterCatalog()">
        <option value="">All Categories</option>
        @php $cats = $allTests->pluck('category')->unique()->sort(); @endphp
        @foreach($cats as $cat)<option value="{{ $cat }}">{{ ucfirst(str_replace('_',' ',$cat)) }}</option>@endforeach
    </select>
    <select id="cat-status" style="padding:10px;border:1px solid var(--border);border-radius:8px;font-size:13px;" onchange="filterCatalog()">
        <option value="">All Tests</option>
        <option value="offered">Offered</option>
        <option value="not">Not Offered</option>
    </select>
</div>

<div style="display:flex;gap:12px;margin-bottom:14px;">
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 14px;font-size:13px;color:#1e40af;flex:1;">
        <strong>{{ $offerings->where('is_active', true)->count() }}</strong> tests offered out of <strong>{{ $allTests->count() }}</strong> in directory
    </div>
    <button onclick="document.getElementById('custom-test-modal').style.display='flex'" style="background:#7c3aed;color:#fff;border:none;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;">+ Add Custom Test</button>
</div>

<div class="card" style="padding:0;max-height:650px;overflow-y:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead style="position:sticky;top:0;background:#f9fafb;z-index:1;">
            <tr style="border-bottom:1px solid var(--border);">
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Test</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Collection</th>
                <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">B2B Price</th>
                <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">TAT</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Parameters</th>
                <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allTests as $test)
            @php
                $off = $offerings[$test->code] ?? null;
                $isOffered = $off && $off->is_active;
                $offParams = $off && $off->parameters ? json_decode($off->parameters, true) : [];
                $defParams = $test->default_parameters ? json_decode($test->default_parameters, true) : [];
                $showParams = !empty($offParams) ? $offParams : $defParams;
            @endphp
            <tr class="cat-row" data-name="{{ strtolower($test->name) }}" data-code="{{ strtolower($test->code) }}" data-cat="{{ $test->category }}" data-offered="{{ $isOffered ? '1' : '0' }}"
                style="border-bottom:1px solid #f3f4f6;{{ $isOffered ? 'background:#f0fdf4;' : '' }}">
                <td style="padding:10px 14px;">
                    <div style="font-weight:600;">{{ $test->name }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ $test->code }} · <span style="background:#eff6ff;color:#1e40af;padding:1px 6px;border-radius:8px;font-size:10px;">{{ ucfirst(str_replace('_',' ',$test->category)) }}</span></div>
                </td>
                <td style="padding:10px 14px;font-size:11px;color:var(--text-muted);">
                    @if($test->preferred_sample)
                        <div>{{ $test->preferred_sample }}</div>
                    @else
                        <div>{{ ucfirst($test->sample_type) }}</div>
                    @endif
                </td>
                <td style="padding:10px 14px;text-align:center;">
                    @if($isOffered)
                    <form method="POST" action="{{ route('lab.catalog.toggle') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="test_code" value="{{ $test->code }}">
                        <input type="hidden" name="action" value="set_price">
                        <input type="number" name="b2b_price" value="{{ $off->b2b_price }}" step="1" min="0" style="width:80px;padding:4px 6px;border:1px solid #d1d5db;border-radius:4px;font-size:12px;text-align:right;" onchange="this.form.submit()">
                    </form>
                    @else
                    <span style="color:#d1d5db;">—</span>
                    @endif
                </td>
                <td style="padding:10px 14px;text-align:center;">
                    @if($isOffered)
                    <form method="POST" action="{{ route('lab.catalog.toggle') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="test_code" value="{{ $test->code }}">
                        <input type="hidden" name="action" value="set_price">
                        <input type="hidden" name="b2b_price" value="{{ $off->b2b_price }}">
                        <input type="text" name="estimated_time" value="{{ $off->estimated_time ?? '' }}" placeholder="e.g. 3 Hrs" style="width:75px;padding:4px 6px;border:1px solid #d1d5db;border-radius:4px;font-size:11px;text-align:center;" onchange="this.form.submit()">
                    </form>
                    @else — @endif
                </td>
                <td style="padding:10px 14px;font-size:11px;color:#6b7280;max-width:220px;">
                    @if($isOffered)
                        @if(!empty($offParams))
                            <span style="cursor:pointer;color:#2563eb;" onclick="editLabParams('{{ $test->code }}', '{{ implode(', ', $offParams) }}')">{{ implode(', ', $offParams) }}</span>
                        @elseif(!empty($defParams))
                            <span style="cursor:pointer;color:#9ca3af;font-style:italic;" onclick="editLabParams('{{ $test->code }}', '{{ implode(', ', $defParams) }}')" title="Default from directory — click to customize">{{ implode(', ', $defParams) }}</span>
                        @else
                            <span style="cursor:pointer;color:#d1d5db;" onclick="editLabParams('{{ $test->code }}', '')">[+ add params]</span>
                        @endif
                    @else
                        @if(!empty($defParams))
                            <span style="color:#d1d5db;font-style:italic;" title="Default parameters from directory">{{ implode(', ', $defParams) }}</span>
                        @else — @endif
                    @endif
                </td>
                <td style="padding:10px 14px;text-align:center;">
                    @if(!$isOffered)
                    <form method="POST" action="{{ route('lab.catalog.toggle') }}" style="display:inline-flex;align-items:center;gap:4px;flex-wrap:wrap;">
                        @csrf
                        <input type="hidden" name="test_code" value="{{ $test->code }}">
                        <input type="hidden" name="action" value="enable">
                        <input type="number" name="b2b_price" placeholder="₹ Price" step="1" min="0" style="width:70px;padding:4px 6px;border:1px solid #d1d5db;border-radius:4px;font-size:11px;" required>
                        <input type="text" name="estimated_time" placeholder="TAT" style="width:60px;padding:4px 6px;border:1px solid #d1d5db;border-radius:4px;font-size:11px;">
                        <button type="submit" class="btn btn-sm btn-primary" style="font-size:11px;">+ Add</button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('lab.catalog.toggle') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="test_code" value="{{ $test->code }}">
                        <input type="hidden" name="action" value="disable">
                        <button type="submit" class="btn btn-sm btn-outline" style="font-size:11px;color:#dc2626;border-color:#fca5a5;">Remove</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Parameter Edit Modal --}}
<div id="param-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:999;justify-content:center;align-items:center;">
    <div style="background:#fff;border-radius:12px;padding:24px;max-width:480px;width:90%;">
        <h3 style="margin:0 0 8px;font-size:15px;font-weight:700;">Edit Parameters</h3>
        <p style="font-size:12px;color:#6b7280;margin:0 0 12px;">Comma-separated list (e.g., RBC, WBC, Hemoglobin, Platelets)</p>
        <form method="POST" action="{{ route('lab.catalog.toggle') }}" id="param-form">
            @csrf
            <input type="hidden" name="test_code" id="p-code">
            <input type="hidden" name="action" value="set_params">
            <textarea name="parameters" id="p-input" rows="3" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;resize:vertical;"></textarea>
            <div style="display:flex;gap:8px;margin-top:12px;">
                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                <button type="button" class="btn btn-sm btn-outline" onclick="document.getElementById('param-modal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Custom Test Modal --}}
<div id="custom-test-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:999;justify-content:center;align-items:center;">
    <div style="background:#fff;border-radius:12px;padding:24px;max-width:520px;width:90%;">
        <h3 style="margin:0 0 4px;font-size:15px;font-weight:700;">Add Custom Test</h3>
        <p style="font-size:12px;color:#6b7280;margin:0 0 14px;">Test not in our directory? Add it here. It will be submitted for review and added to the master directory.</p>
        <form method="POST" action="{{ route('lab.catalog.toggle') }}">
            @csrf
            <input type="hidden" name="action" value="custom">
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px;margin-bottom:10px;">
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">Test Name *</label>
                    <input type="text" name="custom_name" required style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">B2B Price (₹) *</label>
                    <input type="number" name="b2b_price" required min="0" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:10px;">
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
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">TAT</label>
                    <input type="text" name="estimated_time" placeholder="e.g. 3 Hrs" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                </div>
            </div>
            <div style="margin-bottom:10px;">
                <label style="font-size:11px;font-weight:600;color:#374151;">Parameters (comma-separated)</label>
                <input type="text" name="parameters" placeholder="e.g., RBC, WBC, Hemoglobin" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">Container / Vial</label>
                    <input type="text" name="container_type" placeholder="e.g., 2ml EDTA Blood" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">Volume</label>
                    <input type="text" name="sample_volume" placeholder="e.g., 2ml" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-sm btn-primary">Add Test</button>
                <button type="button" class="btn btn-sm btn-outline" onclick="document.getElementById('custom-test-modal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function editLabParams(code, current) {
    document.getElementById('p-code').value = code;
    document.getElementById('p-input').value = current;
    document.getElementById('param-modal').style.display = 'flex';
    document.getElementById('p-input').focus();
}

function filterCatalog() {
    const q = document.getElementById('cat-search').value.toLowerCase();
    const cat = document.getElementById('cat-filter').value;
    const status = document.getElementById('cat-status').value;
    document.querySelectorAll('.cat-row').forEach(row => {
        const matchName = !q || row.dataset.name.includes(q) || row.dataset.code.includes(q);
        const matchCat = !cat || row.dataset.cat === cat;
        const matchStatus = !status || (status === 'offered' && row.dataset.offered === '1') || (status === 'not' && row.dataset.offered === '0');
        row.style.display = matchName && matchCat && matchStatus ? '' : 'none';
    });
}
</script>
@endsection
