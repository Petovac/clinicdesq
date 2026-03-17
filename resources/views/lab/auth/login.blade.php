<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Portal Login — ClinicDesq</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .login-card h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .login-card .subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 28px;
        }
        .badge {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 16px;
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 16px;
            transition: border-color 0.2s;
        }
        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .btn {
            width: 100%;
            padding: 11px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover { background: #1d4ed8; }
        .error {
            background: #fef2f2;
            color: #dc2626;
            font-size: 13px;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="badge">Lab Portal</div>
        <h1>Laboratory Login</h1>
        <p class="subtitle">Access your lab orders and upload results</p>

        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <form method="POST" action="{{ route('lab.login') }}">
            @csrf
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn">Sign In</button>
        </form>
    </div>
</body>
</html>
