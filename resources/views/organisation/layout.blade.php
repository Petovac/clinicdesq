<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Organisation Panel</title>

<style>

*{
box-sizing:border-box;
}

body{
margin:0;
font-family:Inter,system-ui,Arial,sans-serif;
background:#f3f4f6;
color:#111827;
}

/* ===== Layout ===== */

.wrapper{
display:flex;
min-height:100vh;
}

/* ===== Sidebar ===== */

.sidebar{
width:240px;
background:#111827;
color:#fff;
display:flex;
flex-direction:column;
}

.logo{
padding:18px 20px;
font-weight:700;
font-size:16px;
border-bottom:1px solid #1f2937;
letter-spacing:0.4px;
}

/* Navigation */

.nav{
padding:14px;
flex:1;
}

.nav a{
display:flex;
align-items:center;
padding:10px 12px;
margin-bottom:6px;
text-decoration:none;
color:#9ca3af;
border-radius:6px;
font-size:14px;
transition:all .15s ease;
}

.nav a:hover{
background:#1f2937;
color:#fff;
}

.nav a.active{
background:#2563eb;
color:#fff;
font-weight:500;
}

/* Section divider */

.nav-section{
margin-top:18px;
padding-top:12px;
border-top:1px solid #1f2937;
}

/* Parent menu — collapsible toggle */

.nav-parent{
display:flex;
align-items:center;
justify-content:space-between;
padding:10px 12px;
font-size:13px;
font-weight:600;
color:#d1d5db;
letter-spacing:0.3px;
cursor:pointer;
user-select:none;
border-radius:6px;
transition:background 0.15s;
}
.nav-parent:hover{
background:rgba(255,255,255,0.05);
}
.nav-parent::after{
content:'›';
font-size:16px;
font-weight:400;
color:#6b7280;
transition:transform 0.2s;
}
.nav-section.open .nav-parent::after{
transform:rotate(90deg);
}

/* Children — collapsed by default */

.nav-children{
display:none;
overflow:hidden;
}
.nav-section.open .nav-children{
display:block;
}

.nav-children a{
padding-left:28px;
font-size:13px;
}

/* ===== Main Area ===== */

.main{
flex:1;
display:flex;
flex-direction:column;
}

/* ===== Topbar ===== */

.topbar{
height:56px;
background:#fff;
border-bottom:1px solid #e5e7eb;
display:flex;
align-items:center;
justify-content:space-between;
padding:0 22px;
}

.page-title{
font-weight:600;
font-size:16px;
}

.user-box{
font-size:14px;
color:#6b7280;
}

/* ===== Content ===== */

.content{
padding:28px;
}

/* ===== Card UI ===== */

.card{
background:#fff;
border-radius:10px;
padding:24px;
border:1px solid #e5e7eb;
box-shadow:0 1px 2px rgba(0,0,0,0.03);
margin-bottom:24px;
}

</style>
</head>

<body>

<div class="wrapper">

<!-- Sidebar -->

<aside class="sidebar">

<div class="logo">
Clinicdesq
</div>

<div class="nav">

{{-- Core --}}
<div class="nav-section" style="margin-top:0;padding-top:0;border-top:none;">
<div class="nav-parent">Organisation</div>
<div class="nav-children">

<a href="{{ route('organisation.dashboard') }}"
class="{{ request()->is('organisation/dashboard') ? 'active' : '' }}">
Dashboard
</a>

@if(auth()->user()->hasPermission('clinics.view'))
<a href="{{ route('organisation.clinics.index') }}"
class="{{ request()->is('organisation/clinics*') ? 'active' : '' }}">
Clinics
</a>
@endif

@if(auth()->user()->hasPermission('roles.view'))
<a href="{{ route('organisation.roles.index') }}"
class="{{ request()->is('organisation/roles*') ? 'active' : '' }}">
Roles
</a>
@endif

@if(auth()->user()->hasPermission('users.view'))
<a href="{{ route('organisation.users.index') }}"
class="{{ request()->is('organisation/users*') ? 'active' : '' }}">
Users
</a>
@endif

@if(auth()->user()->hasPermission('vets.view'))
<a href="{{ route('organisation.vets.index') }}"
class="{{ request()->is('organisation/vets*') ? 'active' : '' }}">
Vets
</a>
@endif

</div>
</div>


@if(auth()->user()->organisation->hasModule('inventory') && (auth()->user()->hasPermission('inventory.view') || auth()->user()->hasPermission('inventory.manage')))

<div class="nav-section">

<div class="nav-parent">
Inventory
</div>

<div class="nav-children">

@if(auth()->user()->hasPermission('inventory.manage'))
<a href="{{ route('organisation.inventory.items') }}"
class="{{ request()->is('organisation/inventory-items*') ? 'active' : '' }}">
Inventory Items
</a>

<a href="{{ route('organisation.inventory.stock') }}"
class="{{ request()->is('organisation/inventory-stock*') ? 'active' : '' }}">
Stock Management
</a>

@php $firstClinic = \App\Models\Clinic::where('organisation_id', auth()->user()->organisation_id)->orderBy('name')->first(); @endphp
@if($firstClinic)
<a href="{{ route('organisation.inventory.clinic-overview', $firstClinic->id) }}"
class="{{ request()->is('organisation/clinic-inventory*') ? 'active' : '' }}">
Clinic Inventory
</a>
@endif
@endif

