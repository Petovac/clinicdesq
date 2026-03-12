@extends('admin.layout')


<style>
/* ===== Admin Form Styling ===== */

.admin-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 24px;
    max-width: 720px;
}

.admin-form-group {
    margin-bottom: 16px;
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
    border: 1px solid #d1d5db;
    border-radius: 6px;
    outline: none;
}

.admin-form-group input:focus,
.admin-form-group textarea:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 1px #6366f1;
}

.admin-help-text {
    font-size: 12px;
    color: #6b7280;
    margin-top: 4px;
}

.admin-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.admin-actions {
    margin-top: 24px;
}

.admin-btn-primary {
    background: #4f46e5;
    color: #ffffff;
    border: none;
    padding: 10px 18px;
    font-size: 14px;
    border-radius: 6px;
    cursor: pointer;
}

.admin-btn-primary:hover {
    background: #4338ca;
}
</style>

@section('content')
<h1 style="font-size:22px; font-weight:600; margin-bottom:20px;">
    Create Clinic
</h1>

<div class="admin-card">

    @if ($errors->any())
        <div style="
            background:#fee2e2;
            border:1px solid #fca5a5;
            color:#7f1d1d;
            padding:12px;
            border-radius:6px;
            margin-bottom:16px;
            font-size:14px;
        ">
            <ul style="margin-left:16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST" action="{{ url('/admin/clinics') }}">
        @csrf

        <div class="admin-form-group">
            <label>Clinic Name *</label>
            <input type="text" name="name" required>
        </div>

        <div class="admin-form-group">
            <label>Clinic Email (Login) *</label>
            <input type="email" name="email" required>
        </div>

        <div class="admin-form-group">
            <label>Password (optional)</label>
            <input type="text" name="password" placeholder="Leave empty to auto-generate">
            <div class="admin-help-text">
                If left empty, a secure password will be generated and shown to admin.
            </div>
        </div>

        <div class="admin-form-group">
            <label>Phone</label>
            <input type="text" name="phone">
        </div>

        <div class="admin-form-group">
            <label>Address</label>
            <textarea name="address" rows="3"></textarea>
        </div>

        <div class="admin-grid">
            <div class="admin-form-group">
                <label>City</label>
                <input type="text" name="city">
            </div>

            <div class="admin-form-group">
                <label>State</label>
                <input type="text" name="state">
            </div>

            <div class="admin-form-group">
                <label>Pincode</label>
                <input type="text" name="pincode">
            </div>
        </div>

        <div class="admin-form-group">
            <label>GST Number</label>
            <input type="text" name="gst_number">
        </div>

        <div class="admin-actions">
            <button type="submit" class="admin-btn-primary">
                Create Clinic
            </button>
        </div>

    </form>
</div>
@endsection
