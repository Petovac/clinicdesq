@extends('organisation.layout')

@section('content')

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.page-header h2 {
    font-size: 22px;
    font-weight: 600;
    margin: 0;
    color: #111827;
}
.card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    max-width: 600px;
}
.form-group {
    margin-bottom: 16px;
}
.form-group label {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 4px;
    display: block;
}
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    box-sizing: border-box;
}
.form-group textarea {
    min-height: 70px;
    resize: vertical;
}
.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
}
.form-row {
    display: flex;
    gap: 16px;
}
.form-row .form-group {
    flex: 1;
}
.btn-primary {
    background: #2563eb;
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
}
.btn-primary:hover { background: #1d4ed8; }
.btn-secondary {
    background: #e5e7eb;
    color: #374151;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
}
.btn-secondary:hover { background: #d1d5db; }
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 20px;
}
.error-text {
    color: #dc2626;
    font-size: 11px;
    margin-top: 4px;
}
.back-link {
    color: #6b7280;
    text-decoration: none;
    font-size: 13px;
}
.back-link:hover { color: #2563eb; }
</style>

<div class="page-header">
    <h2>Onboard External Lab</h2>
    <a href="{{ route('organisation.labs.index') }}" class="back-link">&larr; Back to Labs</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('organisation.labs.store') }}">
        @csrf

        <div class="form-group">
            <label>Lab Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}">
                @error('phone') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}">
                @error('email') <div class="error-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address">{{ old('address') }}</textarea>
            @error('address') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" value="{{ old('city') }}">
                @error('city') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>State</label>
                <input type="text" name="state" value="{{ old('state') }}">
                @error('state') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Pincode</label>
                <input type="text" name="pincode" value="{{ old('pincode') }}">
                @error('pincode') <div class="error-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Lab</button>
            <a href="{{ route('organisation.labs.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
