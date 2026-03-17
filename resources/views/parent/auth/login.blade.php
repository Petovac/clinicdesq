<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Parent Login — ClinicDesq</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f7fa; color: #1a1a2e; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 36px; width: 100%; max-width: 400px; }
        .brand { text-align: center; margin-bottom: 28px; }
        .brand h1 { font-size: 22px; font-weight: 700; color: #1e293b; }
        .brand p { font-size: 13px; color: #64748b; margin-top: 4px; }
        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 5px; }
        .field input { width: 100%; padding: 10px 14px; font-size: 15px; border: 1px solid #d1d5db; border-radius: 8px; outline: none; }
        .field input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .captcha { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 16px; }
        .captcha .question { font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 8px; }
        .captcha input { width: 100%; padding: 9px 12px; font-size: 15px; border: 1px solid #d1d5db; border-radius: 8px; outline: none; }
        .btn { display: block; width: 100%; padding: 12px; background: #2563eb; color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; }
        .btn:hover { background: #1d4ed8; }
        .error { color: #dc2626; font-size: 12px; margin-top: 4px; }
        .info { text-align: center; margin-top: 16px; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <h1>Pet Parent Portal</h1>
            <p>View your pets' health records</p>
        </div>

        @if($errors->any())
            <div style="background:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('parent.login') }}">
            @csrf

            <div class="field">
                <label>Phone Number</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Enter your registered phone number" required autofocus>
            </div>

            <div class="captcha">
                <div class="question">{{ $captcha_question }}</div>
                <input type="number" name="captcha_answer" placeholder="Your answer" required>
            </div>

            <button type="submit" class="btn">View My Pets</button>
        </form>

        <div class="info">
            Your phone number must be registered with a clinic to access records.
        </div>
    </div>
</body>
</html>
