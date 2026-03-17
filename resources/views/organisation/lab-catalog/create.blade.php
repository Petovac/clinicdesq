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
    max-width: 640px;
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
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    box-sizing: border-box;
}
.form-group input:focus,
.form-group select:focus {
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
    <h2>Add Lab Test</h2>
    <a href="{{ route('organisation.lab-catalog.index') }}" class="back-link">&larr; Back to Catalog</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('organisation.lab-catalog.store') }}">
        @csrf

        <div class="form-group">
            <label>Test Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Code</label>
                <input type="text" name="code" value="{{ old('code') }}">
                @error('code') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category">
                    <option value="">-- Select --</option>
                    @foreach(['hematology','biochemistry','urinalysis','serology','cytology','histopathology','microbiology','immunology','endocrinology','parasitology','other'] as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
                @error('category') <div class="error-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Sample Type</label>
                <select name="sample_type">
                    <option value="">-- Select --</option>
                    @foreach(['blood','serum','plasma','urine','swab','tissue','fluid','feces','other'] as $type)
                        <option value="{{ $type }}" {{ old('sample_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
                @error('sample_type') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Estimated Time</label>
                <input type="text" name="estimated_time" value="{{ old('estimated_time') }}" placeholder="e.g. 2 hours">
                @error('estimated_time') <div class="error-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label>Parameters</label>
            <input type="text" name="parameters" value="{{ old('parameters') }}" placeholder="e.g. RBC, WBC, Platelets, Hb">
            @error('parameters') <div class="error-text">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Price (&#8377;)</label>
                <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0">
                @error('price') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Cost Price (&#8377;)</label>
                <input type="number" name="cost_price" value="{{ old('cost_price') }}" step="0.01" min="0">
                @error('cost_price') <div class="error-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Test</button>
            <a href="{{ route('organisation.lab-catalog.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
