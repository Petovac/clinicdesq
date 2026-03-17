@extends('layouts.lab')

@section('content')
<h1 class="page-title">Test Catalog</h1>
<p style="color:var(--text-muted);font-size:13px;margin-bottom:20px;">Manage the tests your lab offers. Clinics that onboard your lab will see these tests and can import them.</p>

{{-- Add Test Form --}}
<div class="card" style="margin-bottom:24px;">
    <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">Add New Test</h3>
    <form method="POST" action="{{ route('lab.catalog.store') }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:4px;">Test Name *</label>
                <input type="text" name="test_name" required placeholder="e.g. Complete Blood Count" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:4px;">Code</label>
                <input type="text" name="test_code" placeholder="e.g. CBC-001" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:4px;">Category</label>
                <select name="category" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
                    <option value="">Select...</option>
                    @foreach(['hematology','biochemistry','urinalysis','serology','cytology','histopathology','microbiology','immunology','endocrinology','parasitology','other'] as $cat)
                        <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;margin-top:12px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:4px;">Sample Type</label>
                <select name="sample_type" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
                    <option value="">Select...</option>
                    @foreach(['blood','serum','plasma','urine','swab','tissue','fluid','feces','other'] as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:4px;">Parameters</label>
                <input type="text" name="parameters" placeholder="RBC, WBC, Platelets..." style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:4px;">Est. Time</label>
                <input type="text" name="estimated_time" placeholder="e.g. 2 hours" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:4px;">B2B Price (₹) *</label>
                <input type="number" name="b2b_price" step="0.01" min="0" required placeholder="0.00" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:14px;">Add Test</button>
    </form>
</div>

{{-- Existing Tests --}}
<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="background:#f9fafb;border-bottom:1px solid var(--border);">
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Test Name</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Code</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Category</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Sample</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Parameters</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Time</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">B2B Price</th>
                <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Status</th>
                <th style="padding:10px 14px;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tests as $test)
                <tr style="border-bottom:1px solid #f3f4f6;" id="row-{{ $test->id }}">
                    <td style="padding:10px 14px;font-weight:600;">{{ $test->test_name }}</td>
                    <td style="padding:10px 14px;color:var(--text-muted);">{{ $test->test_code ?? '—' }}</td>
                    <td style="padding:10px 14px;text-transform:capitalize;">{{ $test->category ?? '—' }}</td>
                    <td style="padding:10px 14px;text-transform:capitalize;">{{ $test->sample_type ?? '—' }}</td>
                    <td style="padding:10px 14px;font-size:11px;color:var(--text-muted);">
                        {{ is_array($test->parameters) ? implode(', ', $test->parameters) : ($test->parameters ?? '—') }}
                    </td>
                    <td style="padding:10px 14px;">{{ $test->estimated_time ?? '—' }}</td>
                    <td style="padding:10px 14px;font-weight:600;">₹{{ number_format($test->b2b_price, 2) }}</td>
                    <td style="padding:10px 14px;">
                        @if($test->is_active)
                            <span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">Active</span>
                        @else
                            <span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">Inactive</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;white-space:nowrap;">
                        <button onclick="document.getElementById('edit-{{ $test->id }}').style.display='table-row'" class="btn btn-outline btn-sm">Edit</button>
                        <form method="POST" action="{{ route('lab.catalog.destroy', $test) }}" style="display:inline;" onsubmit="return confirm('Delete this test?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#991b1b;">Del</button>
                        </form>
                    </td>
                </tr>
                {{-- Inline edit row --}}
                <tr id="edit-{{ $test->id }}" style="display:none;background:#f9fafb;">
                    <td colspan="9" style="padding:14px;">
                        <form method="POST" action="{{ route('lab.catalog.update', $test) }}" style="display:flex;flex-wrap:wrap;gap:8px;align-items:end;">
                            @csrf @method('PUT')
                            <div><label style="font-size:11px;font-weight:600;display:block;margin-bottom:2px;">Name</label><input name="test_name" value="{{ $test->test_name }}" style="padding:6px 8px;border:1px solid var(--border);border-radius:6px;font-size:12px;width:160px;" required></div>
                            <div><label style="font-size:11px;font-weight:600;display:block;margin-bottom:2px;">Code</label><input name="test_code" value="{{ $test->test_code }}" style="padding:6px 8px;border:1px solid var(--border);border-radius:6px;font-size:12px;width:80px;"></div>
                            <div><label style="font-size:11px;font-weight:600;display:block;margin-bottom:2px;">Category</label><input name="category" value="{{ $test->category }}" style="padding:6px 8px;border:1px solid var(--border);border-radius:6px;font-size:12px;width:100px;"></div>
                            <div><label style="font-size:11px;font-weight:600;display:block;margin-bottom:2px;">Sample</label><input name="sample_type" value="{{ $test->sample_type }}" style="padding:6px 8px;border:1px solid var(--border);border-radius:6px;font-size:12px;width:80px;"></div>
                            <div><label style="font-size:11px;font-weight:600;display:block;margin-bottom:2px;">Parameters</label><input name="parameters" value="{{ is_array($test->parameters) ? implode(', ', $test->parameters) : $test->parameters }}" style="padding:6px 8px;border:1px solid var(--border);border-radius:6px;font-size:12px;width:180px;"></div>
                            <div><label style="font-size:11px;font-weight:600;display:block;margin-bottom:2px;">Time</label><input name="estimated_time" value="{{ $test->estimated_time }}" style="padding:6px 8px;border:1px solid var(--border);border-radius:6px;font-size:12px;width:80px;"></div>
                            <div><label style="font-size:11px;font-weight:600;display:block;margin-bottom:2px;">B2B ₹</label><input name="b2b_price" type="number" step="0.01" value="{{ $test->b2b_price }}" style="padding:6px 8px;border:1px solid var(--border);border-radius:6px;font-size:12px;width:80px;" required></div>
                            <div style="display:flex;align-items:center;gap:4px;"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ $test->is_active ? 'checked' : '' }} style="accent-color:var(--primary);"> <span style="font-size:11px;">Active</span></div>
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            <button type="button" onclick="this.closest('tr').style.display='none'" class="btn btn-outline btn-sm">Cancel</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="padding:40px;text-align:center;color:var(--text-muted);">No tests in your catalog yet. Add your first test above.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
