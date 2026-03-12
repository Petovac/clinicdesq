@extends('admin.layout')

<style>
/* =========================
   SUPER ADMIN DASHBOARD
========================= */

.admin-dashboard {
    max-width: 1100px;
}

/* Page title */
.admin-dashboard h2 {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 24px;
    color: #111827;
}

/* Stat cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 32px;
}

.stat-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 20px;
}

.stat-label {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 6px;
}

.stat-value {
    font-size: 28px;
    font-weight: 600;
    color: #111827;
}

/* Section headings */
.section-title {
    font-size: 18px;
    font-weight: 500;
    margin-bottom: 12px;
    color: #111827;
}

/* Table styling */
.admin-table {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}

.admin-table thead {
    background: #f9fafb;
}

.admin-table th {
    text-align: left;
    padding: 12px 14px;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
}

.admin-table td {
    padding: 12px 14px;
    font-size: 14px;
    color: #111827;
    border-bottom: 1px solid #f1f5f9;
}

.admin-table tr:last-child td {
    border-bottom: none;
}

.admin-table tr:hover {
    background: #f9fafb;
}

/* Empty state */
.empty-state {
    padding: 20px;
    font-size: 14px;
    color: #6b7280;
}
</style>


@section('content')

<div class="admin-dashboard">

    <h2>Super Admin Dashboard</h2>

    {{-- Compiled Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Clinics Registered</div>
            <div class="stat-value">{{ $totalClinics }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Vets Registered</div>
            <div class="stat-value">{{ $totalVets }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Active Clinics</div>
            <div class="stat-value">{{ $activeClinics }}</div>
        </div>
    </div>

    {{-- Recent Clinics --}}
    <div>
        <h3 class="section-title">Recently Added Clinics</h3>

        @if($recentClinics->count())
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Clinic Name</th>
                        <th>City</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentClinics as $clinic)
                        <tr>
                            <td>{{ $clinic->name }}</td>
                            <td>{{ $clinic->city ?? '-' }}</td>
                            <td>{{ $clinic->phone ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                No clinics added yet.
            </div>
        @endif
    </div>

</div>

@endsection
