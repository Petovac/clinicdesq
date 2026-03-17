<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clinic Panel</title>

    <style>
        body {
            margin: 0;
            font-family: Inter, system-ui, Arial, sans-serif;
            background: #f3f4f6;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 220px;
            background: #0f172a;
            color: #fff;
            display: flex;
            flex-direction: column;
        }

        .logo {
            padding: 16px;
            font-weight: 600;
            border-bottom: 1px solid #1e293b;
        }

        .nav { padding: 10px 8px; flex: 1; display: flex; flex-direction: column; }

        .nav a {
            display: block;
            padding: 10px 14px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .nav a:hover { background: #1e293b; color: #fff; }
        .nav a.active { background: #2563eb; color: #fff; font-weight: 500; }

        .nav-section { margin-top: 14px; padding-top: 10px; border-top: 1px solid #1e293b; }
        .nav-parent { display: block; padding: 8px 14px; font-size: 12px; font-weight: 600; color: #cbd5e1; letter-spacing: 0.3px; text-transform: uppercase; }
        .nav-children a { padding-left: 26px; font-size: 13px; }

        .content {
            flex: 1;
            padding: 24px;
        }
    </style>
</head>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<body>

<div class="wrapper">

    <aside class="sidebar">
        <div class="logo">
            Clinic
        </div>

        <div class="nav">

            @if(auth()->user()->hasPermission('dashboard.view'))
            <a href="{{ route('clinic.dashboard') }}"
               class="{{ request()->is('clinic/dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
            @endif

            {{-- Appointments --}}
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

            {{-- Billing --}}
            @if(auth()->user()->hasPermission('billing.view') || auth()->user()->hasPermission('billing.create'))
            <a href="{{ route('clinic.appointments.index') }}"
               class="{{ request()->is('clinic/appointments/*/billing*') ? 'active' : '' }}">
                Billing
            </a>
            @endif

            {{-- Inventory Section --}}
            @if(auth()->user()->hasPermission('inventory.view') || auth()->user()->hasPermission('inventory.adjust') || auth()->user()->hasPermission('inventory.purchase'))
            <div class="nav-section">
                <div class="nav-parent">Inventory</div>
                <div class="nav-children">

                    @if(auth()->user()->hasPermission('inventory.view'))
                    <a href="{{ route('clinic.inventory.index') }}"
                       class="{{ request()->is('clinic/inventory') || request()->is('clinic/inventory/*') ? 'active' : '' }}">
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

            {{-- Lab Orders --}}
            <a href="{{ route('clinic.lab-orders.index') }}"
               class="{{ request()->is('clinic/lab-orders*') ? 'active' : '' }}">
                Lab Orders
            </a>

            {{-- Follow-ups --}}
            @if(auth()->user()->hasPermission('followups.view'))
            <a href="{{ route('clinic.followups.index') }}"
               class="{{ request()->is('clinic/followups*') ? 'active' : '' }}">
                Follow-ups
            </a>
            @endif

            {{-- IPD --}}
            @if(auth()->user()->hasPermission('ipd.view'))
            <div class="nav-section">
                <div class="nav-parent">IPD</div>
                <div class="nav-children">
                    <a href="{{ route('clinic.ipd.index') }}"
                       class="{{ request()->is('clinic/ipd') ? 'active' : '' }}">
                        Patients
                    </a>
                    @if(auth()->user()->hasPermission('ipd.manage'))
                    <a href="{{ route('clinic.ipd.create') }}"
                       class="{{ request()->is('clinic/ipd-admit') ? 'active' : '' }}">
                        + New Admission
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Logout --}}
            <div style="margin-top:auto;padding-top:16px;border-top:1px solid #1e293b;">
                <div style="padding:6px 14px;font-size:12px;color:#64748b;margin-bottom:4px;">{{ auth()->user()->name }}</div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="display:block;width:100%;text-align:left;padding:10px 14px;background:none;border:none;color:#94a3b8;font-size:14px;cursor:pointer;border-radius:6px;">
                        Logout
                    </button>
                </form>
            </div>

        </div>
    </aside>

    <main class="content">
        @yield('content')
    </main>

</div>

</body>
</html>
