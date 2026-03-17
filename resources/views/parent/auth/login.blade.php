<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Parent Portal — ClinicDesq</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;min-height:100vh;display:flex;}
        .left{flex:1;background:linear-gradient(135deg,#ea580c 0%,#9a3412 100%);display:flex;flex-direction:column;justify-content:center;padding:60px;color:#fff;position:relative;overflow:hidden;}
        .left::before{content:'';position:absolute;top:-80px;right:-80px;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,0.05);}
        .left::after{content:'';position:absolute;bottom:-120px;left:-60px;width:400px;height:400px;border-radius:50%;background:rgba(255,255,255,0.03);}
        .left-brand{font-size:18px;font-weight:700;margin-bottom:48px;letter-spacing:0.3px;position:relative;z-index:1;}
        .left-brand span{color:#fed7aa;}
        .left h2{font-size:36px;font-weight:800;line-height:1.2;margin-bottom:16px;position:relative;z-index:1;}
        .left p{font-size:16px;color:rgba(255,255,255,0.7);line-height:1.6;max-width:400px;position:relative;z-index:1;}
        .left-features{margin-top:36px;position:relative;z-index:1;}
        .left-features li{display:flex;align-items:center;gap:12px;padding:8px 0;font-size:14px;color:rgba(255,255,255,0.8);list-style:none;}
        .left-features li svg{width:20px;height:20px;color:#fed7aa;flex-shrink:0;}
        .right{flex:1;display:flex;align-items:center;justify-content:center;background:#f8fafc;padding:40px;}
        .login-card{width:100%;max-width:420px;}
        .login-card .badge{display:inline-block;background:#ffedd5;color:#c2410c;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;margin-bottom:20px;text-transform:uppercase;letter-spacing:0.5px;}
        .login-card h1{font-size:28px;font-weight:800;color:#0f172a;margin-bottom:6px;}
        .login-card .subtitle{font-size:14px;color:#64748b;margin-bottom:32px;}
        .form-group{margin-bottom:20px;}
        .form-group label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
        .form-group input{width:100%;padding:12px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;background:#fff;transition:all 0.2s;}
        .form-group input:focus{outline:none;border-color:#ea580c;box-shadow:0 0 0 3px rgba(234,88,12,0.1);}
        .form-group input::placeholder{color:#94a3b8;}
        .captcha-box{background:#fff7ed;border:1.5px solid #fed7aa;border-radius:10px;padding:14px;margin-bottom:20px;}
        .captcha-box .question{font-size:16px;font-weight:700;color:#9a3412;margin-bottom:8px;}
        .captcha-box input{width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:15px;background:#fff;}
        .captcha-box input:focus{outline:none;border-color:#ea580c;box-shadow:0 0 0 3px rgba(234,88,12,0.1);}
        .btn-login{width:100%;padding:13px;background:#ea580c;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;transition:all 0.2s;}
        .btn-login:hover{background:#c2410c;transform:translateY(-1px);box-shadow:0 4px 12px rgba(234,88,12,0.3);}
        .error-box{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:20px;}
        .info{margin-top:20px;text-align:center;font-size:12px;color:#94a3b8;line-height:1.5;}
        .divider{margin-top:20px;padding-top:16px;border-top:1px solid #e2e8f0;text-align:center;font-size:12px;color:#94a3b8;}
        .divider a{color:#64748b;text-decoration:none;font-weight:500;}
        .divider a:hover{color:#ea580c;}
        @media(max-width:768px){.left{display:none;}.right{padding:24px;}}
    </style>
</head>
<body>
    <div class="left">
        <div class="left-brand">Clinic<span>Desq</span></div>
        <h2>Your pet's health,<br>at your fingertips.</h2>
        <p>Access your pet's complete medical history, prescriptions, lab reports, and upcoming appointments.</p>
        <ul class="left-features">
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>View Complete Pet Health Records</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Download Prescriptions & Case Sheets</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Track Lab Results & Diagnostics</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Appointment History & Follow-ups</li>
        </ul>
    </div>
    <div class="right">
        <div class="login-card">
            <div class="badge">Pet Parent</div>
            <h1>Pet Parent Portal</h1>
            <p class="subtitle">View your pet's health records and reports</p>

            @if($errors->any())
                <div class="error-box">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('parent.login') }}">
                @csrf
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter your registered phone number" required autofocus>
                </div>
                <div class="captcha-box">
                    <div class="question">{{ $captcha_question }}</div>
                    <input type="number" name="captcha_answer" placeholder="Your answer" required>
                </div>
                <button type="submit" class="btn-login">View My Pets</button>
            </form>

            <div class="info">
                Your phone number must be registered with a ClinicDesq clinic to access records.
            </div>
            <div class="divider">
                <a href="/">Back to ClinicDesq</a>
            </div>
        </div>
    </div>
</body>
</html>
