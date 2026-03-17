@extends('organisation.layout')

@section('content')
<style>
.page-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:24px; }
.page-header h2 { font-size:22px;font-weight:600;margin:0;color:#111827; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.06); }
.form-group { margin-bottom:16px; }
.form-group label { font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;display:block; }
.form-group input,.form-group textarea,.form-group select { width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;box-sizing:border-box; }
.form-group textarea { min-height:70px;resize:vertical; }
.form-row { display:flex;gap:16px; }
.form-row .form-group { flex:1; }
.btn-primary { background:#2563eb;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer; }
.btn-secondary { background:#e5e7eb;color:#374151;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;text-decoration:none; }
.form-actions { display:flex;gap:12px;margin-top:20px; }
.error-text { color:#dc2626;font-size:11px;margin-top:4px; }
.back-link { color:#6b7280;text-decoration:none;font-size:13px; }
.back-link:hover { color:#2563eb; }
</style>

<div class="page-header">
    <h2>Edit Lab: {{ $lab->name }}</h2>
    <a href="{{ route('organisation.labs.index') }}" class="back-link">&larr; Back</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    {{-- Left: Lab details --}}
    <div class="card">
        <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">Lab Details</h3>
        <form method="POST" action="{{ route('organisation.labs.update', $lab) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Lab Name *</label>
                <input type="text" name="name" value="{{ old('name', $lab->name) }}" required>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Phone</label><input type="text" name="phone" value="{{ old('phone', $lab->phone) }}"></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email', $lab->email) }}"></div>
            </div>
            <div class="form-group"><label>Address</label><textarea name="address">{{ old('address', $lab->address) }}</textarea></div>
            <div class="form-row">
                <div class="form-group"><label>City</label><input type="text" name="city" value="{{ old('city', $lab->city) }}"></div>
                <div class="form-group"><label>State</label><input type="text" name="state" value="{{ old('state', $lab->state) }}"></div>
                <div class="form-group"><label>Pincode</label><input type="text" name="pincode" value="{{ old('pincode', $lab->pincode) }}"></div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Update Lab</button>
            </div>
        </form>
    </div>

    {{-- Right: Test offerings --}}
    <div>
        <div class="card">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">Test Offerings ({{ $lab->testOfferings->count() }})</h3>
            @foreach($lab->testOfferings as $test)
                <div style="padding:8px 0;{{ !$loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }};font-size:13px;">
                    <div style="display:flex;justify-content:space-between;">
                        <strong>{{ $test->test_name }}</strong>
                        <span style="color:#6b7280;">B2B: ₹{{ number_format($test->b2b_price, 2) }} @if($test->org_selling_price)| Sell: ₹{{ number_format($test->org_selling_price, 2) }}@endif</span>
                    </div>
                    @if($test->parameters)
                        <div style="font-size:11px;color:#6b7280;margin-top:2px;">{{ is_array($test->parameters) ? implode(', ', $test->parameters) : $test->parameters }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="card" style="margin-top:16px;">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">Add Test Offering</h3>
            <form method="POST" action="{{ route('organisation.labs.test.store', $lab) }}">
                @csrf
                <div class="form-group"><label>Test Name *</label><input type="text" name="test_name" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Code</label><input type="text" name="test_code"></div>
                    <div class="form-group"><label>Category</label><input type="text" name="category"></div>
                </div>
                <div class="form-group"><label>Parameters</label><input type="text" name="parameters" placeholder="e.g. RBC, WBC, Platelets"></div>
                <div class="form-row">
                    <div class="form-group"><label>B2B Price (₹) *</label><input type="number" name="b2b_price" step="0.01" min="0" required></div>
                    <div class="form-group"><label>Selling Price (₹)</label><input type="number" name="org_selling_price" step="0.01" min="0"></div>
                </div>
                <div class="form-group"><label>Est. Time</label><input type="text" name="estimated_time" placeholder="e.g. 24 hours"></div>
                <button type="submit" class="btn-primary">Add Test</button>
            </form>
        </div>

        @if($lab->users->count())
        <div class="card" style="margin-top:16px;">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">Lab Staff ({{ $lab->users->count() }})</h3>
            @foreach($lab->users as $u)
                <div style="padding:6px 0;font-size:13px;{{ !$loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }}">
                    {{ $u->name }} — {{ $u->email }}
                    <span style="background:{{ $u->is_active ? '#dcfce7' : '#fee2e2' }};color:{{ $u->is_active ? '#166534' : '#991b1b' }};padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;margin-left:8px;">
                        {{ $u->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
