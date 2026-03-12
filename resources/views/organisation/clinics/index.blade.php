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

/* ===== Buttons ===== */
.btn {
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    border: none;
}

.btn-primary {
    background: #4f46e5;
    color: #fff;
}

.btn-primary:hover {
    background: #4338ca;
}

/* ===== Card ===== */
.card {
    background: #ffffff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.04);
}

/* ===== Table ===== */
.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: #f9fafb;
}

thead th {
    text-align: left;
    padding: 14px 12px;
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    border-bottom: 1px solid #e5e7eb;
}

tbody td {
    padding: 14px 12px;
    font-size: 14px;
    border-bottom: 1px solid #f1f5f9;
}

tbody tr:hover {
    background: #f9fafb;
}

/* ===== Badge ===== */
.badge {
    display: inline-block;
    padding: 4px 10px;
    font-size: 12px;
    border-radius: 999px;
    background: #e0e7ff;
    color: #3730a3;
}

/* ===== Empty State ===== */
.empty-state {
    text-align: center;
    padding: 40px 0;
    color: #6b7280;
    font-size: 14px;
}
</style>

<div class="page-header">
    <h2>Clinics</h2>

    <a href="{{ route('organisation.clinics.create') }}" class="btn btn-primary">
        + Add Clinic
    </a>
</div>

<div class="card table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Clinic Name</th>
                <th>City</th>
                <th>Manager</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clinics as $clinic)
                <tr>
                    <td>{{ $clinic->name }}</td>
                    <td>{{ $clinic->city ?? '-' }}</td>
                    <td>
                        @if($clinic->user)
                            <span class="badge">{{ $clinic->user->name }}</span>
                        @else
                            <span class="badge">Not Assigned</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty-state">
                        No clinics created yet
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
