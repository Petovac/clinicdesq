@extends('layouts.lab')

@section('content')
<h1 class="page-title">Test Catalog</h1>
<p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">Manage your test offerings. Organisations see this when browsing your catalog.</p>

@php
    $offeredTests = $allTests->filter(function($t) use ($offerings) {
        $off = $offerings[$t->code] ?? null;
        return $off && $off->is_active;
    });
    $notOfferedTests = $allTests->filter(function($t) use ($offerings) {
        $off = $offerings[$t->code] ?? null;
        return !$off || !$off->is_active;
    });
@endphp

{{-- Stats --}}
<div style="display:flex;gap:12px;margin-bottom:18px;">
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;flex:1;">
        <div style="font-size:24px;font-weight:700;color:#16a34a;">{{ $offeredTests->count() }}</div>
        <div style="font-size:12px;color:#15803d;">Tests You Offer</div>
    </div>
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 18px;flex:1;">
        <div style="font-size:24px;font-weight:700;color:#2563eb;">{{ $allTests->count() }}</div>
        <div style="font-size:12px;color:#1e40af;">In Directory</div>
    </div>
    <div style="flex:0;">
        <button onclick="document.getElementById('custom-test-modal').style.display='flex'" style="background:#7c3aed;color:#fff;border:none;padding:14px 20px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;height:100%;">+ Add Custom Test</button>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- YOUR OFFERED TESTS --}}
{{-- ═══════════════════════════════════════════ --}}
<div style="margin-bottom:24px;">
    <h2 style="font-size:16px;font-weight:700;margin:0 0 12px;display:flex;align-items:center;gap:8px;">
        <span style="color:#16a34a;">✓</span> Your Offered Tests ({{ $offeredTests->count() }})
    </h2>

    @if($offeredTests->isEmpty())
        <div class="card" style="padding:24px;text-align:center;color:var(--text-muted);">
            <p style="font-size:14px;">You haven't added any tests yet. Browse the directory below and add tests you offer.</p>
        </div>
    @else
        <div class="card" style="padding:0;max-height:400px;overflow-y:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="position:sticky;top:0;background:#f0fdf4;z-index:1;">
                    <tr style="border-bottom:1px solid #bbf7d0;">
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:#15803d;text-transform:uppercase;">Test</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:#15803d;text-transform:uppercase;">Sample Required</th>
                        <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:600;color:#15803d;text-transform:uppercase;">B2B Price</th>
                        <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:600;color:#15803d;text-transform:uppercase;">TAT</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:#15803d;text-transform:uppercase;">Parameters</th>
                        <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:600;color:#15803d;text-transform:uppercase;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offeredTests as $test)
                    @php
                        $off = $offerings[$test->code];
                        $offParams = $off->parameters ? json_decode($off->parameters, true) : [];
                        $defParams = $test->default_parameters ? json_decode($test->default_parameters, true) : [];
                        $dirSample = $test->preferred_sample ?? ucfirst($test->sample_type);
                        $labSample = $off->container_type ?? '';
                        $displaySample = $labSample ?: $dirSample;
                    @endphp
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:10px 14px;">
                            <div style="font-weight:600;">{{ $test->name }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $test->code }} · <span style="background:#eff6ff;color:#1e40af;padding:1px 6px;border-radius:8px;font-size:10px;">{{ ucfirst(str_replace('_',' ',$test->category)) }}</span></div>
                        </td>
                        <td style="padding:10px 14px;font-size:12px;color:#374151;">
                            {{ $displaySample }}
                            @if($off->sample_volume)
                                <span style="color:#9ca3af;font-size:11px;">({{ $off->sample_volume }})</span>
                            @endif
                        </td>
                        <td style="padding:10px 14px;text-align:center;">
                            <form method="POST" action="{{ route('lab.catalog.toggle') }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="test_code" value="{{ $test->code }}">
                                <input type="hidden" name="action" value="set_price">
                                <input type="number" name="b2b_price" value="{{ $off->b2b_price }}" step="1" min="0" style="width:80px;padding:4px 6px;border:1px solid #d1d5db;border-radius:4px;font-size:12px;text-align:right;" onchange="this.form.submit()">
                            </form>
                        </td>
                        <td style="padding:10px 14px;text-align:center;">
                            <form method="POST" action="{{ route('lab.catalog.toggle') }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="test_code" value="{{ $test->code }}">
                                <input type="hidden" name="action" value="set_price">
                                <input type="hidden" name="b2b_price" value="{{ $off->b2b_price }}">
                                <input type="text" name="estimated_time" value="{{ $off->estimated_time ?? '' }}" placeholder="{{ $test->tat ?? 'TAT' }}" style="width:75px;padding:4px 6px;border:1px solid #d1d5db;border-radius:4px;font-size:11px;text-align:center;" onchange="this.form.submit()">
                            </form>
                        </td>
                        <td style="padding:10px 14px;font-size:11px;color:#6b7280;max-width:200px;">
                            @if(!empty($offParams))
                                <span style="cursor:pointer;color:#2563eb;" onclick="editLabParams('{{ $test->code }}', '{{ implode(', ', $offParams) }}')">{{ implode(', ', $offParams) }}</span>
                            @elseif(!empty($defParams))
                                <span style="cursor:pointer;color:#9ca3af;font-style:italic;" onclick="editLabParams('{{ $test->code }}', '{{ implode(', ', $defParams) }}')" title="Default — click to customize">{{ implode(', ', $defParams) }}</span>
                            @else
                                <span style="cursor:pointer;color:#d1d5db;" onclick="editLabParams('{{ $test->code }}', '')">[+ add]</span>
                            @endif
                        </td>
                        <td style="padding:10px 14px;text-align:center;">
                            <div style="display:flex;gap:4px;justify-content:center;">
                                <button onclick="editOffering('{{ $test->code }}', '{{ addslashes($test->name) }}', '{{ $off->b2b_price }}', '{{ addslashes($off->estimated_time ?? '') }}', '{{ addslashes($labSample) }}', '{{ addslashes($off->sample_volume ?? '') }}', '{{ addslashes(implode(', ', $offParams)) }}', '{{ addslashes($dirSample) }}', '{{ $test->tat ?? '' }}')"
                                        class="btn btn-sm btn-outline" style="font-size:11px;color:#2563eb;border-color:#93c5fd;">Edit</button>
                                <form method="POST" action="{{ route('lab.catalog.toggle') }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="test_code" value="{{ $test->code }}">
                                    <input type="hidden" name="action" value="disable">
                                    <button type="submit" class="btn btn-sm btn-outline" style="font-size:11px;color:#dc2626;border-color:#fca5a5;">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- ADD MORE TESTS --}}
{{-- ═══════════════════════════════════════════ --}}
<div>
    <h2 style="font-size:16px;font-weight:700;margin:0 0 12px;display:flex;align-items:center;gap:8px;">
        <span style="color:#2563eb;">+</span> Add More Tests ({{ $notOfferedTests->count() }} available)
    </h2>

    {{-- Search/Filter --}}
    <div style="display:flex;gap:10px;margin-bottom:12px;">
        <input type="text" id="cat-search" placeholder="Search by name or code..." style="flex:1;padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-size:13px;" oninput="filterCatalog()">
        <select id="cat-filter" style="padding:10px;border:1px solid var(--border);border-radius:8px;font-size:13px;" onchange="filterCatalog()">
            <option value="">All Categories</option>
            @php $cats = $notOfferedTests->pluck('category')->unique()->sort(); @endphp
            @foreach($cats as $cat)<option value="{{ $cat }}">{{ ucfirst(str_replace('_',' ',$cat)) }}</option>@endforeach
        </select>
    </div>

    <div class="card" style="padding:0;max-height:500px;overflow-y:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead style="position:sticky;top:0;background:#f9fafb;z-index:1;">
                <tr style="border-bottom:1px solid var(--border);">
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Test</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Sample</th>
                    <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">TAT</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Parameters</th>
                    <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Add to Catalog</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notOfferedTests as $test)
                @php
                    $defParams = $test->default_parameters ? json_decode($test->default_parameters, true) : [];
                    $dirSample = $test->preferred_sample ?? ucfirst($test->sample_type);
                @endphp
                <tr class="cat-row" data-name="{{ strtolower($test->name) }}" data-code="{{ strtolower($test->code) }}" data-cat="{{ $test->category }}"
                    style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:10px 14px;">
                        <div style="font-weight:600;">{{ $test->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $test->code }} · <span style="background:#eff6ff;color:#1e40af;padding:1px 6px;border-radius:8px;font-size:10px;">{{ ucfirst(str_replace('_',' ',$test->category)) }}</span></div>
                    </td>
                    <td style="padding:10px 14px;font-size:11px;color:var(--text-muted);">{{ $dirSample }}</td>
                    <td style="padding:10px 14px;text-align:center;font-size:11px;color:var(--text-muted);">{{ $test->tat ?? '—' }}</td>
                    <td style="padding:10px 14px;font-size:11px;color:#9ca3af;max-width:180px;">
                        @if(!empty($defParams))
                            {{ implode(', ', $defParams) }}
                        @else — @endif
                    </td>
                    <td style="padding:10px 14px;text-align:center;">
                        <form method="POST" action="{{ route('lab.catalog.toggle') }}" style="display:inline-flex;align-items:center;gap:4px;flex-wrap:wrap;justify-content:center;">
                            @csrf
                            <input type="hidden" name="test_code" value="{{ $test->code }}">
                            <input type="hidden" name="action" value="enable">
                            <input type="hidden" name="container_type" value="{{ $dirSample }}">
                            <input type="number" name="b2b_price" placeholder="₹ Price" step="1" min="0" style="width:75px;padding:5px 6px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;" required>
                            <input type="text" name="estimated_time" value="{{ $test->tat ?? '' }}" placeholder="TAT" style="width:65px;padding:5px 6px;border:1px solid #d1d5db;border-radius:6px;font-size:11px;">
                            <button type="submit" class="btn btn-sm btn-primary" style="font-size:11px;padding:5px 12px;">+ Add</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Edit Offering Modal --}}
