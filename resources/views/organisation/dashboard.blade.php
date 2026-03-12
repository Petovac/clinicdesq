@extends('organisation.layout')

@section('content')

<style>
.dashboard-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:16px;
    margin-top:20px;
}

.card{
    background:#fff;
    padding:18px;
    border-radius:10px;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
}

.card h3{
    margin:0 0 8px;
    font-size:16px;
}

.card a{
    text-decoration:none;
    color:#4f46e5;
    font-weight:500;
}

.metrics-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
gap:16px;
margin-top:20px;
margin-bottom:25px;
}

.metric-card{
background:#fff;
padding:16px;
border-radius:10px;
box-shadow:0 6px 18px rgba(0,0,0,0.05);
}

.metric-card h4{
margin:0 0 6px;
font-size:13px;
color:#6b7280;
}

.metric-card .value{
font-size:22px;
font-weight:600;
}
</style>

<h2>Dashboard</h2>

<p><strong>Organisation:</strong> {{ $organisation->name }}</p>
<p><strong>Your Role:</strong> {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>

<hr>

<div class="metrics-grid">

@if($showAppointments)
<div class="metric-card">
<h4>Today's Appointments</h4>
<div class="value">{{ $todayAppointments }}</div>
</div>

<div class="metric-card">
<h4>Upcoming Appointments</h4>
<div class="value">{{ $upcomingAppointments }}</div>
</div>
@endif

@if($showClinics)
<div class="metric-card">
<h4>Total Clinics</h4>
<div class="value">{{ $totalClinics }}</div>
</div>
@endif

@if($showAppointments)
<div class="metric-card">
<h4>Doctors Linked</h4>
<div class="value">{{ $totalVets }}</div>
</div>
@endif

@if($showBilling)
<div class="metric-card">
<h4>Total Bills</h4>
<div class="value">{{ $totalBills }}</div>
</div>
@endif

</div>

<div class="dashboard-grid">

@if($showAppointments)
<div class="card">
    <h3>Appointments</h3>
    <a href="/clinic/appointments">Open module</a>
</div>
@endif

@if($showBilling)
<div class="card">
    <h3>Billing</h3>
    <a href="#">Open module</a>
</div>
@endif

@if($showInventory)
<div class="card">
    <h3>Inventory</h3>
    <a href="#">Open module</a>
</div>
@endif

@if($showClinics)
<div class="card">
    <h3>Clinics</h3>
    <a href="/organisation/clinics">Manage clinics</a>
</div>
@endif

@if($showUsers)
<div class="card">
    <h3>Users</h3>
    <a href="/organisation/users">Manage users</a>
</div>
@endif

@if($showReports)
<div class="card">
    <h3>Reports</h3>
    <a href="#">View reports</a>
</div>
@endif

</div>

@endsection