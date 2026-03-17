<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Portal — ClinicDesq</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-soft: #eff6ff;
            --bg: #f5f7fa;
            --bg-card: #ffffff;
            --text: #374151;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --radius-md: 10px;
            --radius-lg: 14px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --header-h: 56px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            padding-top: var(--header-h);
        }
        header {
            position: fixed; top: 0; left: 0; right: 0;
            height: var(--header-h);
            background: #1f2937;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px;
            z-index: 100;
        }
        header .brand {
            display: flex; align-items: center; gap: 10px;
            color: #fff; font-size: 15px; font-weight: 700; text-decoration: none;
        }
        header .brand .badge {
            background: #3b82f6;
            font-size: 10px; font-weight: 700;
            padding: 2px 8px; border-radius: 20px; color: #fff;
        }
        header nav { display: flex; align-items: center; gap: 4px; }
        header nav a {
            color: #d1d5db; font-size: 13px; font-weight: 500;
            padding: 8px 14px; border-radius: 8px; text-decoration: none;
            transition: all 0.15s;
        }
        header nav a:hover { color: #fff; background: rgba(255,255,255,0.08); }
        header nav a.active { color: #fff; background: rgba(255,255,255,0.14); }
        header nav form button {
            color: #d1d5db; font-size: 13px; font-weight: 500;
            padding: 8px 14px; border-radius: 8px; background: none; border: none;
            cursor: pointer; transition: all 0.15s;
        }
        header nav form button:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .lab-name {
            color: #9ca3af; font-size: 12px; margin-right: 16px;
        }
        main { max-width: 1200px; margin: 24px auto; padding: 0 24px; }
        .page-title {
            font-size: 20px; font-weight: 700; color: var(--text-dark);
            margin-bottom: 20px;
        }
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 16px;
        }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
            border: none; cursor: pointer; text-decoration: none; transition: all 0.15s;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-outline { background: #fff; color: var(--text); border: 1px solid var(--border); }
        .btn-outline:hover { background: #f9fafb; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .status-badge {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600; text-transform: uppercase;
        }
        .status-ordered { background: #fef3c7; color: #92400e; }
        .status-routed { background: #dbeafe; color: #1d4ed8; }
        .status-processing { background: #e0e7ff; color: #4338ca; }
        .status-results_uploaded { background: #d1fae5; color: #065f46; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-retest_requested { background: #fee2e2; color: #991b1b; }
        @yield('head')
    </style>
</head>
<body>
    <header>
        <a href="{{ route('lab.dashboard') }}" class="brand">
            ClinicDesq <span class="badge">LAB</span>
        </a>
        <nav>
            <a href="{{ route('lab.dashboard') }}"
               class="{{ request()->routeIs('lab.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('lab.orders.index') }}"
               class="{{ request()->routeIs('lab.orders.*') ? 'active' : '' }}">
                Orders
            </a>
            <span class="lab-name">{{ auth('lab')->user()->lab->name ?? '' }}</span>
            <form method="POST" action="{{ route('lab.logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </nav>
    </header>

    <main>
        @if(session('success'))
            <div style="background:#dcfce7;border:1px solid #bbf7d0;color:#166534;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background:#fee2e2;border:1px solid #fecaca;color:#991b1b;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
