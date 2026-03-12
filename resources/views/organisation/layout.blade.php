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

/* Parent menu */

.nav-parent{
display:block;
padding:10px 12px;
font-size:13px;
font-weight:600;
color:#d1d5db;
letter-spacing:0.3px;
}

/* Children */

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

@if(auth()->user()->hasPermission('dashboard.view'))
<a href="{{ route('organisation.dashboard') }}"
class="{{ request()->is('organisation/dashboard') ? 'active' : '' }}">
Dashboard
</a>
@endif


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


@if(auth()->user()->hasPermission('inventory.manage'))

<div class="nav-section">

<div class="nav-parent">
Inventory
</div>

<div class="nav-children">

<a href="{{ route('organisation.inventory.items') }}"
class="{{ request()->is('organisation/inventory-items*') ? 'active' : '' }}">
Inventory Items
</a>

<a href="{{ route('organisation.inventory.stock') }}"
class="{{ request()->is('organisation/inventory-stock*') ? 'active' : '' }}">
Stock Management
</a>

</div>

</div>

@endif


@if(auth()->user()->hasPermission('pricing.manage'))
<a href="{{ route('organisation.price-lists.index') }}"
class="{{ request()->is('organisation/price-lists*') ? 'active' : '' }}">
Price List
</a>
@endif

</div>

</aside>


<!-- Main -->

<div class="main">

<header class="topbar">

<div class="page-title">
Organisation Panel
</div>

<div class="user-box">
{{ Auth::user()->name ?? 'User' }}
</div>

</header>


<main class="content">

@yield('content')

</main>

</div>

</div>

</body>
</html>