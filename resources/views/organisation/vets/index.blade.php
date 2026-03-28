@extends('organisation.layout')

@section('content')

<style>
.page-title { font-size:22px; font-weight:600; margin-bottom:20px; }
.card { background:#fff; padding:20px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.05); margin-bottom:24px; }
.search-bar { display:flex; gap:10px; max-width:520px; }
.search-bar input { flex:1; padding:10px 12px; border-radius:8px; border:1px solid #d1d5db; font-size:14px; }
.search-bar input:focus { outline:none; border-color:#4f46e5; box-shadow:0 0 0 2px rgba(79,70,229,0.15); }
.search-bar button { padding:10px 16px; border-radius:8px; border:none; background:#4f46e5; color:#fff; cursor:pointer; font-weight:600; }
.vet-muted { font-size:13px; color:#6b7280; }
.table-wrap { overflow-x:auto; }
table { width:100%; border-collapse:collapse; }
th { text-align:left; font-size:12px; color:#6b7280; padding:10px; border-bottom:1px solid #e5e7eb; text-transform:uppercase; letter-spacing:.5px; }
td { padding:12px 10px; border-bottom:1px solid #f1f5f9; font-size:14px; }
tr:hover td { background:#f9fafb; }
.actions a, .actions button { font-size:13px; margin-right:10px; text-decoration:none; background:none; border:none; cursor:pointer; }
.actions a { color:#2563eb; }
.actions button { color:#dc2626; }
.badge { display:inline-block; padding:3px 8px; border-radius:10px; font-size:10px; font-weight:600; }
.badge-active { background:#dcfce7; color:#166534; }
.badge-pending { background:#fef3c7; color:#92400e; }

/* Toggle */
.toggle-section { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:16px 20px; display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
.toggle-label { font-size:14px; font-weight:600; color:#111827; }
.toggle-desc { font-size:12px; color:#6b7280; margin-top:2px; }
.toggle-wrap { display:flex; align-items:center; gap:10px; }
.toggle-status { font-size:12px; font-weight:700; padding:3px 10px; border-radius:6px; }
.toggle-status--on { background:#dcfce7; color:#16a34a; }
.toggle-status--off { background:#fee2e2; color:#991b1b; }
.toggle-switch { position:relative; width:52px; height:28px; flex-shrink:0; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-slider { position:absolute; cursor:pointer; inset:0; background:#cbd5e1; border-radius:28px; transition:.25s; box-shadow:inset 0 1px 3px rgba(0,0,0,0.15); }
.toggle-slider:before { content:''; position:absolute; width:22px; height:22px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.25s; box-shadow:0 1px 3px rgba(0,0,0,0.2); }
.toggle-switch input:checked + .toggle-slider { background:#16a34a; box-shadow:inset 0 1px 3px rgba(0,0,0,0.1); }
.toggle-switch input:checked + .toggle-slider:before { transform:translateX(24px); }
.success-bar { background:#dcfce7; border:1px solid #bbf7d0; padding:10px 14px; border-radius:6px; margin-bottom:14px; color:#166534; font-size:14px; }
</style>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <h2 class="page-title" style="margin-bottom:0;">Vet Management</h2>
    <a href="{{ url('/organisation/jobs') }}" style="padding:8px 14px;background:#7c3aed;color:#fff;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;">📋 Hiring Portal</a>
</div>

@if(session('success'))<div class="success-bar">✓ {{ session('success') }}</div>@endif

{{-- Vet Can Select Lab Toggle --}}
@php $orgModel = \App\Models\Organisation::find(auth()->user()->organisation_id); @endphp
<div class="toggle-section">
    <div>
        <div class="toggle-label">Allow vets to select labs when ordering tests</div>
        <div class="toggle-desc">When OFF, vets can only order tests — clinic manager selects which lab to send to.</div>
    </div>
    <form method="POST" action="{{ route('organisation.settings.toggle-vet-lab') }}">
        @csrf
        <div class="toggle-wrap">
            <span class="toggle-status {{ ($orgModel && $orgModel->vet_can_select_lab) ? 'toggle-status--on' : 'toggle-status--off' }}">
                {{ ($orgModel && $orgModel->vet_can_select_lab) ? 'ON' : 'OFF' }}
            </span>
            <label class="toggle-switch">
                <input type="checkbox" name="vet_can_select_lab" value="1" {{ ($orgModel && $orgModel->vet_can_select_lab) ? 'checked' : '' }} onchange="this.form.submit()">
                <span class="toggle-slider"></span>
            </label>
        </div>
    </form>
</div>

{{-- SEARCH --}}
<div class="card">
    <h3 style="margin:0 0 12px;font-size:15px;font-weight:700;">🔍 Onboard a Vet</h3>
    <form method="GET" class="search-bar">
        <input name="q" placeholder="Search by email, phone, or registration number" value="{{ request('q') }}">
        <button>Search</button>
    </form>

    @if($searchedVet)
        <div style="margin-top:14px;padding:14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <strong style="font-size:15px;">{{ $searchedVet->name }}</strong>
                    <div class="vet-muted">{{ $searchedVet->email }} · {{ $searchedVet->phone }} · Reg: {{ $searchedVet->registration_number ?? '—' }}</div>
                    @if($searchedVet->specialization)<div class="vet-muted">{{ $searchedVet->specialization }}</div>@endif
                </div>
                <a href="{{ route('organisation.vets.show', $searchedVet) }}" style="padding:8px 14px;background:#4f46e5;color:#fff;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;">View / Assign</a>
            </div>
        </div>
    @elseif(request()->filled('q'))
        <p class="vet-muted" style="margin-top:10px;">No vet found. Make sure the vet has registered on ClinicDesq.</p>
    @endif
</div>

{{-- ASSIGNED VETS --}}
<div class="card">
    <h3 style="margin:0 0 12px;font-size:15px;font-weight:700;">Your Vets ({{ $assignedVets->count() }})</h3>
    <div class="table-wrap">
    <table>
        <tr>
            <th>Name</th>
            <th>Email / Phone</th>
            <th>Clinics</th>
            <th>Cases</th>
            <th>Tenure</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        @forelse($assignedVets as $vet)
            @php
                $clinicIds = $vet->clinics->pluck('id');
                $caseCount = \App\Models\Appointment::where('vet_id', $vet->id)->whereIn('clinic_id', $clinicIds)->count();
                $tenure = optional(
                    \DB::table('clinic_vet')->where('vet_id', $vet->id)->where('is_active', 1)->orderBy('created_at')->first()
                )->created_at;
                $pivotStatus = \DB::table('clinic_vet')->where('vet_id', $vet->id)->whereIn('clinic_id', $clinicIds)->value('status');
            @endphp
            <tr>
                <td><strong>{{ $vet->name }}</strong></td>
                <td><span class="vet-muted">{{ $vet->email }}<br>{{ $vet->phone }}</span></td>
                <td>{{ $vet->clinics->pluck('name')->join(', ') }}</td>
                <td>{{ $caseCount }}</td>
                <td class="vet-muted">{{ $tenure ? \Carbon\Carbon::parse($tenure)->diffForHumans(null, true) : '—' }}</td>
                <td>
                    @if($pivotStatus === 'pending')
                        <span class="badge badge-pending">Pending</span>
                    @else
                        <span class="badge badge-active">Active</span>
                    @endif
                </td>
                <td class="actions">
                    <a href="{{ route('organisation.vets.show', $vet) }}">Manage</a>
                    <form method="POST" action="{{ route('organisation.vets.offboard', $vet) }}" style="display:inline" onsubmit="return confirm('Offboard this vet?')">
                        @csrf
                        <button>Offboard</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="vet-muted" style="text-align:center;padding:30px;">No vets onboarded yet. Search above to find and assign vets.</td></tr>
        @endforelse
    </table>
    </div>
</div>

@endsection
