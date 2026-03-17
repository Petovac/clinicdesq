@extends('organisation.layout')

@section('content')

<style>
.page-header {
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
    margin-bottom: 20px;
}
.card h3 {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 16px 0;
}
.form-group {
    margin-bottom: 14px;
}
.form-group label {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 4px;
    display: block;
}
.form-group input,
.form-group select {
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
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
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
.btn-sm {
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    border: none;
    cursor: pointer;
}
.btn-activate {
    background: #dcfce7;
    color: #166534;
}
.btn-activate:hover { background: #bbf7d0; }
.btn-deactivate {
    background: #fee2e2;
    color: #991b1b;
}
.btn-deactivate:hover { background: #fecaca; }
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
thead th {
    background: #f9fafb;
    text-transform: uppercase;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-align: left;
    padding: 10px 14px;
    border-bottom: 1px solid #e5e7eb;
}
tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f1f5f9;
    color: #111827;
}
tbody tr:hover { background: #f9fafb; }
.badge-active {
    background: #dcfce7;
    color: #166534;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    display: inline-block;
}
.badge-inactive {
    background: #fee2e2;
    color: #991b1b;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    display: inline-block;
}
.error-text {
    color: #dc2626;
    font-size: 11px;
    margin-top: 4px;
}
.empty-state {
    text-align: center;
    padding: 40px 0;
    color: #6b7280;
    font-size: 14px;
}
</style>

<div class="page-header">
    <h2>Lab Technicians</h2>
</div>

@if(session('success'))
    <div style="background:#dcfce7; color:#166534; padding:10px 16px; border-radius:8px; margin-bottom:16px; font-size:13px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background:#fee2e2; color:#991b1b; padding:10px 16px; border-radius:8px; margin-bottom:16px; font-size:13px;">
        {{ session('error') }}
    </div>
@endif

{{-- Create New Lab Tech --}}
<div class="card">
    <h3>Add New Lab Technician</h3>
    <form method="POST" action="{{ route('organisation.lab-techs.store') }}">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
                @error('name') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
                @error('email') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required>
                @error('password') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}">
                @error('phone') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Clinic *</label>
                <select name="clinic_id" required>
                    <option value="">-- Select Clinic --</option>
                    @foreach($clinics as $clinic)
                        <option value="{{ $clinic->id }}" {{ old('clinic_id') == $clinic->id ? 'selected' : '' }}>{{ $clinic->name }}</option>
                    @endforeach
                </select>
                @error('clinic_id') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Role *</label>
                <select name="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="lab_tech" {{ old('role') == 'lab_tech' ? 'selected' : '' }}>Lab Technician</option>
                    <option value="lab_admin" {{ old('role') == 'lab_admin' ? 'selected' : '' }}>Lab Admin</option>
                </select>
                @error('role') <div class="error-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="margin-top:12px;">
            <button type="submit" class="btn-primary">Create Lab Tech</button>
        </div>
    </form>
</div>

{{-- Existing Lab Techs --}}
<div class="card" style="overflow-x:auto;">
    <h3>Existing Lab Technicians</h3>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Clinic</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($labTechs as $labUser)
                <tr>
                    <td style="font-weight:500;">{{ $labUser->name }}</td>
                    <td>{{ $labUser->email }}</td>
                    <td>{{ $labUser->clinic->name ?? '-' }}</td>
                    <td style="text-transform:capitalize;">{{ str_replace('_', ' ', $labUser->role ?? '-') }}</td>
                    <td>
                        @if($labUser->is_active)
                            <span class="badge-active">Active</span>
                        @else
                            <span class="badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('organisation.lab-techs.toggle', $labUser) }}" style="display:inline;">
                            @csrf
                            @if($labUser->is_active)
                                <button type="submit" class="btn-sm btn-deactivate">Deactivate</button>
                            @else
                                <button type="submit" class="btn-sm btn-activate">Activate</button>
                            @endif
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">No lab technicians added yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
