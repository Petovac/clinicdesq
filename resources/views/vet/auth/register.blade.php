<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vet Registration — ClinicDesq</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f7fa; color: #1a1a2e; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 36px; width: 100%; max-width: 560px; }
        .brand { text-align: center; margin-bottom: 24px; }
        .brand h1 { font-size: 24px; font-weight: 700; color: #1e293b; }
        .brand p { font-size: 13px; color: #64748b; margin-top: 4px; }
        .section-title { font-size: 13px; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 1px; margin: 20px 0 10px; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }
        .row { display: flex; gap: 12px; }
        .row > .field { flex: 1; }
        .field { margin-bottom: 14px; }
        .field label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 4px; }
        .field label .req { color: #dc2626; }
        .field input[type="text"],
        .field input[type="email"],
        .field input[type="password"],
        .field input[type="tel"],
        .field textarea { width: 100%; padding: 9px 12px; font-size: 14px; border: 1px solid #d1d5db; border-radius: 8px; outline: none; transition: border-color 0.15s; }
        .field input:focus, .field textarea:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .field textarea { resize: vertical; min-height: 70px; }
        .field input[type="file"] { font-size: 13px; padding: 7px 0; }
        .field .hint { font-size: 11px; color: #9ca3af; margin-top: 3px; }
        .field .error { color: #dc2626; font-size: 12px; margin-top: 3px; }
        .btn { display: block; width: 100%; padding: 12px; background: #2563eb; color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 20px; transition: background 0.15s; }
        .btn:hover { background: #1d4ed8; }
        .login-link { text-align: center; margin-top: 16px; font-size: 13px; color: #64748b; }
        .login-link a { color: #2563eb; text-decoration: none; font-weight: 600; }
        .alert { padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        @media (max-width: 480px) { .row { flex-direction: column; gap: 0; } .card { padding: 24px 20px; } }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <h1>Veterinarian Registration</h1>
            <p>Create your ClinicDesq account</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('vet.register') }}" enctype="multipart/form-data">
            @csrf

            <div class="section-title">Personal Information</div>

            <div class="field">
                <label>Full Name <span class="req">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="row">
                <div class="field">
                    <label>Phone <span class="req">*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required>
                </div>
                <div class="field">
                    <label>Email <span class="req">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="row">
                <div class="field">
                    <label>Password <span class="req">*</span></label>
                    <input type="password" name="password" required minlength="8">
                </div>
                <div class="field">
                    <label>Confirm Password <span class="req">*</span></label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>

            <div class="section-title">Professional Details</div>

            <div class="row">
                <div class="field">
                    <label>Registration Number <span class="req">*</span></label>
                    <input type="text" name="registration_number" value="{{ old('registration_number') }}" required placeholder="e.g. KSVC-12345">
                </div>
                <div class="field">
                    <label>Degree <span class="req">*</span></label>
                    <input type="text" name="degree" value="{{ old('degree') }}" required placeholder="e.g. BVSc & AH">
                </div>
            </div>

            <div class="field">
                <label>Specialization</label>
                <input type="text" name="specialization" value="{{ old('specialization') }}" placeholder="e.g. Small Animal Surgery">
            </div>

            <div class="field">
                <label>Skills</label>
                <textarea name="skills" placeholder="Describe your skills and areas of expertise...">{{ old('skills') }}</textarea>
            </div>

            <div class="section-title">Documents</div>

            <div class="field">
                <label>Practicing License</label>
                <input type="file" name="practicing_license" accept=".pdf,.jpg,.jpeg,.png">
                <div class="hint">PDF, JPG, or PNG. Max 5MB.</div>
            </div>

            <div class="field">
                <label>Certificates</label>
                <input type="file" name="certificates[]" accept=".pdf,.jpg,.jpeg,.png" multiple>
                <div class="hint">Upload multiple certificates. PDF, JPG, or PNG. Max 5MB each.</div>
            </div>

            <div class="field">
                <label>Signature</label>
                <input type="file" name="signature" accept=".jpg,.jpeg,.png">
                <div class="hint">Upload an image of your handwritten signature. JPG or PNG. Max 2MB. This will appear on prescriptions and case sheets.</div>
            </div>

            <button type="submit" class="btn">Create Account</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="{{ route('vet.login') }}">Log in</a>
        </div>
    </div>
</body>
</html>
