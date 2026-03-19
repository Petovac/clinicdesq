@extends('organisation.layout')

@section('content')

<style>
.dashboard-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 24px;
}
.dashboard-header h2 { font-size: 24px; font-weight: 700; color: #111827; margin: 0; }
.org-info { font-size: 13px; color: #6b7280; margin-top: 4px; }
.org-info strong { color: #374151; }

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.metric-card {
    background: #fff;
    padding: 18px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    border: 1px solid #f3f4f6;
}
.metric-card h4 {
    margin: 0 0 6px;
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.metric-card .value {
    font-size: 28px;
    font-weight: 700;
    color: #111827;
}

.modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
}
.module-card {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    border: 1px solid #f3f4f6;
    transition: all 0.15s;
}
.module-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); border-color: #e0e7ff; }
.module-card h3 { margin: 0 0 6px; font-size: 15px; font-weight: 700; color: #111827; }
.module-card p { margin: 0 0 12px; font-size: 12px; color: #6b7280; }
.module-card a {
    text-decoration: none; color: #4f46e5; font-weight: 600; font-size: 13px;
}
.module-card a:hover { text-decoration: underline; }
.section-label {
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
    color: #9ca3af; margin-bottom: 12px; margin-top: 8px;
}
</style>

<div class="dashboard-header">
    <div>
        <h2>Dashboard</h2>
        <div class="org-info">
            <strong>{{ $organisation->name }}</strong> &middot; {{ ucfirst(str_replace('_', ' ', $user->role)) }}
        </div>
    </div>
</div>

{{-- Metrics --}}
<div class="metrics-grid">
    @if($showClinics)
    <div class="metric-card">
        <h4>Clinics</h4>
        <div class="value">{{ $totalClinics }}</div>
    </div>
    @endif

    <div class="metric-card">
        <h4>Doctors Linked</h4>
        <div class="value">{{ $totalVets }}</div>
    </div>

    <div class="metric-card">
        <h4>Staff Users</h4>
        <div class="value">{{ $totalUsers ?? 0 }}</div>
    </div>

    @if($showBilling)
    <div class="metric-card">
        <h4>Total Bills</h4>
        <div class="value">{{ $totalBills }}</div>
    </div>
    @endif
</div>

{{-- Quick Access Modules --}}
<div class="section-label">Quick Access</div>
<div class="modules-grid">

    @if($showClinics)
    <div class="module-card">
        <h3>Clinics</h3>
        <p>Manage your clinic locations and settings</p>
        <a href="{{ route('organisation.clinics.index') }}">Manage clinics &rarr;</a>
    </div>
    @endif

    @if($showUsers)
    <div class="module-card">
        <h3>Users & Roles</h3>
        <p>Create staff accounts and assign permissions</p>
        <a href="{{ route('organisation.users.index') }}">Manage users &rarr;</a>
    </div>
    @endif

    @if($showInventory)
    <div class="module-card">
        <h3>Inventory</h3>
        <p>Central stock management and clinic transfers</p>
        <a href="{{ route('organisation.inventory.index') }}">Manage inventory &rarr;</a>
    </div>
    @endif

    <div class="module-card">
        <h3>Lab Management</h3>
        <p>Test catalog, external labs, and lab technicians</p>
        <a href="{{ route('organisation.lab-catalog.index') }}">Open lab module &rarr;</a>
    </div>

    <div class="module-card">
        <h3>Pricing</h3>
        <p>Price lists, consultation fees, and lab test pricing</p>
        <a href="{{ route('organisation.pricelist.index') }}">Manage pricing &rarr;</a>
    </div>

    <div class="module-card">
        <h3>Branding & Templates</h3>
        <p>Logo, templates for prescriptions, case sheets, bills</p>
        <a href="{{ route('organisation.settings.branding') }}">Customize branding &rarr;</a>
    </div>

    <div class="module-card">
        <h3>Vets</h3>
        <p>View and manage veterinarians linked to your clinics</p>
        <a href="{{ route('organisation.vets.index') }}">View vets &rarr;</a>
    </div>

    <div class="module-card">
        <h3>Roles & Permissions</h3>
        <p>Define custom roles and fine-tune access control</p>
        <a href="{{ route('organisation.roles.index') }}">Manage roles &rarr;</a>
    </div>

</div>

@endsection
