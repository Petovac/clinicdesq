<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Rate Your Visit — {{ $review->clinic->name }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Inter,system-ui,sans-serif; background:#f8fafc; color:#111827; min-height:100vh; display:flex; justify-content:center; padding:20px; }
.review-card { background:#fff; border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,0.08); max-width:480px; width:100%; padding:32px; margin-top:20px; }
.clinic-header { text-align:center; margin-bottom:24px; }
.clinic-name { font-size:20px; font-weight:700; color:#111827; }
.clinic-sub { font-size:13px; color:#6b7280; margin-top:4px; }
.pet-info { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px; text-align:center; margin-bottom:24px; font-size:14px; color:#166534; }
.pet-info strong { color:#111827; }

.rating-section { margin-bottom:20px; }
.rating-label { font-size:13px; font-weight:600; color:#374151; margin-bottom:8px; }
.stars { display:flex; gap:6px; }
.star { width:40px; height:40px; cursor:pointer; border-radius:8px; border:2px solid #e5e7eb; display:flex; align-items:center; justify-content:center; font-size:20px; transition:all .15s; background:#fff; }
.star:hover, .star.active { border-color:#f59e0b; background:#fef3c7; }
.star.active { background:#f59e0b; color:#fff; border-color:#f59e0b; }
.required { font-size:10px; color:#ef4444; margin-left:4px; }

.sub-ratings { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:20px; }
.sub-rating { background:#f9fafb; border:1px solid #f3f4f6; border-radius:8px; padding:10px; }
.sub-rating .label { font-size:11px; font-weight:600; color:#6b7280; margin-bottom:6px; }
.mini-stars { display:flex; gap:3px; }
.mini-star { width:26px; height:26px; cursor:pointer; border-radius:4px; border:1px solid #e5e7eb; display:flex; align-items:center; justify-content:center; font-size:14px; transition:all .15s; background:#fff; }
.mini-star:hover, .mini-star.active { border-color:#f59e0b; background:#fef3c7; }
.mini-star.active { background:#f59e0b; color:#fff; }

.form-group { margin-bottom:16px; }
.form-group label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:4px; }
.form-group textarea { width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; resize:vertical; min-height:80px; font-family:inherit; }
.form-group textarea:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 2px rgba(37,99,235,0.15); }

.recommend { display:flex; gap:10px; margin-bottom:20px; }
.rec-btn { flex:1; padding:12px; border-radius:8px; border:2px solid #e5e7eb; cursor:pointer; text-align:center; font-weight:600; font-size:14px; transition:all .15s; background:#fff; }
.rec-btn:hover { border-color:#2563eb; }
.rec-btn.yes.active { background:#dcfce7; border-color:#16a34a; color:#166534; }
.rec-btn.no.active { background:#fee2e2; border-color:#ef4444; color:#991b1b; }

.submit-btn { width:100%; padding:14px; background:#2563eb; color:#fff; border:none; border-radius:10px; font-size:16px; font-weight:700; cursor:pointer; transition:all .15s; }
.submit-btn:hover { background:#1d4ed8; }
.submit-btn:disabled { background:#d1d5db; cursor:not-allowed; }

.powered { text-align:center; margin-top:16px; font-size:11px; color:#9ca3af; }
</style>
</head>
<body>

<div class="review-card">
    <div class="clinic-header">
        @if($review->clinic->organisation->logo_path ?? null)
            <img src="{{ asset('storage/'.$review->clinic->organisation->logo_path) }}" style="max-height:40px;margin-bottom:8px;">
        @endif
        <div class="clinic-name">{{ $review->clinic->name }}</div>
        <div class="clinic-sub">{{ $review->clinic->organisation->name ?? '' }}</div>
    </div>

    @if($review->appointment && $review->appointment->pet)
    <div class="pet-info">
        How was <strong>{{ $review->appointment->pet->name }}</strong>'s visit?
        @if($review->vet)
            <br>with <strong>{{ str_starts_with($review->vet->name, 'Dr') ? $review->vet->name : 'Dr. '.$review->vet->name }}</strong>
        @endif
    </div>
    @endif

    <form method="POST" action="{{ url('/review/'.$review->token) }}" id="reviewForm">
        @csrf

        {{-- Overall Rating --}}
        <div class="rating-section">
            <div class="rating-label">Overall Experience <span class="required">*</span></div>
            <div class="stars" id="overall-stars">
                @for($i = 1; $i <= 5; $i++)
                <div class="star" data-value="{{ $i }}" onclick="setRating('overall_rating', {{ $i }}, this)">{{ $i <= 3 ? '★' : '★' }}</div>
                @endfor
            </div>
            <input type="hidden" name="overall_rating" id="overall_rating" required>
        </div>

        {{-- Sub Ratings --}}
        <div class="sub-ratings">
            @foreach(['doctor_rating' => 'Doctor', 'staff_rating' => 'Staff', 'cleanliness_rating' => 'Cleanliness', 'wait_time_rating' => 'Wait Time'] as $field => $label)
            <div class="sub-rating">
                <div class="label">{{ $label }}</div>
                <div class="mini-stars" id="{{ $field }}-stars">
                    @for($i = 1; $i <= 5; $i++)
                    <div class="mini-star" data-value="{{ $i }}" onclick="setRating('{{ $field }}', {{ $i }}, this)">★</div>
                    @endfor
                </div>
                <input type="hidden" name="{{ $field }}" id="{{ $field }}">
            </div>
            @endforeach
        </div>

        {{-- Would Recommend --}}
        <div class="rating-label">Would you recommend us?</div>
        <div class="recommend">
            <div class="rec-btn yes" onclick="setRecommend(1, this)">👍 Yes</div>
            <div class="rec-btn no" onclick="setRecommend(0, this)">👎 No</div>
        </div>
        <input type="hidden" name="would_recommend" id="would_recommend">

        {{-- Feedback --}}
        <div class="form-group">
            <label>Tell us more (optional)</label>
            <textarea name="feedback" placeholder="What did you like? What can we improve?"></textarea>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn" disabled>Rate & Submit</button>
    </form>

    <div class="powered">Powered by ClinicDesq</div>
</div>

<script>
function setRating(field, value, el) {
    document.getElementById(field).value = value;
    const parent = el.parentElement;
    parent.querySelectorAll('.star, .mini-star').forEach((s, i) => {
        s.classList.toggle('active', i < value);
    });
    checkReady();
}

function setRecommend(val, el) {
    document.getElementById('would_recommend').value = val;
    document.querySelectorAll('.rec-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
}

function checkReady() {
    const overall = document.getElementById('overall_rating').value;
    document.getElementById('submitBtn').disabled = !overall;
}
</script>

</body>
</html>
