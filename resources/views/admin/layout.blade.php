<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Clinicdesq Admin</title>

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

.admin-wrapper{
display:flex;
min-height:100vh;
}

/* ===== Sidebar ===== */

.admin-sidebar{
width:240px;
background:#111827;
color:#fff;
display:flex;
flex-direction:column;
}

.admin-logo{
padding:18px 20px;
font-weight:700;
font-size:16px;
border-bottom:1px solid #1f2937;
letter-spacing:0.5px;
}

.admin-nav{
padding:14px;
flex:1;
}

.admin-nav a{
display:flex;
align-items:center;
gap:10px;
padding:10px 12px;
text-decoration:none;
color:#9ca3af;
border-radius:6px;
font-size:14px;
margin-bottom:6px;
transition:all .15s ease;
}

.admin-nav a:hover{
background:#1f2937;
color:#fff;
}

.admin-nav a.active{
background:#2563eb;
color:#fff;
font-weight:500;
}

/* ===== Main ===== */

.admin-main{
flex:1;
display:flex;
flex-direction:column;
}

/* ===== Top Bar ===== */

.admin-topbar{
height:56px;
background:#fff;
border-bottom:1px solid #e5e7eb;
display:flex;
align-items:center;
justify-content:space-between;
padding:0 20px;
}

.admin-title{
font-weight:600;
font-size:16px;
}

.admin-user{
font-size:14px;
color:#6b7280;
}

/* ===== Page Content ===== */

.admin-content{
padding:28px;
}

/* ===== Cards ===== */

.card{
background:#fff;
border-radius:10px;
padding:24px;
border:1px solid #e5e7eb;
box-shadow:0 1px 2px rgba(0,0,0,0.03);
margin-bottom:24px;
}

/* ===== Page Title ===== */

.page-title{
font-size:24px;
font-weight:700;
margin-bottom:24px;
}

</style>
</head>

<body>

<div class="admin-wrapper">

<!-- Sidebar -->
<aside class="admin-sidebar">

<div class="admin-logo">
Clinicdesq
</div>

<nav class="admin-nav">

<a href="{{ route('admin.dashboard') }}"
class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
Dashboard
</a>

<a href="{{ url('/admin/organisations') }}"
class="{{ request()->is('admin/organisations*') ? 'active' : '' }}">
Organisations
</a>

<a href="{{ url('/admin/clinics') }}"
class="{{ request()->is('admin/clinics*') ? 'active' : '' }}">
Clinics
</a>

<a href="{{ url('/admin/vets') }}"
class="{{ request()->is('admin/vets*') ? 'active' : '' }}">
Vets
</a>

<a href="{{ url('/admin/drugs') }}"
class="{{ request()->is('admin/drugs*') ? 'active' : '' }}">
Drug Knowledge Base
</a>

<a href="{{ url('/admin/support') }}"
class="{{ request()->is('admin/support*') ? 'active' : '' }}">
Support Mode
</a>

</nav>

</aside>

<!-- Main -->
<div class="admin-main">

<!-- Topbar -->
<header class="admin-topbar">

<div class="admin-title">
Admin Panel
</div>

<div class="admin-user">
{{ Auth::user()->name ?? 'Admin' }}
</div>

</header>

<!-- Page Content -->
<main class="admin-content">

@yield('content')

</main>

</div>

</div>

</body>
</html>