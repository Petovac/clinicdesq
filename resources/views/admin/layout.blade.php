<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Clinicdesq Admin</title>

<style>
* { box-sizing:border-box; }
body {
    margin:0;
    font-family:Inter,system-ui,Arial,sans-serif;
    background:#f3f4f6;
    color:#111827;
}

.admin-wrapper { display:flex; min-height:100vh; }

/* ===== Sidebar ===== */
.admin-sidebar {
    width:240px;
    background:#111827;
    color:#fff;
    display:flex;
    flex-direction:column;
}

.admin-logo {
    padding:18px 20px;
    font-weight:700;
    font-size:16px;
    border-bottom:1px solid #1f2937;
    letter-spacing:0.4px;
}

.admin-nav { padding:14px; flex:1; }

.admin-nav a {
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px 12px;
    text-decoration:none;
    color:#9ca3af;
    border-radius:6px;
    font-size:14px;
    margin-bottom:4px;
    transition:all .15s ease;
}
.admin-nav a:hover { background:#1f2937; color:#fff; }
.admin-nav a.active { background:#2563eb; color:#fff; font-weight:500; }

.nav-section {
    margin-top:14px;
    padding-top:10px;
    border-top:1px solid #1f2937;
}
.nav-parent {
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
.nav-parent:hover { background:rgba(255,255,255,0.05); }
.nav-parent::after {
    content:'›';
    font-size:16px;
    font-weight:400;
    color:#6b7280;
    transition:transform 0.2s;
}
.nav-section.open .nav-parent::after { transform:rotate(90deg); }

.nav-children { display:none; overflow:hidden; }
.nav-section.open .nav-children { display:block; }
.nav-children a { padding-left:28px; font-size:13px; }

/* ===== Main ===== */
.admin-main { flex:1; display:flex; flex-direction:column; }

.admin-topbar {
    height:56px;
    background:#fff;
    border-bottom:1px solid #e5e7eb;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 22px;
}
.admin-title { font-weight:600; font-size:16px; }
.admin-user { font-size:14px; color:#6b7280; }

.admin-content { padding:28px; }

.card {
    background:#fff;
    border-radius:10px;
    padding:24px;
    border:1px solid #e5e7eb;
    box-shadow:0 1px 2px rgba(0,0,0,0.03);
    margin-bottom:24px;
}

.page-title { font-size:24px; font-weight:700; margin-bottom:24px; }
</style>
</head>

<body>

<div class="admin-wrapper">

<aside class="admin-sidebar">

<div class="admin-logo">Clinicdesq</div>

<nav class="admin-nav">

<div class="nav-section" style="margin-top:0;padding-top:0;border-top:none;">
<div class="nav-parent">Platform</div>
<div class="nav-children">

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

</div>
</div>

<div class="nav-section">
<div class="nav-parent">Knowledge Base</div>
<div class="nav-children">

<a href="{{ url('/admin/drugs') }}"
class="{{ request()->is('admin/drugs*') ? 'active' : '' }}">
Drug KB
</a>

<a href="{{ url('/admin/lab-directory') }}"
class="{{ request()->is('admin/lab-directory*') ? 'active' : '' }}">
Lab Test Directory
</a>

<a href="{{ url('/admin/drug-submissions') }}"
class="{{ request()->is('admin/drug-submissions*') ? 'active' : '' }}">
Drug Submissions
@php $pendingCount = \App\Models\DrugSubmission::where('status','pending')->count(); @endphp
@if($pendingCount > 0)
<span style="background:#ef4444;color:#fff;padding:1px 7px;border-radius:10px;font-size:11px;margin-left:auto;">{{ $pendingCount }}</span>
@endif
</a>

</div>
</div>

<div class="nav-section">
<div class="nav-parent">Support</div>
<div class="nav-children">

<a href="{{ url('/admin/support') }}"
class="{{ request()->is('admin/support*') ? 'active' : '' }}">
Support Mode
</a>

</div>
</div>

</nav>

</aside>

<div class="admin-main">

<header class="admin-topbar">
<div class="admin-title">Admin Panel</div>
<div class="admin-user">{{ Auth::user()->name ?? 'Admin' }}</div>
</header>

<main class="admin-content">
@yield('content')
</main>

</div>

</div>

<script>
document.querySelectorAll('.nav-section .nav-parent').forEach(function(parent) {
    parent.addEventListener('click', function() {
        this.closest('.nav-section').classList.toggle('open');
    });
});
var anyOpen = false;
document.querySelectorAll('.nav-section').forEach(function(section) {
    if (section.querySelector('.nav-children a.active')) {
        section.classList.add('open');
        anyOpen = true;
    }
});
if (!anyOpen) {
    var first = document.querySelector('.nav-section');
    if (first) first.classList.add('open');
}
</script>

</body>
</html>
