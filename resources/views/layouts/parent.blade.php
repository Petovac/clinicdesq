<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Pets') — ClinicDesq</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f7fa; color: #1a1a2e; }
        .navbar { background: #1e293b; color: #fff; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .navbar .brand { font-size: 18px; font-weight: 700; text-decoration: none; color: #fff; }
        .navbar .user-info { display: flex; align-items: center; gap: 12px; font-size: 13px; }
        .navbar .user-name { color: #94a3b8; }
        .navbar .logout-btn { background: none; border: 1px solid #475569; color: #94a3b8; padding: 5px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; }
        .navbar .logout-btn:hover { background: #334155; color: #fff; }
        .container { max-width: 900px; margin: 0 auto; padding: 24px 16px; }
        a { color: #2563eb; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('parent.dashboard') }}" class="brand">ClinicDesq</a>
        <div class="user-info">
            <span class="user-name">{{ auth('pet_parent')->user()->name }}</span>
            <form method="POST" action="{{ route('parent.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>
</body>
</html>
