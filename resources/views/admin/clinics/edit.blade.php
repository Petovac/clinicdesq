@extends('admin.layout')

<style>
/* ==============================
   ADMIN FORM – CLEAN UI
============================== */

.admin-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 28px;
    max-width: 760px;
}

.admin-form-group {
    margin-bottom: 18px;
}

.admin-form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
}

.admin-form-group input,
.admin-form-group textarea {
    width: 100%;
    padding: 10px 12px;
    font-size: 14px;
    color: #111827;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    outline: none;
    transition: border 0.2s ease, box-shadow 0.2s ease;
}

.admin-form-group textarea {
    resize: vertical;
}

.admin-form-group input:focus,
.admin-form-group textarea:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 1px #6366f1;
}

/* Help / hint text */
.admin-help-text {
    font-size: 12px;
    color: #6b7280;
    margin-top: 5px;
}

/* Grid for city/state/pincode */
.admin-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

/* Buttons */
.admin-actions {
    margin-top: 26px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.admin-btn-primary {
    background: #4f46e5;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s ease;
}

.admin-btn-primary:hover {
    background: #4338ca;
}

.admin-btn-secondary {
    background: #ffffff;
    color: #374151;
    border: 1px solid #d1d5db;
    padding: 10px 18px;
    font-size: 14px;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
}

.admin-btn-secondary:hover {
    background: #f9fafb;
}

/* Page heading */
.admin-page-title {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #111827;
}
</style>


@section('content')
<h1 class="admin-page-title">
    Edit Clinic
</h1>

<div class="admin-card">

    <form method="POST" action="{{ url('/admin/clinics/'.$clinic->id) }}">
        @csrf
        @method('PUT')

        <div class="admin-form-group">
            <label>Clinic Name *</label>
            <input type="text" name="name" value="{{ old('name', $clinic->name) }}" required>
        </div>

        <div class="admin-form-group">
            <label>Clinic Email (Login) *</label>
            <input type="email" name="email" value="{{ old('email', $clinic->user->email) }}" required>
        </div>

        <div class="admin-form-group">
            <label>Reset Password (optional)</label>
            <input type="text" name="password" placeholder="Leave empty to keep existing password">
            <div class="admin-help-text">
                Enter only if you want to reset clinic login password.
            </div>
        </div>

        <div class="admin-form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $clinic->phone) }}">
        </div>

        <div class="admin-form-group">
            <label>Address</label>
            <textarea name="address" rows="3">{{ old('address', $clinic->address) }}</textarea>
        </div>

        <div class="admin-grid">
            <div class="admin-form-group">
                <label>City</label>
                <input type="text" name="city" value="{{ old('city', $clinic->city) }}">
            </div>

            <div class="admin-form-group">
                <label>State</label>
                <input type="text" name="state" value="{{ old('state', $clinic->state) }}">
            </div>

            <div class="admin-form-group">
                <label>Pincode</label>
                <input type="text" name="pincode" value="{{ old('pincode', $clinic->pincode) }}">
            </div>
        </div>

        <div class="admin-form-group">
            <label>GST Number</label>
            <input type="text" name="gst_number" value="{{ old('gst_number', $clinic->gst_number) }}">
        </div>

        <div class="admin-actions">
            <a href="{{ url('/admin/clinics') }}" class="admin-btn-secondary">
                Cancel
            </a>

            <button type="submit" class="admin-btn-primary">
                Update Clinic
            </button>
        </div>

    </form>
</div>
@endsection