<div id="edit-offering-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:999;justify-content:center;align-items:center;">
    <div style="background:#fff;border-radius:12px;padding:24px;max-width:520px;width:90%;">
        <h3 style="margin:0 0 4px;font-size:15px;font-weight:700;" id="edit-modal-title">Edit Test Offering</h3>
        <p style="font-size:12px;color:#6b7280;margin:0 0 14px;">Customize your offering details for this test.</p>
        <form method="POST" action="{{ route('lab.catalog.toggle') }}" id="edit-offering-form">
            @csrf
            <input type="hidden" name="test_code" id="eo-code">
            <input type="hidden" name="action" value="set_price">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">B2B Price (₹) *</label>
                    <input type="number" name="b2b_price" id="eo-price" required min="0" step="1" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">TAT</label>
                    <input type="text" name="estimated_time" id="eo-tat" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                    <div style="font-size:10px;color:#9ca3af;margin-top:2px;" id="eo-tat-hint"></div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">Sample Required</label>
                    <input type="text" name="container_type" id="eo-sample" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                    <div style="font-size:10px;color:#9ca3af;margin-top:2px;" id="eo-sample-hint"></div>
                </div>
                <div>
                    <label style="font-size:11px;font-weight:600;color:#374151;">Sample Volume</label>
                    <input type="text" name="sample_volume" id="eo-volume" placeholder="e.g., 2ml" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:11px;font-weight:600;color:#374151;">Parameters (comma-separated)</label>
                <textarea name="parameters" id="eo-params" rows="2" placeholder="e.g., RBC, WBC, Hemoglobin, Platelets" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;resize:vertical;"></textarea>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
                <button type="button" class="btn btn-sm btn-outline" onclick="document.getElementById('edit-offering-modal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
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
        <p style="font-size:12px;color:#6b7280;margin:0 0 14px;">Test not in our directory? Add it here.</p>
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
                    <label style="font-size:11px;font-weight:600;color:#374151;">Sample Required</label>
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
function editOffering(code, name, price, tat, sample, volume, params, dirSample, dirTat) {
    document.getElementById('edit-modal-title').textContent = 'Edit: ' + name;
    document.getElementById('eo-code').value = code;
    document.getElementById('eo-price').value = price;
    document.getElementById('eo-tat').value = tat;
    document.getElementById('eo-tat').placeholder = dirTat || 'e.g. 3 Hrs';
    document.getElementById('eo-tat-hint').textContent = dirTat ? 'Directory default: ' + dirTat : '';
    document.getElementById('eo-sample').value = sample;
    document.getElementById('eo-sample').placeholder = dirSample || 'e.g., 2ml EDTA Blood';
    document.getElementById('eo-sample-hint').textContent = dirSample ? 'Directory default: ' + dirSample : '';
    document.getElementById('eo-volume').value = volume;
    document.getElementById('eo-params').value = params;
    document.getElementById('edit-offering-modal').style.display = 'flex';
    document.getElementById('eo-price').focus();
}

function editLabParams(code, current) {
    document.getElementById('p-code').value = code;
    document.getElementById('p-input').value = current;
    document.getElementById('param-modal').style.display = 'flex';
    document.getElementById('p-input').focus();
}

function filterCatalog() {
    const q = document.getElementById('cat-search').value.toLowerCase();
    const cat = document.getElementById('cat-filter').value;
    document.querySelectorAll('.cat-row').forEach(row => {
        const matchName = !q || row.dataset.name.includes(q) || row.dataset.code.includes(q);
        const matchCat = !cat || row.dataset.cat === cat;
        row.style.display = matchName && matchCat ? '' : 'none';
    });
}

// Close modals on backdrop click
['edit-offering-modal', 'param-modal', 'custom-test-modal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});
</script>
@endsection
