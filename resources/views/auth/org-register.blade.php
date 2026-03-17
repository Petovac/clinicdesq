<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Organisation — ClinicDesq</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%); color: #1a1a2e; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }

        .card { background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,0.2); padding: 40px; width: 100%; max-width: 640px; position: relative; overflow: hidden; }
        .card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #2563eb, #7c3aed, #06b6d4); }

        .brand { text-align: center; margin-bottom: 28px; }
        .brand h1 { font-size: 26px; font-weight: 800; background: linear-gradient(135deg, #2563eb, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .brand p { font-size: 14px; color: #64748b; margin-top: 4px; }

        /* Steps indicator */
        .steps { display: flex; justify-content: center; gap: 8px; margin-bottom: 32px; }
        .step-dot { display: flex; align-items: center; gap: 8px; }
        .step-num { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; border: 2px solid #e2e8f0; color: #94a3b8; transition: all 0.3s; }
        .step-num.active { background: #2563eb; border-color: #2563eb; color: #fff; }
        .step-num.done { background: #10b981; border-color: #10b981; color: #fff; }
        .step-line { width: 40px; height: 2px; background: #e2e8f0; transition: background 0.3s; }
        .step-line.done { background: #10b981; }
        .step-label { font-size: 11px; color: #94a3b8; text-align: center; margin-top: 4px; }
        .step-label.active { color: #2563eb; font-weight: 600; }

        /* Form — use visibility instead of display:none so hidden password fields still submit */
        .step-panel { position: absolute; left: -9999px; opacity: 0; pointer-events: none; }
        .step-panel.active { position: static; left: auto; opacity: 1; pointer-events: auto; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .section-title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
        .section-desc { font-size: 13px; color: #64748b; margin-bottom: 20px; }

        .row { display: flex; gap: 12px; }
        .row > .field { flex: 1; }
        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 5px; }
        .field label .req { color: #dc2626; }
        .field input, .field select { width: 100%; padding: 10px 14px; font-size: 14px; border: 1.5px solid #d1d5db; border-radius: 10px; outline: none; transition: all 0.2s; background: #f8fafc; }
        .field input:focus, .field select:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background: #fff; }
        .field .hint { font-size: 11px; color: #9ca3af; margin-top: 3px; }

        .btn-row { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s; flex: 1; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; transform: translateY(-1px); }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-success { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
        .btn-success:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16,185,129,0.3); }

        /* Package cards */
        .packages { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 16px; }
        .pkg-card { border: 2px solid #e2e8f0; border-radius: 14px; padding: 20px 16px; text-align: center; cursor: pointer; transition: all 0.2s; position: relative; }
        .pkg-card:hover { border-color: #93c5fd; transform: translateY(-2px); }
        .pkg-card.selected { border-color: #2563eb; background: #eff6ff; box-shadow: 0 0 0 3px rgba(37,99,235,0.15); }
        .pkg-card input[type="radio"] { display: none; }
        .pkg-name { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
        .pkg-price { font-size: 22px; font-weight: 800; color: #2563eb; }
        .pkg-price .per { font-size: 12px; font-weight: 500; color: #64748b; }
        .pkg-price .original { text-decoration: line-through; color: #94a3b8; font-size: 13px; font-weight: 400; margin-right: 4px; }
        .pkg-trial { font-size: 11px; color: #10b981; font-weight: 600; margin-top: 6px; background: #d1fae5; padding: 3px 8px; border-radius: 20px; display: inline-block; }
        .pkg-limits { font-size: 11px; color: #64748b; margin-top: 8px; line-height: 1.6; }
        .pkg-features { text-align: left; margin-top: 10px; padding-top: 10px; border-top: 1px solid #e2e8f0; }
        .pkg-features li { font-size: 11px; color: #475569; list-style: none; padding: 2px 0; }
        .pkg-features li::before { content: '\2713'; color: #10b981; font-weight: 700; margin-right: 6px; }

        .alert { padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        .login-link { text-align: center; margin-top: 20px; font-size: 13px; color: #64748b; }
        .login-link a { color: #2563eb; text-decoration: none; font-weight: 600; }

        .password-strength { height: 3px; border-radius: 2px; margin-top: 6px; transition: all 0.3s; }

        @media (max-width: 540px) {
            .row { flex-direction: column; gap: 0; }
            .card { padding: 28px 20px; }
            .packages { grid-template-columns: 1fr; }
            .step-line { width: 24px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <h1>ClinicDesq</h1>
            <p>Register your veterinary practice</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- Steps Indicator -->
        <div class="steps">
            <div style="text-align:center">
                <div class="step-num active" id="sn1">1</div>
                <div class="step-label active" id="sl1">Admin</div>
            </div>
            <div class="step-line" id="line1" style="align-self:center;margin-bottom:16px"></div>
            <div style="text-align:center">
                <div class="step-num" id="sn2">2</div>
                <div class="step-label" id="sl2">Organisation</div>
            </div>
            <div class="step-line" id="line2" style="align-self:center;margin-bottom:16px"></div>
            <div style="text-align:center">
                <div class="step-num" id="sn3">3</div>
                <div class="step-label" id="sl3">Plan</div>
            </div>
        </div>

        <form method="POST" action="{{ route('org.register') }}" id="regForm" novalidate>
            @csrf

            <!-- STEP 1: Admin Details -->
            <div class="step-panel active" id="step1">
                <div class="section-title">Admin Account</div>
                <div class="section-desc">You'll be the organisation owner with full access.</div>

                <div class="field">
                    <label>Full Name <span class="req">*</span></label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" required placeholder="Dr. Jane Smith">
                </div>

                <div class="row">
                    <div class="field">
                        <label>Email <span class="req">*</span></label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" required placeholder="jane@example.com">
                        <div class="hint">This will be your login email</div>
                    </div>
                    <div class="field">
                        <label>Phone <span class="req">*</span></label>
                        <input type="tel" name="admin_phone" value="{{ old('admin_phone') }}" required placeholder="+91 98765 43210">
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Password <span class="req">*</span></label>
                        <input type="password" name="password" id="password" required minlength="8" placeholder="Min 8 characters">
                    </div>
                    <div class="field">
                        <label>Confirm Password <span class="req">*</span></label>
                        <input type="password" name="password_confirmation" required placeholder="Re-enter password">
                    </div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn btn-primary" onclick="goStep(2)">Continue &rarr;</button>
                </div>
            </div>

            <!-- STEP 2: Organisation Details -->
            <div class="step-panel" id="step2">
                <div class="section-title">Organisation Details</div>
                <div class="section-desc">Tell us about your veterinary practice.</div>

                <div class="field">
                    <label>Organisation Name <span class="req">*</span></label>
                    <input type="text" name="org_name" value="{{ old('org_name') }}" required placeholder="Happy Paws Veterinary Hospital">
                </div>

                <div class="row">
                    <div class="field">
                        <label>Type <span class="req">*</span></label>
                        <select name="org_type" required>
                            <option value="">Select type...</option>
                            <option value="single_clinic" {{ old('org_type') == 'single_clinic' ? 'selected' : '' }}>Single Clinic</option>
                            <option value="hospital" {{ old('org_type') == 'hospital' ? 'selected' : '' }}>Hospital</option>
                            <option value="multi_clinic" {{ old('org_type') == 'multi_clinic' ? 'selected' : '' }}>Multi-Clinic Chain</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label>Organisation Email</label>
                        <input type="email" name="org_email" value="{{ old('org_email') }}" placeholder="info@happypaws.com">
                        <div class="hint">Defaults to admin email if empty</div>
                    </div>
                    <div class="field">
                        <label>Organisation Phone</label>
                        <input type="tel" name="org_phone" value="{{ old('org_phone') }}" placeholder="+91 80 1234 5678">
                    </div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn btn-secondary" onclick="goStep(1)">&larr; Back</button>
                    <button type="button" class="btn btn-primary" onclick="goStep(3)">Continue &rarr;</button>
                </div>
            </div>

            <!-- STEP 3: Plan Selection -->
            <div class="step-panel" id="step3">
                <div class="section-title">Choose Your Plan</div>
                <div class="section-desc">Start with a free trial. All features unlocked during trial period.</div>

                <div class="packages">
                    @foreach($packages as $pkg)
                    <label class="pkg-card" onclick="selectPkg(this, {{ $pkg->id }})">
                        <input type="radio" name="package_id" value="{{ $pkg->id }}" {{ old('package_id') == $pkg->id ? 'checked' : '' }}>
                        <div class="pkg-name">{{ $pkg->name }}</div>
                        <div class="pkg-price">
                            @if($pkg->original_price)
                                <span class="original">&#8377;{{ number_format($pkg->original_price) }}</span>
                            @endif
                            &#8377;{{ number_format($pkg->price_per_doctor) }}
                            <span class="per">/doctor/mo</span>
                        </div>
                        <div class="pkg-trial">{{ $pkg->trial_days ?? 30 }}-day free trial</div>
                        <div class="pkg-limits">
                            Up to {{ $pkg->max_clinics ?? 'unlimited' }} clinic{{ ($pkg->max_clinics ?? 0) != 1 ? 's' : '' }}
                            &middot; {{ $pkg->max_doctors ?? 'unlimited' }} doctor{{ ($pkg->max_doctors ?? 0) != 1 ? 's' : '' }}
                        </div>
                        @if($pkg->features)
                        <ul class="pkg-features">
                            @foreach(array_slice($pkg->features, 0, 5) as $feat)
                                <li>{{ ucfirst(str_replace('_', ' ', $feat)) }}</li>
                            @endforeach
                            @if(count($pkg->features) > 5)
                                <li style="color:#2563eb">+ {{ count($pkg->features) - 5 }} more</li>
                            @endif
                        </ul>
                        @endif
                    </label>
                    @endforeach
                </div>

                <div class="btn-row">
                    <button type="button" class="btn btn-secondary" onclick="goStep(2)">&larr; Back</button>
                    <button type="submit" class="btn btn-success" id="submitBtn">Start Free Trial &rarr;</button>
                </div>
            </div>
        </form>

        <div class="login-link">
            Already have an account? <a href="{{ route('staff.login') }}">Log in</a>
            &nbsp;&middot;&nbsp;
            <a href="{{ route('vet.register') }}">Register as a Vet</a>
        </div>
    </div>

    <script>
        let currentStep = 1;

        let skipValidation = false;

        function goStep(step) {
            // Validate current step before advancing (skip if returning from server error)
            if (step > currentStep && !skipValidation && !validateStep(currentStep)) return;

            document.getElementById('step' + currentStep).classList.remove('active');
            document.getElementById('step' + step).classList.add('active');

            // Update indicators
            for (let i = 1; i <= 3; i++) {
                const sn = document.getElementById('sn' + i);
                const sl = document.getElementById('sl' + i);
                sn.classList.remove('active', 'done');
                sl.classList.remove('active');

                if (i < step) {
                    sn.classList.add('done');
                    sn.textContent = '\u2713';
                } else if (i === step) {
                    sn.classList.add('active');
                    sl.classList.add('active');
                    sn.textContent = i;
                } else {
                    sn.textContent = i;
                }
            }

            // Update lines
            const line1 = document.getElementById('line1');
            const line2 = document.getElementById('line2');
            line1.classList.toggle('done', step > 1);
            line2.classList.toggle('done', step > 2);

            currentStep = step;
        }

        function validateStep(step) {
            const panel = document.getElementById('step' + step);
            const required = panel.querySelectorAll('[required]');
            let valid = true;

            required.forEach(el => {
                if (!el.value.trim()) {
                    el.style.borderColor = '#dc2626';
                    valid = false;
                    el.addEventListener('input', () => el.style.borderColor = '', { once: true });
                }
            });

            if (step === 1) {
                const pw = document.querySelector('[name="password"]').value;
                const pwc = document.querySelector('[name="password_confirmation"]').value;
                if (pw && pwc && pw !== pwc) {
                    alert('Passwords do not match.');
                    valid = false;
                }
                if (pw && pw.length < 8) {
                    alert('Password must be at least 8 characters.');
                    valid = false;
                }
            }

            if (!valid) {
                panel.querySelector('[required]:invalid, [style*="border-color: rgb(220, 38, 38)"]')?.focus();
            }

            return valid;
        }

        function selectPkg(el, id) {
            document.querySelectorAll('.pkg-card').forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
            el.querySelector('input[type="radio"]').checked = true;
        }

        // On submit: make all panels active so every field is included
        document.getElementById('regForm').addEventListener('submit', function() {
            document.querySelectorAll('.step-panel').forEach(p => p.classList.add('active'));
        });

        // Auto-select if old value or first package
        document.addEventListener('DOMContentLoaded', function() {
            const checked = document.querySelector('.pkg-card input:checked');
            if (checked) {
                checked.closest('.pkg-card').classList.add('selected');
            }

            // If there are validation errors, jump to the relevant step
            @if($errors->any())
                skipValidation = true;
                @if($errors->has('admin_name') || $errors->has('admin_email') || $errors->has('admin_phone') || $errors->has('password'))
                    goStep(1);
                @elseif($errors->has('org_name') || $errors->has('org_type') || $errors->has('org_email') || $errors->has('org_phone'))
                    goStep(2);
                @else
                    goStep(3);
                @endif
                skipValidation = false;
            @endif
        });
    </script>
</body>
</html>
