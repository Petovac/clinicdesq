@extends('organisation.layout')

@section('content')

<style>
/* ===== Base ===== */
body {
    background: #f5f7fb;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #2c3e50;
}

/* ===== Page Header ===== */
.page-header {
    margin-bottom: 24px;
}

.page-header h2 {
    font-size: 22px;
    font-weight: 600;
    margin: 0;
}

/* ===== Card ===== */
.card {
    background: #ffffff;
    border-radius: 10px;
    padding: 24px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.04);
    max-width: 600px;
}

/* ===== Form ===== */
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

input, textarea {
    width: 100%;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    font-size: 14px;
    transition: border 0.2s, box-shadow 0.2s;
}

input:focus, textarea:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}

textarea {
    min-height: 80px;
}

/* ===== Buttons ===== */
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

.btn-primary {
    background: #4f46e5;
    color: #fff;
}

.btn-primary:hover {
    background: #4338ca;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}
</style>

<div class="page-header">
    <h2>Create Clinic</h2>
</div>

<div class="card">
    <form method="POST" action="{{ route('organisation.clinics.store') }}">
        @csrf

        <div class="form-group">
            <label>Clinic Name *</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email">
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address"></textarea>
        </div>

        @php $locStates = config('locations.states', []); $locCities = config('locations.cities', []); @endphp
        <div class="form-group">
            <label>State</label>
            <select name="state" id="cc-state" style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #d1d5db;font-size:14px;">
                <option value="">Select State</option>
                @foreach($locStates as $s)<option value="{{ $s }}" {{ old('state') === $s ? 'selected' : '' }}>{{ $s }}</option>@endforeach
            </select>
        </div>

        <div class="form-group">
            <label>City</label>
            <select name="city" id="cc-city" style="width:100%;padding:10px 12px;border-radius:6px;border:1px solid #d1d5db;font-size:14px;">
                <option value="">Select City</option>
            </select>
            <script>(function(){var cm=@json($locCities),ss=document.getElementById('cc-state'),cs=document.getElementById('cc-city');ss.addEventListener('change',function(){cs.innerHTML='<option value="">Select City</option>';(cm[this.value]||[]).forEach(function(c){var o=document.createElement('option');o.value=c;o.textContent=c;cs.appendChild(o);});});})();</script>
        </div>

        <div class="form-group">
            <label>Pincode</label>
            <input type="text" name="pincode">
        </div>

        <div class="form-group">
            <label>GST Number</label>
            <input type="text" name="gst_number">
        </div>

        <div class="form-group">
            <label>Google Reviews URL</label>
            <input type="url" name="gmb_review_url" placeholder="https://g.page/r/your-clinic/review">
            <small style="color:#6b7280;font-size:11px;">Paste your Google My Business review link. Happy clients will be prompted to leave a Google review.</small>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">Create Clinic</button>
            <a href="{{ route('organisation.clinics.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection
