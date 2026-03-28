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
}
.btn {
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-primary { background: #4f46e5; color: #fff; }
.btn-primary:hover { background: #4338ca; }
.btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 5px; }
.btn-edit { background: #dbeafe; color: #1d4ed8; }
.btn-edit:hover { background: #bfdbfe; }

.card {
    background: #ffffff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.04);
}
.table-wrapper { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
thead { background: #f9fafb; }
thead th {
    text-align: left;
    padding: 12px;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    border-bottom: 1px solid #e5e7eb;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
tbody td {
    padding: 12px;
    font-size: 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
tbody tr:hover { background: #f9fafb; }

.badge {
    display: inline-block;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 999px;
}
.badge-assigned { background: #dcfce7; color: #166534; }
.badge-unassigned { background: #f3f4f6; color: #6b7280; }
.badge-gmb { background: #dbeafe; color: #1d4ed8; font-size: 10px; }

.clinic-name { font-weight: 600; color: #111827; }
.clinic-details { font-size: 12px; color: #6b7280; margin-top: 2px; }

.empty-state {
    text-align: center;
    padding: 40px 0;
    color: #6b7280;
    font-size: 14px;
}

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
    <h2>Clinics</h2>
    <a href="{{ route('organisation.clinics.create') }}" class="btn btn-primary">+ Add Clinic</a>
</div>

@if(session('success'))
<div class="success-bar">✓ {{ session('success') }}</div>
@endif

<div class="card table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Clinic</th>
                <th>Phone</th>
                <th>City</th>
                <th>Manager</th>
                <th>Google Reviews</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clinics as $clinic)
                <tr>
                    <td>
                        <div class="clinic-name">{{ $clinic->name }}</div>
                        @if($clinic->address)
                        <div class="clinic-details">{{ Str::limit($clinic->address, 40) }}</div>
                        @endif
                    </td>
                    <td>{{ $clinic->phone ?? '—' }}</td>
                    <td>{{ $clinic->city ?? '—' }}</td>
                    <td>
                        @if($clinic->user)
                            <span class="badge badge-assigned">{{ $clinic->user->name }}</span>
                        @else
                            <span class="badge badge-unassigned">Not Assigned</span>
                        @endif
                    </td>
                    <td>
                        @if($clinic->gmb_review_url)
                            <span class="badge badge-gmb">✓ Linked</span>
                        @else
                            <span style="color:#d1d5db;font-size:12px;">—</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <a href="{{ route('organisation.clinics.edit', $clinic) }}" class="btn btn-sm btn-edit">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">No clinics created yet</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
