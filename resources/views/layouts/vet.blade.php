<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vet Panel - Clinicdesq</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* ================= DESIGN TOKENS ================= */
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-soft: #eff6ff;
            --primary-border: #bfdbfe;

            --success: #16a34a;
            --success-soft: #dcfce7;
            --success-border: #bbf7d0;

            --danger: #dc2626;
            --danger-soft: #fee2e2;
            --danger-border: #fecaca;

            --warning: #f59e0b;
            --warning-soft: #fef3c7;
            --warning-border: #fde68a;

            --bg: #f5f7fa;
            --bg-card: #ffffff;
            --bg-soft: #f9fafb;

            --text: #374151;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --text-light: #9ca3af;

            --border: #e5e7eb;
            --border-light: #f3f4f6;

            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --radius-full: 999px;

            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 28px rgba(0,0,0,0.1);

            --header-h: 56px;
            --font: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* ================= RESET ================= */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: var(--font);
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.5;
        }

        /* ================= HEADER ================= */
        .v-header {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--header-h);
            background: #1f2937;
            color: #fff;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        }

        .v-header .brand {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #fff;
        }

        .v-header nav {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
        }

        .v-header nav a,
        .v-header nav button {
            color: #d1d5db;
            text-decoration: none;
            padding: 6px 12px;
            font-weight: 500;
            border-radius: var(--radius-sm);
            transition: all 0.15s ease;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-family: var(--font);
        }

        .v-header nav a:hover,
        .v-header nav button:hover {
            color: #fff;
            background: rgba(255,255,255,0.08);
        }

        .v-header nav a.active {
            color: #fff;
            background: rgba(255,255,255,0.12);
        }

        .v-header nav a.disabled {
            color: #6b7280;
            pointer-events: none;
        }

        .v-header nav form { margin: 0; }

        /* ================= PAGE ================= */
        .v-page {
            padding: calc(var(--header-h) + 24px) 32px 40px;
            max-width: 1280px;
            margin: 0 auto;
            min-height: 100vh;
        }

        .v-page.v-page--wide {
            max-width: 100%;
            padding-left: 24px;
            padding-right: 24px;
        }

        .v-page.v-page--split {
            max-width: 100%;
            padding-left: 24px;
            padding-right: 24px;
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }

        .v-page--split .v-main { flex: 1; min-width: 0; }
        .v-page--split .v-aside { width: 340px; flex-shrink: 0; position: sticky; top: calc(var(--header-h) + 24px); }

        /* ================= FLASH ================= */
        .v-flash {
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 500;
        }

        .v-flash--success {
            background: var(--success-soft);
            color: #065f46;
            border: 1px solid var(--success-border);
        }

        .v-flash--error {
            background: var(--danger-soft);
            color: #7f1d1d;
            border: 1px solid var(--danger-border);
        }

        /* ================= CARD ================= */
        .v-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px 28px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
        }

        .v-card--compact { padding: 18px 22px; }
        .v-card--flush { padding: 0; overflow: hidden; }

        /* ================= PAGE HEADER ================= */
        .v-page-header {
            margin-bottom: 24px;
        }

        .v-page-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 4px;
        }

        .v-page-header p {
            color: var(--text-muted);
            font-size: 14px;
            margin: 0;
        }

        .v-page-header--row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ================= BUTTONS ================= */
        .v-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            font-size: 14px;
            font-weight: 500;
            font-family: var(--font);
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
            line-height: 1.4;
        }

        .v-btn--primary { background: var(--primary); color: #fff; }
        .v-btn--primary:hover { background: var(--primary-hover); }

        .v-btn--success { background: var(--success); color: #fff; }
        .v-btn--success:hover { background: #15803d; }

        .v-btn--danger { background: var(--danger); color: #fff; }
        .v-btn--danger:hover { background: #b91c1c; }

        .v-btn--outline { background: #fff; color: var(--text-dark); border: 1px solid var(--border); }
        .v-btn--outline:hover { background: var(--bg-soft); }

        .v-btn--ghost { background: transparent; color: var(--primary); padding: 6px 10px; }
        .v-btn--ghost:hover { background: var(--primary-soft); }

        .v-btn--sm { padding: 6px 12px; font-size: 13px; }
        .v-btn--block { width: 100%; justify-content: center; }

        /* ================= FORM ================= */
        .v-form-group {
            margin-bottom: 16px;
        }

        .v-form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 5px;
        }

        .v-input {
            width: 100%;
            padding: 9px 12px;
            font-size: 14px;
            font-family: var(--font);
            border-radius: var(--radius-sm);
            border: 1px solid #d1d5db;
            background: #fff;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .v-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(37,99,235,0.12);
        }

        textarea.v-input { resize: vertical; min-height: 70px; }
        select.v-input { cursor: pointer; }
        .v-input--readonly { background: var(--bg-soft); cursor: default; }

        .v-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .v-form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
        }

        .v-form-hint {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ================= TABLE ================= */
        .v-table {
            width: 100%;
            border-collapse: collapse;
        }

        .v-table thead { background: var(--bg-soft); }

        .v-table th {
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 10px 16px;
            border-bottom: 1px solid var(--border);
        }

        .v-table td {
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
        }

        .v-table tbody tr:hover { background: var(--bg-soft); }
        .v-table tbody tr:last-child td { border-bottom: none; }

        /* ================= BADGE ================= */
        .v-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: var(--radius-full);
            font-size: 12px;
            font-weight: 600;
            line-height: 1.4;
        }

        .v-badge--blue { background: #e0f2fe; color: #0369a1; }
        .v-badge--green { background: var(--success-soft); color: #065f46; }
        .v-badge--red { background: var(--danger-soft); color: #991b1b; }
        .v-badge--yellow { background: var(--warning-soft); color: #92400e; }
        .v-badge--gray { background: #f3f4f6; color: #6b7280; }

        /* ================= LINK ================= */
        .v-link {
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
            font-size: 14px;
        }

        .v-link:hover { text-decoration: underline; }

        /* ================= EMPTY STATE ================= */
        .v-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .v-empty--bordered {
            background: #fff;
            border: 1px dashed var(--border);
            border-radius: var(--radius-lg);
        }

        /* ================= BANNER ================= */
        .v-banner {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .v-banner--info { background: var(--primary-soft); border: 1px solid var(--primary-border); }

        /* ================= DIVIDER ================= */
        .v-divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 20px 0;
        }

        /* ================= GRID ================= */
        .v-grid {
            display: grid;
            gap: 20px;
        }

        .v-grid--2 { grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); }
        .v-grid--3 { grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); }

        /* ================= SECTION TITLE ================= */
        .v-section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
            margin: 0 0 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }

        /* ================= DETAIL ROW ================= */
        .v-detail-row {
            display: flex;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .v-detail-row dt {
            width: 180px;
            font-weight: 600;
            color: var(--text-dark);
            flex-shrink: 0;
        }

        .v-detail-row dd {
            color: var(--text);
            margin: 0;
        }

        /* ================= BACK LINK ================= */
        .v-back {
            display: inline-block;
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 16px;
        }

        .v-back:hover { color: var(--primary); }

        /* ================= CENTERED FORM CARD ================= */
        .v-form-card {
            max-width: 520px;
            margin: 0 auto;
        }

        .v-form-card--narrow { max-width: 420px; }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1024px) {
            .v-page--split { flex-direction: column; }
            .v-page--split .v-aside { width: 100%; position: relative; top: 0; }
        }

        @media (max-width: 768px) {
            .v-header nav { gap: 0; overflow-x: auto; }
            .v-header nav a, .v-header nav button { padding: 6px 8px; font-size: 12px; white-space: nowrap; }
            .v-page { padding-left: 16px; padding-right: 16px; }
            .v-card { padding: 18px; }
            .v-form-row, .v-form-row-3 { grid-template-columns: 1fr; }
            .v-grid--2, .v-grid--3 { grid-template-columns: 1fr; }
        }

        @media (max-width: 480px) {
            .v-page-header--row { flex-direction: column; align-items: flex-start; gap: 12px; }
        }
    </style>

    @yield('head')
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

    $aiCreditBalance = 0;
    if ($vet) {
        $aiCreditBalance = \App\Models\VetAiCredit::where('vet_id', $vet->id)->value('balance') ?? 0;
    }
@endphp

{{-- ================= HEADER ================= --}}
<header class="v-header">
    <div class="brand">Clinicdesq</div>

    <nav>
        <a href="{{ route('vet.dashboard') }}"
           class="{{ request()->routeIs('vet.dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        @if($hasActiveClinic)
            <a href="{{ route('vet.clinic.dashboard') }}"
               class="{{ request()->routeIs('vet.clinic.*') ? 'active' : '' }}">
                Clinic
            </a>
        @endif

        @if($hasActiveClinic)
            <a href="{{ route('vet.appointments.create') }}"
               class="{{ request()->routeIs('vet.appointments.create') ? 'active' : '' }}">
                New Appointment
            </a>
        @else
            <a class="disabled">New Appointment</a>
        @endif

        <a href="{{ route('vet.appointments.history') }}"
           class="{{ request()->routeIs('vet.appointments.history') ? 'active' : '' }}">
            History
        </a>

        @if($hasActiveClinic)
            <a href="{{ route('vet.lab-orders.index') }}"
               class="{{ request()->routeIs('vet.lab-orders.*') ? 'active' : '' }}">
                Lab Tests
            </a>
        @endif

        @if($hasActiveClinic)
            <a href="{{ route('vet.ipd.index') }}"
               class="{{ request()->routeIs('vet.ipd.*') ? 'active' : '' }}">
                IPD
            </a>
        @endif

        <a href="{{ route('vet.pet.history') }}"
           class="{{ request()->routeIs('vet.pet.*') ? 'active' : '' }}">
            Pet History
        </a>

        @if($hasActiveClinic)
            <a href="{{ url('/vet/schedule') }}"
               class="{{ request()->is('vet/schedule*') ? 'active' : '' }}">
                Schedule
            </a>
        @endif

        <a href="{{ route('vet.jobs.index') }}"
           class="{{ request()->is('vet/jobs*') ? 'active' : '' }}">
            Jobs
        </a>

        <a href="{{ route('vet.profile') }}"
           class="{{ request()->routeIs('vet.profile') ? 'active' : '' }}">
            Profile
        </a>

        <a href="{{ route('vet.credits.index') }}"
           style="display:inline-flex;align-items:center;gap:4px;background:rgba(255,255,255,0.08);padding:5px 10px;border-radius:var(--radius-sm);text-decoration:none;color:#d1d5db;font-size:12px;">
            <span style="color:#fbbf24;">&#9733;</span>
            <span style="font-weight:700;color:#fff;" class="ai-credit-balance">{{ $aiCreditBalance }}</span>
            <span>credits</span>
        </a>

        @php
            $canSwitchToClinic = false;
            if ($hasActiveClinic && auth('vet')->user()) {
                $canSwitchToClinic = auth('vet')->user()->canManageClinic(session('active_clinic_id'));
            }
        @endphp
        @if($canSwitchToClinic)
            <form method="POST" action="{{ route('vet.switchToClinic') }}" style="margin:0;">
                @csrf
                <button type="submit" style="background:var(--primary-soft);color:var(--primary);border:1px solid var(--primary-border);border-radius:var(--radius-sm);padding:8px 14px;font-weight:600;font-size:13px;cursor:pointer;width:100%;text-align:left;">
                    ⇄ Switch to Clinic Panel
                </button>
            </form>
        @endif

        <form method="POST" action="{{ route('vet.logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </nav>
</header>

{{-- ================= PAGE BODY ================= --}}
<div class="v-page @yield('page-class')">

    @if(session('success'))
        <div class="v-flash v-flash--success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="v-flash v-flash--error">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>

@yield('scripts')

</body>
</html>
