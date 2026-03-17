@extends('organisation.layout')

@section('content')

<style>
.page-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:24px; }
.page-header h2 { font-size:22px;font-weight:600;margin:0;color:#111827; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.06);margin-bottom:16px; }
.labs-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;margin-top:16px; }
.lab-card { background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.06); }
.lab-card h3 { font-size:15px;font-weight:600;color:#111827;margin:0 0 8px 0; }
.lab-meta { font-size:13px;color:#6b7280;line-height:1.7; }
.lab-meta span { display:block; }
.lab-card-footer { margin-top:14px;padding-top:12px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center; }
.test-count { font-size:12px;font-weight:500;color:#2563eb;background:#eff6ff;padding:2px 10px;border-radius:12px; }
.btn-sm { padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;border:none;cursor:pointer;text-decoration:none; }
.btn-primary-sm { background:#2563eb;color:#fff; }
.btn-primary-sm:hover { background:#1d4ed8; }
.btn-outline-sm { background:#fff;color:#374151;border:1px solid #e5e7eb; }
.btn-outline-sm:hover { background:#f9fafb; }
.btn-danger-sm { background:#fee2e2;color:#991b1b; }
.btn-danger-sm:hover { background:#fecaca; }
.search-box { position:relative; }
.search-box input { width:100%;padding:12px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px; }
.search-box input:focus { outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
.search-results { position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,0.12);max-height:300px;overflow-y:auto;z-index:50;margin-top:4px;display:none; }
.search-results.active { display:block; }
.search-result-item { padding:12px 16px;cursor:pointer;border-bottom:1px solid #f3f4f6;transition:background 0.1s; }
.search-result-item:hover { background:#f0f9ff; }
.search-result-item:last-child { border-bottom:none; }
.search-result-item .lab-name { font-weight:600;font-size:14px;color:#111827; }
.search-result-item .lab-info { font-size:12px;color:#6b7280;margin-top:2px; }
.empty-state { text-align:center;padding:40px 0;color:#6b7280;font-size:14px;background:#fff;border:1px solid #e5e7eb;border-radius:10px; }
</style>

<div class="page-header">
    <h2>External Labs</h2>
</div>

@if(session('success'))
    <div style="background:#dcfce7;color:#166534;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="background:#fee2e2;color:#991b1b;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('error') }}</div>
@endif

{{-- Search & Onboard --}}
<div class="card">
    <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;color:#111827;">Search & Onboard a Lab</h3>
    <p style="font-size:13px;color:#6b7280;margin-bottom:12px;">Search for registered labs by name or city. Labs register on ClinicDesq independently — you link them to your organisation.</p>
    <div class="search-box">
        <input type="text" id="lab-search" placeholder="Search labs by name or city..." autocomplete="off">
        <div class="search-results" id="search-results"></div>
    </div>
</div>

{{-- Onboard confirmation form (hidden) --}}
<form method="POST" action="{{ route('organisation.labs.onboard') }}" id="onboard-form" style="display:none;">
    @csrf
    <input type="hidden" name="lab_id" id="onboard-lab-id">
</form>

{{-- Tied-up Labs --}}
<h3 style="font-size:16px;font-weight:700;margin-bottom:12px;margin-top:28px;">Your Labs ({{ $tiedUpLabs->count() }})</h3>

@if($tiedUpLabs->count())
    <div class="labs-grid">
        @foreach($tiedUpLabs as $lab)
            <div class="lab-card">
                <h3>{{ $lab->name }}</h3>
                <div class="lab-meta">
                    @if($lab->city)<span>{{ $lab->city }}{{ $lab->state ? ", {$lab->state}" : '' }}</span>@endif
                    @if($lab->phone)<span>{{ $lab->phone }}</span>@endif
                    @if($lab->email)<span>{{ $lab->email }}</span>@endif
                </div>
                <div class="lab-card-footer">
                    <span class="test-count">{{ $lab->testOfferings->count() }} tests</span>
                    <div style="display:flex;gap:6px;">
                        <a href="{{ route('organisation.labs.edit', $lab) }}" class="btn-sm btn-outline-sm">Manage</a>
                        <form method="POST" action="{{ route('organisation.labs.detach', $lab) }}" onsubmit="return confirm('Remove this lab from your organisation?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-danger-sm">Remove</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">No external labs onboarded yet. Search above to find and link labs.</div>
@endif

<script>
const searchInput = document.getElementById('lab-search');
const resultsBox = document.getElementById('search-results');
let debounce;

searchInput.addEventListener('input', function() {
    clearTimeout(debounce);
    const q = this.value.trim();
    if (q.length < 2) { resultsBox.classList.remove('active'); return; }

    debounce = setTimeout(() => {
        fetch(`{{ route('organisation.labs.search') }}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(labs => {
                if (labs.length === 0) {
                    resultsBox.innerHTML = '<div style="padding:16px;text-align:center;color:#6b7280;font-size:13px;">No labs found for "' + q + '"</div>';
                } else {
                    resultsBox.innerHTML = labs.map(lab => `
                        <div class="search-result-item" onclick="onboardLab(${lab.id}, '${lab.name.replace(/'/g, "\\'")}')">
                            <div class="lab-name">${lab.name}</div>
                            <div class="lab-info">${lab.city || ''}${lab.state ? ', ' + lab.state : ''} ${lab.phone ? '&middot; ' + lab.phone : ''}</div>
                        </div>
                    `).join('');
                }
                resultsBox.classList.add('active');
            });
    }, 300);
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.search-box')) resultsBox.classList.remove('active');
});

function onboardLab(id, name) {
    if (!confirm(`Onboard "${name}" to your organisation?`)) return;
    document.getElementById('onboard-lab-id').value = id;
    document.getElementById('onboard-form').submit();
}
</script>

@endsection
