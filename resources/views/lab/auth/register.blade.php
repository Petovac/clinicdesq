<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your Lab — ClinicDesq</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;min-height:100vh;display:flex;}
        .left{flex:1;background:linear-gradient(135deg,#7c3aed 0%,#4c1d95 100%);display:flex;flex-direction:column;justify-content:center;padding:60px;color:#fff;position:relative;overflow:hidden;}
        .left::before{content:'';position:absolute;top:-80px;right:-80px;width:300px;height:300px;border-radius:50%;background:rgba(255,255,255,0.05);}
        .left::after{content:'';position:absolute;bottom:-120px;left:-60px;width:400px;height:400px;border-radius:50%;background:rgba(255,255,255,0.03);}
        .left-brand{font-size:18px;font-weight:700;margin-bottom:48px;letter-spacing:0.3px;position:relative;z-index:1;}
        .left-brand span{color:#c4b5fd;}
        .left h2{font-size:32px;font-weight:800;line-height:1.2;margin-bottom:16px;position:relative;z-index:1;}
        .left p{font-size:15px;color:rgba(255,255,255,0.7);line-height:1.6;max-width:400px;position:relative;z-index:1;}
        .left-features{margin-top:32px;position:relative;z-index:1;}
        .left-features li{display:flex;align-items:center;gap:12px;padding:8px 0;font-size:14px;color:rgba(255,255,255,0.8);list-style:none;}
        .left-features li svg{width:20px;height:20px;color:#c4b5fd;flex-shrink:0;}
        .right{flex:1;display:flex;align-items:center;justify-content:center;background:#f8fafc;padding:40px;overflow-y:auto;}
        .reg-card{width:100%;max-width:480px;}
        .reg-card .badge{display:inline-block;background:#ede9fe;color:#6d28d9;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;margin-bottom:16px;text-transform:uppercase;letter-spacing:0.5px;}
        .reg-card h1{font-size:26px;font-weight:800;color:#0f172a;margin-bottom:6px;}
        .reg-card .subtitle{font-size:14px;color:#64748b;margin-bottom:28px;}
        .section-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#7c3aed;margin-bottom:12px;margin-top:24px;padding-bottom:6px;border-bottom:1px solid #ede9fe;}
        .section-label:first-of-type{margin-top:0;}
        .form-row{display:flex;gap:12px;}
        .form-row .form-group{flex:1;}
        .form-group{margin-bottom:14px;}
        .form-group label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;}
        .form-group input,.form-group textarea{width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;background:#fff;transition:all 0.2s;font-family:inherit;}
        .form-group textarea{resize:vertical;min-height:60px;}
        .form-group input:focus,.form-group textarea:focus{outline:none;border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,0.1);}
        .form-group input::placeholder,.form-group textarea::placeholder{color:#94a3b8;}
        .btn-register{width:100%;padding:13px;background:#7c3aed;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;transition:all 0.2s;margin-top:8px;}
        .btn-register:hover{background:#6d28d9;transform:translateY(-1px);box-shadow:0 4px 12px rgba(124,58,237,0.3);}
        .error-box{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:16px;}
        .error-text{color:#dc2626;font-size:11px;margin-top:2px;}
        .login-footer{margin-top:20px;text-align:center;font-size:13px;color:#64748b;}
        .login-footer a{color:#7c3aed;font-weight:600;text-decoration:none;}
        .login-footer a:hover{text-decoration:underline;}
        .divider{margin-top:16px;padding-top:16px;border-top:1px solid #e2e8f0;text-align:center;font-size:12px;color:#94a3b8;}
        .divider a{color:#64748b;text-decoration:none;font-weight:500;}
        .divider a:hover{color:#7c3aed;}
        @media(max-width:768px){.left{display:none;}.right{padding:24px;}}
    </style>
</head>
<body>
    <div class="left">
        <div class="left-brand">Clinic<span>Desq</span></div>
        <h2>Register your lab<br>on ClinicDesq</h2>
        <p>Join the network. Veterinary clinics across India can onboard your lab, send you orders, and receive results digitally.</p>
        <ul class="left-features">
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Receive lab orders from multiple clinics</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Upload results directly to the vet</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Manage your test catalog & B2B pricing</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Get onboarded by orgs in your city</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Free to register — no subscription needed</li>
        </ul>
    </div>
    <div class="right">
        <div class="reg-card">
            <div class="badge">Laboratory</div>
            <h1>Register Your Lab</h1>
            <p class="subtitle">Create your lab profile and admin account</p>

            @if($errors->any())
                <div class="error-box">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('lab.register') }}">
                @csrf

                <div class="section-label">Lab Details</div>

                <div class="form-group">
                    <label>Lab Name *</label>
                    <input type="text" name="lab_name" value="{{ old('lab_name') }}" placeholder="e.g. Bangalore Veterinary Diagnostics" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>City *</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g. Bangalore" required>
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="state" value="{{ old('state') }}" placeholder="e.g. Karnataka">
                    </div>
                    <div class="form-group">
                        <label>Pincode</label>
                        <input type="text" name="pincode" value="{{ old('pincode') }}" placeholder="560001">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Lab Phone</label>
                        <input type="tel" name="lab_phone" value="{{ old('lab_phone') }}" placeholder="080-12345678">
                    </div>
                    <div class="form-group">
                        <label>Lab Email</label>
                        <input type="email" name="lab_email" value="{{ old('lab_email') }}" placeholder="info@lab.com">
                    </div>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" placeholder="Lab address">{{ old('address') }}</textarea>
                </div>

                <div class="section-label">Admin Account</div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Full name" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="9876543210">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="you@lab.com" required>
                    @error('email') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" name="password" placeholder="Min 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm" required>
                    </div>
                </div>

                <button type="submit" class="btn-register">Register Lab</button>
            </form>

            <div class="login-footer">
                Already registered? <a href="{{ route('lab.login') }}">Sign in</a>
            </div>
            <div class="divider">
                <a href="/">Back to ClinicDesq</a>
            </div>
        </div>
    </div>
</body>
</html>
