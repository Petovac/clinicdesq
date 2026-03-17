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
            @php $locStates = config('locations.states', []); $locCities = config('locations.cities', []); $eState = old('state', $lab->state); $eCity = old('city', $lab->city); @endphp
            <div class="form-row">
                <div class="form-group"><label>State</label>
                    <select name="state" id="le-state" style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
                        <option value="">Select State</option>
                        @foreach($locStates as $s)<option value="{{ $s }}" {{ $eState === $s ? 'selected' : '' }}>{{ $s }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group"><label>City</label>
                    <select name="city" id="le-city" style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
                        <option value="">Select City</option>
                        @if($eState && isset($locCities[$eState]))@foreach($locCities[$eState] as $c)<option value="{{ $c }}" {{ $eCity === $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach @endif
                    </select>
                    <script>(function(){var cm=@json($locCities),ss=document.getElementById('le-state'),cs=document.getElementById('le-city'),sv=@json($eCity);ss.addEventListener('change',function(){cs.innerHTML='<option value="">Select City</option>';(cm[this.value]||[]).forEach(function(c){var o=document.createElement('option');o.value=c;o.textContent=c;if(c===sv)o.selected=true;cs.appendChild(o);});});})();</script>
                </div>
                <div class="form-group"><label>Pincode</label><input type="text" name="pincode" value="{{ old('pincode', $lab->pincode) }}"></div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Update Lab</button>
            </div>
        </form>
    </div>

    {{-- Right: Test offerings --}}
    <div>
        {{-- Import Tests Button --}}
        <div class="card" style="background:#f0f9ff;border-color:#bae6fd;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:#0c4a6e;">Import Lab's Test Catalog</div>
                    <div style="font-size:12px;color:#0369a1;margin-top:2px;">{{ $masterTestCount }} tests available from this lab. Import and set your selling prices.</div>
                </div>
                <form method="POST" action="{{ route('organisation.labs.import-tests', $lab) }}">
                    @csrf
                    <button type="submit" style="background:#0284c7;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">Import Tests</button>
                </form>
            </div>
        </div>

        <div class="card" style="margin-top:16px;">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:14px;">Test Offerings ({{ $lab->testOfferings->count() }})</h3>
            @if($lab->testOfferings->count())
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="border-bottom:1px solid #e5e7eb;">
                            <th style="padding:8px 0;text-align:left;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">Test</th>
                            <th style="padding:8px 0;text-align:left;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">Parameters</th>
                            <th style="padding:8px 0;text-align:right;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">B2B Price</th>
                            <th style="padding:8px 0;text-align:right;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">Your Selling Price</th>
                            <th style="padding:8px 0;text-align:right;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lab->testOfferings as $test)
                            <tr style="border-bottom:1px solid #f3f4f6;">
                                <td style="padding:10px 0;">
                                    <strong>{{ $test->test_name }}</strong>
                                    @if($test->estimated_time)<div style="font-size:11px;color:#6b7280;">{{ $test->estimated_time }}</div>@endif
                                </td>
                                <td style="padding:10px 0;font-size:11px;color:#6b7280;">
                                    {{ is_array($test->parameters) ? implode(', ', $test->parameters) : ($test->parameters ?? '—') }}
                                </td>
                                <td style="padding:10px 0;text-align:right;color:#6b7280;">₹{{ number_format($test->b2b_price, 2) }}</td>
                                <td style="padding:10px 0;text-align:right;">
                                    <form method="POST" action="{{ route('organisation.labs.test.update-price', $test) }}" style="display:inline-flex;align-items:center;gap:4px;">
                                        @csrf @method('PUT')
                                        <span style="color:#6b7280;">₹</span>
                                        <input type="number" name="org_selling_price" value="{{ $test->org_selling_price ?? $test->b2b_price }}" step="0.01" min="0"
                                            style="width:90px;padding:5px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;text-align:right;font-weight:600;">
                                        <button type="submit" style="background:#2563eb;color:#fff;border:none;padding:5px 10px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;">Set</button>
                                    </form>
                                </td>
                                <td style="padding:10px 0;text-align:right;">
                                    @php
                                        $sell = $test->org_selling_price ?? $test->b2b_price;
                                        $margin = $sell - $test->b2b_price;
                                    @endphp
                                    <span style="color:{{ $margin > 0 ? '#16a34a' : ($margin < 0 ? '#dc2626' : '#6b7280') }};font-weight:600;">
                                        {{ $margin >= 0 ? '+' : '' }}₹{{ number_format($margin, 2) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color:#6b7280;text-align:center;padding:20px 0;">No tests imported yet. Click "Import Tests" above.</p>
            @endif
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
