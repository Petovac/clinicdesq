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
        }

        .logo {
            padding: 16px;
            font-weight: 600;
            border-bottom: 1px solid #1e293b;
        }

        .nav a {
            display: block;
            padding: 10px 16px;
            color: #94a3b8;
            text-decoration: none;
        }

        .nav a:hover,
        .nav a.active {
            background: #020617;
            color: #fff;
        }

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
        <a href="{{ route('organisation.dashboard') }}"
               class="{{ request()->is('organisation/dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
        </div>
    </aside>

    <main class="content">
        @yield('content')
    </main>

</div>

</body>
</html>