@if(auth()->user()->hasPermission('inventory.transfer'))
<a href="{{ route('organisation.inventory.transfer') }}"
class="{{ request()->is('organisation/inventory-transfer') ? 'active' : '' }}">
Stock Transfer
</a>
@endif

@if(auth()->user()->hasPermission('inventory.movements.view'))
<a href="{{ route('organisation.inventory.movements') }}"
class="{{ request()->is('organisation/inventory-movements*') ? 'active' : '' }}">
Inventory Log
</a>
@endif

</div>

</div>

@endif


@if(auth()->user()->organisation->hasModule('billing') && (auth()->user()->hasPermission('pricing.view') || auth()->user()->hasPermission('pricing.manage')))

<div class="nav-section">

<div class="nav-parent">
Pricing
</div>

<div class="nav-children">

<a href="{{ route('organisation.price-lists.index') }}"
class="{{ request()->is('organisation/price-lists*') ? 'active' : '' }}">
Price List
</a>

@if(auth()->user()->hasPermission('pricing.manage'))
<a href="{{ route('organisation.fee-config.index') }}"
class="{{ request()->is('organisation/fee-config*') ? 'active' : '' }}">
Fee Configuration
</a>
@endif

</div>

</div>

@endif

{{-- Lab Management --}}
@if(auth()->user()->organisation->hasModule('lab') && (auth()->user()->hasPermission('lab_catalog.manage') || auth()->user()->hasPermission('labs.manage')))
<div class="nav-section">
<div class="nav-parent">Lab</div>
<div class="nav-children">
@if(auth()->user()->hasPermission('lab_catalog.manage'))
<a href="{{ route('organisation.lab-catalog.index') }}"
class="{{ request()->is('organisation/lab-catalog*') ? 'active' : '' }}">
Test Catalog
</a>
@endif
@if(auth()->user()->hasPermission('labs.manage'))
<a href="{{ route('organisation.labs.index') }}"
class="{{ request()->is('organisation/labs*') ? 'active' : '' }}">
External Labs
</a>
<a href="{{ route('organisation.lab-techs.index') }}"
class="{{ request()->is('organisation/lab-techs*') ? 'active' : '' }}">
Lab Technicians
</a>
@endif
</div>
</div>
@endif

{{-- Reviews & Hiring --}}
<div class="nav-section">
<div class="nav-parent">Engagement</div>
<div class="nav-children">

@if(auth()->user()->hasPermission('reviews.view') || auth()->user()->hasPermission('reviews.manage'))
<a href="{{ url('/organisation/reviews') }}"
   class="{{ request()->is('organisation/reviews*') ? 'active' : '' }}">
Reviews & Feedback
</a>
@endif

<a href="{{ url('/organisation/jobs') }}"
   class="{{ request()->is('organisation/jobs*') ? 'active' : '' }}">
Hiring Portal
</a>

</div>
</div>

{{-- Settings --}}
@if(auth()->user()->hasPermission('settings.manage'))
<div class="nav-section">
    <div class="nav-parent">Settings</div>
    <div class="nav-children">
        <a href="{{ route('organisation.settings.modules') }}"
           class="{{ request()->is('organisation/settings/modules*') ? 'active' : '' }}">
            Modules
        </a>
        <a href="{{ route('organisation.settings.branding') }}"
           class="{{ request()->is('organisation/settings/branding*') ? 'active' : '' }}">
            Branding &amp; Templates
        </a>
        <a href="{{ url('/organisation/whatsapp/settings') }}"
           class="{{ request()->is('organisation/whatsapp*') ? 'active' : '' }}">
            WhatsApp Integration
        </a>
        <a href="{{ url('/organisation/webhooks') }}"
           class="{{ request()->is('organisation/webhooks*') ? 'active' : '' }}">
            Webhooks / API
        </a>
    </div>
</div>
@endif

</div>

</aside>


<!-- Main -->

<div class="main">

<header class="topbar">

<div class="page-title">
Organisation Panel
</div>

<div class="user-box" style="display:flex;align-items:center;gap:14px;">
<span>{{ Auth::user()->name ?? 'User' }}</span>
<form method="POST" action="{{ route('logout') }}" style="margin:0;">
@csrf
<button type="submit" style="background:none;border:1px solid #d1d5db;color:#6b7280;padding:5px 14px;border-radius:6px;font-size:13px;cursor:pointer;">Logout</button>
</form>
</div>

</header>


<main class="content">

@yield('content')

</main>

</div>

</div>

<script>
// Collapsible sidebar sections
document.querySelectorAll('.nav-section .nav-parent').forEach(function(parent) {
    parent.addEventListener('click', function() {
        this.closest('.nav-section').classList.toggle('open');
    });
});

// Auto-expand sections that have an active link, or the first section by default
var anyOpen = false;
document.querySelectorAll('.nav-section').forEach(function(section) {
    if (section.querySelector('.nav-children a.active')) {
        section.classList.add('open');
        anyOpen = true;
    }
});
// If nothing is active, open the first section
if (!anyOpen) {
    var first = document.querySelector('.nav-section');
    if (first) first.classList.add('open');
}
</script>

</body>
</html>