<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clinic Panel</title>

    <style>
        * { box-sizing:border-box; }
        body {
            margin: 0;
            font-family: Inter, system-ui, Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        .wrapper { display:flex; min-height:100vh; }

        /* ===== Sidebar ===== */
        .sidebar {
            width: 240px;
            background: #111827;
            color: #fff;
            display: flex;
            flex-direction: column;
        }

        .logo {
            padding: 18px 20px;
            font-weight: 700;
            font-size: 16px;
            border-bottom: 1px solid #1f2937;
            letter-spacing: 0.4px;
        }

        .nav { padding:14px; flex:1; display:flex; flex-direction:column; }

        .nav a {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            margin-bottom: 4px;
            text-decoration: none;
            color: #9ca3af;
            border-radius: 6px;
            font-size: 14px;
            transition: all .15s ease;
        }
        .nav a:hover { background:#1f2937; color:#fff; }
        .nav a.active { background:#2563eb; color:#fff; font-weight:500; }

        .nav-section {
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid #1f2937;
        }

        .nav-parent {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 600;
            color: #d1d5db;
            letter-spacing: 0.3px;
            cursor: pointer;
            user-select: none;
            border-radius: 6px;
            transition: background 0.15s;
        }
        .nav-parent:hover { background:rgba(255,255,255,0.05); }
        .nav-parent::after {
            content: '›';
            font-size: 16px;
            font-weight: 400;
            color: #6b7280;
            transition: transform 0.2s;
        }
        .nav-section.open .nav-parent::after { transform:rotate(90deg); }

        .nav-children { display:none; overflow:hidden; }
        .nav-section.open .nav-children { display:block; }
        .nav-children a { padding-left:28px; font-size:13px; }

        /* ===== Content ===== */
        .content { flex:1; padding:28px; }

        /* ===== Bottom user area ===== */
        .nav-bottom {
            margin-top: auto;
            padding: 12px 14px;
            border-top: 1px solid #1f2937;
        }
        .nav-bottom .user-name {
            font-size: 12px;
            color: #64748b;
            padding: 4px 0 6px;
        }
        .switch-btn {
            display: block;
            width: 100%;
            text-align: left;
            padding: 10px 12px;
            background: #1e3a5f;
            border: 1px solid #2563eb;
            color: #93c5fd;
            font-size: 13px;
            cursor: pointer;
            border-radius: 6px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .switch-btn:hover { background:#1e40af; }
        .logout-btn {
            display: block;
            width: 100%;
            text-align: left;
            padding: 10px 12px;
            background: none;
            border: none;
            color: #9ca3af;
            font-size: 14px;
            cursor: pointer;
            border-radius: 6px;
        }
        .logout-btn:hover { background:#1f2937; color:#fff; }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="wrapper">

    <aside class="sidebar">
        <div class="logo">Clinicdesq</div>

        <div class="nav">

            {{-- Clinic Operations --}}
            <div class="nav-section" style="margin-top:0;padding-top:0;border-top:none;">
            <div class="nav-parent">Clinic</div>
            <div class="nav-children">

            <a href="{{ route('clinic.dashboard') }}"
               class="{{ request()->is('clinic/dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('clinic.analytics') }}"
               class="{{ request()->is('clinic/analytics*') ? 'active' : '' }}">
                Analytics
            </a>

            @if(auth()->user()->hasPermission('appointments.view'))
            <a href="{{ route('clinic.appointments.index') }}"
               class="{{ request()->is('clinic/appointments') ? 'active' : '' }}">
                Appointments
            </a>
            @endif

            @if(auth()->user()->hasPermission('appointments.create'))
            <a href="{{ route('clinic.appointments.create') }}"
               class="{{ request()->is('clinic/appointments/create*') ? 'active' : '' }}">
                + New Appointment
            </a>
            @endif

            @if(auth()->user()->hasPermission('billing.view') || auth()->user()->hasPermission('billing.create'))
            <a href="{{ route('clinic.appointments.index') }}"
               class="{{ request()->is('clinic/appointments/*/billing*') ? 'active' : '' }}">
                Billing
            </a>
            @endif

            @if(auth()->user()->hasPermission('followups.view'))
            <a href="{{ route('clinic.followups.index') }}"
               class="{{ request()->is('clinic/followups*') ? 'active' : '' }}">
                Follow-ups
            </a>
            @endif

            </div>
            </div>

            {{-- Inventory --}}
            @if(auth()->user()->hasPermission('inventory.view') || auth()->user()->hasPermission('inventory.adjust') || auth()->user()->hasPermission('inventory.purchase'))
            <div class="nav-section">
            <div class="nav-parent">Inventory</div>
            <div class="nav-children">

                @if(auth()->user()->hasPermission('inventory.view'))
                <a href="{{ route('clinic.inventory.index') }}"
                   class="{{ request()->is('clinic/inventory') || request()->is('clinic/inventory/*') && !request()->is('clinic/inventory/adjust') && !request()->is('clinic/inventory/movements') ? 'active' : '' }}">
                    Stock Overview
                </a>
                @endif

                @if(auth()->user()->hasPermission('inventory.adjust'))
                <a href="{{ route('clinic.inventory.adjust') }}"
                   class="{{ request()->is('clinic/inventory/adjust') ? 'active' : '' }}">
                    Stock Adjust
                </a>
                @endif

                @if(auth()->user()->hasPermission('inventory.movements.view'))
                <a href="{{ route('clinic.inventory.movements') }}"
                   class="{{ request()->is('clinic/inventory/movements') ? 'active' : '' }}">
                    Stock Movements
                </a>
                @endif

                @if(auth()->user()->hasPermission('inventory.purchase'))
                <a href="{{ route('clinic.orders.index') }}"
                   class="{{ request()->is('clinic/orders*') ? 'active' : '' }}">
                    Order Requests
                </a>
                @endif

            </div>
            </div>
            @endif

            {{-- Lab & IPD --}}
            <div class="nav-section">
            <div class="nav-parent">Lab & IPD</div>
            <div class="nav-children">

            <a href="{{ route('clinic.lab-orders.index') }}"
               class="{{ request()->is('clinic/lab-orders*') ? 'active' : '' }}">
                Lab Orders
            </a>

            @if(auth()->user()->hasPermission('ipd.view'))
            <a href="{{ route('clinic.ipd.index') }}"
               class="{{ request()->is('clinic/ipd') ? 'active' : '' }}">
                IPD Patients
            </a>
            @if(auth()->user()->hasPermission('ipd.manage'))
            <a href="{{ route('clinic.ipd.create') }}"
               class="{{ request()->is('clinic/ipd-admit') ? 'active' : '' }}">
                + New Admission
            </a>
            @endif
            @endif

            </div>
            </div>

            {{-- Bottom: user info + switch + logout --}}
            <div class="nav-bottom">
                <div class="user-name">{{ auth()->user()->name }}</div>

                @if(auth()->user()->linked_vet_id)
                <form method="POST" action="{{ route('clinic.switchToVet') }}">
                    @csrf
                    <button type="submit" class="switch-btn">⇄ Switch to Vet Panel</button>
                </form>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>

        </div>
    </aside>

    <main class="content">
        @yield('content')
    </main>

</div>

<script>
// Collapsible sidebar sections
document.querySelectorAll('.nav-section .nav-parent').forEach(function(parent) {
    parent.addEventListener('click', function() {
        this.closest('.nav-section').classList.toggle('open');
    });
});
// Auto-expand sections with active links
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
