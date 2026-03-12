<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vet Panel - Clinicdesq</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            margin: 0;
            background: #f5f7fa;
            color: #1f2937;
        }

        /* ================= HEADER ================= */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #1f2937;
            color: #ffffff;
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }

        .header .brand {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .header nav {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 14px;
        }

        .header nav a {
            color: #e5e7eb; /* brighter default */
            text-decoration: none;
            padding: 6px 2px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .header nav a[aria-current="page"] {
            color: #60a5fa;   /* blue highlight */
            font-weight: 600;
        }

        .header nav a:hover {
            color: #ffffff;
        }

        .header nav a.active {
            color: #ffffff;
            font-weight: 600;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 6px;
        }

        .header nav a.disabled {
            color: #9ca3af;
            pointer-events: none;
        }

        .header nav form {
            margin: 0;
        }

        .header nav form button {
            background: none;
            border: none;
            color: #d1d5db;
            cursor: pointer;
            font-size: 14px;
            padding: 6px 2px;
        }

        .header nav form button:hover {
            color: #ffffff;
        }

        /* ================= PAGE LAYOUT ================= */
        .page {
            display: flex;
            gap: 32px;
            padding: 32px 48px;   /* more breathing room */
            padding-top: 96px;    /* space for fixed header */
            width: 100%;
            box-sizing: border-box;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            width: 100%;
            max-width: none;
        }

        .main-content {
            margin-left: auto;
            margin-right: auto;
        }

        .content-wrapper {
            width: 100%;
            max-width: 1400px;     /* controls readability */
            margin: 0 auto;       /* centers content */
        }

        .right-panel {
            flex: 1;
            max-width: 420px;
        }

        /* ================= FLASH ================= */
        .flash-success {
            background: #d1fae5;
            padding: 12px 14px;
            margin-bottom: 16px;
            border-radius: 6px;
            font-size: 14px;
            color: #065f46;
        }

        .flash-error {
            background: #fee2e2;
            padding: 12px 14px;
            margin-bottom: 16px;
            border-radius: 6px;
            font-size: 14px;
            color: #7f1d1d;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1024px) {
            .page {
                flex-direction: column;
            }

            .right-panel {
                max-width: 100%;
            }

            .header nav {
                gap: 14px;
            }
        }
        
    </style>
</head>
<body>

@php
    use App\Models\Clinic;

    $vet = auth('vet')->user();

    $hasAnyClinic = false;
    if ($vet) {
        $hasAnyClinic = Clinic::whereHas('vets', function ($q) use ($vet) {
            $q->where('clinic_vet.vet_id', $vet->id)
              ->where('clinic_vet.is_active', 1)
              ->whereNull('clinic_vet.offboarded_at');
        })->exists();
    }

    $hasActiveClinic = session()->has('active_clinic_id');
@endphp

{{-- ================= HEADER ================= --}}
<header class="header">
    <div class="brand">Clinicdesq</div>

    <nav>
            <a href="{{ route('vet.dashboard') }}"
            class="{{ request()->routeIs('vet.dashboard') ? 'active' : '' }}">
            Dashboard
            </a>

        @if($hasActiveClinic)
        <a href="{{ route('vet.clinic.dashboard') }}"
        class="{{ request()->routeIs('vet.clinic.*') ? 'active' : '' }}">
        Clinic Panel
        </a>
        @endif

        @if($hasActiveClinic)
        <a href="{{ route('vet.appointments.create') }}"
        class="{{ request()->routeIs('vet.appointments.create') ? 'active' : '' }}">
        Create Appointment
        </a>
        @else
            <a class="disabled">Create Appointment</a>
        @endif

        <a href="{{ route('vet.appointments.history') }}"
        class="{{ request()->routeIs('vet.appointments.history') ? 'active' : '' }}">
        Past Appointments
        </a>

        <a href="{{ route('vet.pet.history') }}"
        class="{{ request()->routeIs('vet.pet.*') ? 'active' : '' }}">
        Pet History
        </a>

        <a href="{{ route('vet.profile') }}"
        class="{{ request()->routeIs('vet.profile') ? 'active' : '' }}">
        My Profile
        </a>

        <form method="POST" action="{{ route('vet.logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </nav>
</header>

{{-- ================= PAGE BODY ================= --}}
<div class="page">

    {{-- MAIN CONTENT --}}
    <main class="main-content">

        @if(session('success'))
            <div class="flash-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="flash-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="content-wrapper">
            @yield('content')
        </div>
    </main>

    {{-- RIGHT PANEL --}}
    <aside class="right-panel">
        @yield('right-panel')
    </aside>

</div>

</body>
</html>