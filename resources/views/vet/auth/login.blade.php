<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinarian Login — ClinicDesq</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;min-height:100vh;display:flex;}
        .left{flex:1;background:linear-gradient(135deg,#0f766e 0%,#134e4a 100%);display:flex;flex-direction:column;justify-content:center;padding:60px;color:#fff;position:relative;overflow:hidden;}
        .left::before{content:'';position:absolute;top:-80px;right:-80px;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,0.05);}
        .left::after{content:'';position:absolute;bottom:-120px;left:-60px;width:400px;height:400px;border-radius:50%;background:rgba(255,255,255,0.03);}
        .left-brand{font-size:18px;font-weight:700;margin-bottom:48px;letter-spacing:0.3px;position:relative;z-index:1;}
        .left-brand span{color:#5eead4;}
        .left h2{font-size:36px;font-weight:800;line-height:1.2;margin-bottom:16px;position:relative;z-index:1;}
        .left p{font-size:16px;color:rgba(255,255,255,0.7);line-height:1.6;max-width:400px;position:relative;z-index:1;}
        .left-features{margin-top:36px;position:relative;z-index:1;}
        .left-features li{display:flex;align-items:center;gap:12px;padding:8px 0;font-size:14px;color:rgba(255,255,255,0.8);list-style:none;}
        .left-features li svg{width:20px;height:20px;color:#5eead4;flex-shrink:0;}
        .right{flex:1;display:flex;align-items:center;justify-content:center;background:#f8fafc;padding:40px;}
        .login-card{width:100%;max-width:420px;}
        .login-card .badge{display:inline-block;background:#d1fae5;color:#065f46;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;margin-bottom:20px;text-transform:uppercase;letter-spacing:0.5px;}
        .login-card h1{font-size:28px;font-weight:800;color:#0f172a;margin-bottom:6px;}
        .login-card .subtitle{font-size:14px;color:#64748b;margin-bottom:32px;}
        .form-group{margin-bottom:20px;}
        .form-group label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
        .form-group input{width:100%;padding:12px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;background:#fff;transition:all 0.2s;}
        .form-group input:focus{outline:none;border-color:#0d9488;box-shadow:0 0 0 3px rgba(13,148,136,0.1);}
        .form-group input::placeholder{color:#94a3b8;}
        .btn-login{width:100%;padding:13px;background:#0d9488;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;transition:all 0.2s;margin-top:4px;}
        .btn-login:hover{background:#0f766e;transform:translateY(-1px);box-shadow:0 4px 12px rgba(13,148,136,0.3);}
        .error-box{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:20px;}
        .success-box{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:20px;}
        .login-footer{margin-top:24px;text-align:center;font-size:13px;color:#64748b;}
        .login-footer a{color:#0d9488;font-weight:600;text-decoration:none;}
        .login-footer a:hover{text-decoration:underline;}
        .divider{margin-top:20px;padding-top:20px;border-top:1px solid #e2e8f0;text-align:center;font-size:12px;color:#94a3b8;}
        .divider a{color:#64748b;text-decoration:none;font-weight:500;}
        .divider a:hover{color:#0d9488;}
        @media(max-width:768px){
            .left{display:none;}
            .right{padding:24px;}
        }
    </style>
</head>
<body>
    <div class="left">
        <div class="left-brand">Clinic<span>Desq</span></div>
        <h2>Your patients<br>deserve the best.</h2>
        <p>AI-powered clinical decision support, digital prescriptions, and complete practice management — all in one place.</p>
        <ul class="left-features">
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>AI Clinical Insights & Prescription Support</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Digital Case Sheets & Prescriptions</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Lab Orders with Real-time Tracking</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Complete Pet History & Diagnostics</li>
        </ul>
    </div>
    <div class="right">
        <div class="login-card">
            <div class="badge">Veterinarian</div>
            <h1>Welcome back</h1>
            <p class="subtitle">Sign in to your veterinarian dashboard</p>

            @error('email')
                <div class="error-box">{{ $message }}</div>
            @enderror

            @if(session('success'))
                <div class="success-box">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('vet.login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@clinic.com" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <div class="login-footer">
                Don't have an account? <a href="{{ route('vet.register') }}">Register here</a>
            </div>

            <div class="divider">
                <a href="/">Back to ClinicDesq</a>
            </div>
        </div>
    </div>
</body>
</html>
