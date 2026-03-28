@extends('organisation.layout')

@section('content')

<style>
.page-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
}
.page-header h2 {
    font-size: 22px;
    font-weight: 600;
    margin: 0;
}
.card {
    background: #ffffff;
    border-radius: 10px;
    padding: 24px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.04);
    max-width: 600px;
}
.form-group {
    margin-bottom: 18px;
}
label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 6px;
    color: #374151;
}
input, textarea, select {
    width: 100%;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    font-size: 14px;
    transition: border 0.2s, box-shadow 0.2s;
}
input:focus, textarea:focus, select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}
textarea { min-height: 80px; }
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}
.btn {
    padding: 10px 18px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    text-decoration: none;
}
.btn-primary { background: #4f46e5; color: #fff; }
.btn-primary:hover { background: #4338ca; }
.btn-secondary { background: #e5e7eb; color: #374151; }
.btn-secondary:hover { background: #d1d5db; }
.form-hint { font-size: 11px; color: #6b7280; margin-top: 3px; }
.success-bar {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    padding: 10px 14px;
    border-radius: 6px;
    margin-bottom: 16px;
    color: #166534;
    font-size: 14px;
}
</style>

<div class="page-header">
    <a href="{{ route('organisation.clinics.index') }}" style="color:#6b7280;text-decoration:none;font-size:18px;">←</a>
    <h2>Edit Clinic — {{ $clinic->name }}</h2>
</div>

@if(session('success'))
<div class="success-bar">✓ {{ session('success') }}</div>
@endif

@if($errors && $errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;padding:10px 14px;border-radius:6px;margin-bottom:16px;color:#991b1b;font-size:14px;">
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<div class="card">
    <form method="POST" action="{{ route('organisation.clinics.update', $clinic) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Clinic Name *</label>
            <input type="text" name="name" value="{{ old('name', $clinic->name) }}" required>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $clinic->phone) }}">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $clinic->email) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address">{{ old('address', $clinic->address) }}</textarea>
        </div>

        @php $locStates = config('locations.states', []); $locCities = config('locations.cities', []); @endphp
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div class="form-group">
                <label>State</label>
                <select name="state" id="ce-state">
                    <option value="">Select State</option>
                    @foreach($locStates as $s)
                    <option value="{{ $s }}" {{ old('state', $clinic->state) === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>City</label>
                <select name="city" id="ce-city">
                    <option value="">Select City</option>
                </select>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div class="form-group">
                <label>Pincode</label>
                <input type="text" name="pincode" value="{{ old('pincode', $clinic->pincode) }}" maxlength="10">
            </div>
            <div class="form-group">
                <label>GST Number</label>
                <input type="text" name="gst_number" value="{{ old('gst_number', $clinic->gst_number) }}">
            </div>
        </div>

        <div class="form-group" style="border-top:1px solid #f3f4f6;padding-top:18px;margin-top:6px;">
            <label>Google My Business — Review URL</label>
            <input type="url" name="gmb_review_url" value="{{ old('gmb_review_url', $clinic->gmb_review_url) }}" placeholder="https://g.page/r/your-clinic/review">
            <div class="form-hint">Clients who rate 4-5 stars internally will be prompted to leave a Google review at this link.</div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('organisation.clinics.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
(function(){
    var cm = @json($locCities);
    var ss = document.getElementById('ce-state');
    var cs = document.getElementById('ce-city');
    var currentCity = @json(old('city', $clinic->city));

    function loadCities() {
        cs.innerHTML = '<option value="">Select City</option>';
        (cm[ss.value] || []).forEach(function(c) {
            var o = document.createElement('option');
            o.value = c;
            o.textContent = c;
            if (c === currentCity) o.selected = true;
            cs.appendChild(o);
        });
    }

    ss.addEventListener('change', function() { currentCity = ''; loadCities(); });
    if (ss.value) loadCities();
})();
</script>

@endsection
