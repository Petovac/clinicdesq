<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Thank You — {{ $review->clinic->name ?? 'ClinicDesq' }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Inter,system-ui,sans-serif; background:#f0fdf4; color:#111827; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
.card { background:#fff; border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,0.08); max-width:440px; width:100%; padding:40px 32px; text-align:center; }
.icon { font-size:48px; margin-bottom:16px; }
.title { font-size:22px; font-weight:700; margin-bottom:8px; }
.subtitle { font-size:14px; color:#6b7280; margin-bottom:24px; line-height:1.6; }
.gmb-box { background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:20px; margin-bottom:16px; }
.gmb-box h3 { font-size:14px; font-weight:700; color:#1e40af; margin-bottom:8px; }
.gmb-box p { font-size:13px; color:#374151; margin-bottom:12px; }
.gmb-btn { display:inline-block; background:#4285f4; color:#fff; padding:12px 24px; border-radius:8px; font-weight:700; font-size:14px; text-decoration:none; }
.gmb-btn:hover { background:#3367d6; }
.powered { margin-top:20px; font-size:11px; color:#9ca3af; }
</style>
</head>
<body>

<div class="card">
    <div class="icon">🎉</div>
    <div class="title">Thank You!</div>
    <div class="subtitle">
        Your feedback helps {{ $review->clinic->name ?? 'us' }} improve.<br>
        We truly appreciate you taking the time.
    </div>

    @if(($showGmb ?? false) && ($gmbUrl ?? null))
    <div class="gmb-box">
        <h3>Loved your experience?</h3>
        <p>Help others find us by leaving a quick Google review!</p>
        <a href="{{ $gmbUrl }}" target="_blank" class="gmb-btn">
            ⭐ Leave a Google Review
        </a>
    </div>
    @endif

    <div class="powered">Powered by ClinicDesq</div>
</div>

</body>
</html>
