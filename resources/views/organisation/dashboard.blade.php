@extends('organisation.layout')

@section('content')
<style>
    .dash-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:24px; }
    .dash-header h2 { font-size:24px;font-weight:700;color:#111827;margin:0; }
    .dash-header .org-info { font-size:13px;color:#6b7280;margin-top:4px; }
    .dash-header .org-info strong { color:#374151; }

    .metrics-row { display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:28px; }
    .metric { background:#fff;padding:16px 18px;border-radius:10px;border:1px solid #f3f4f6;box-shadow:0 1px 4px rgba(0,0,0,0.03); }
    .metric .label { font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:#6b7280;margin-bottom:4px; }
    .metric .value { font-size:26px;font-weight:700;color:#111827; }
    .metric .sub { font-size:11px;color:#9ca3af;margin-top:2px; }
    .metric--highlight { background:linear-gradient(135deg,#eff6ff,#dbeafe);border-color:#bfdbfe; }
    .metric--highlight .value { color:#1d4ed8; }
    .metric--green { background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-color:#bbf7d0; }
    .metric--green .value { color:#16a34a; }

    .section-title { font-size:16px;font-weight:700;color:#111827;margin-bottom:14px;margin-top:8px; }
    .section-sub { font-size:12px;color:#6b7280;margin-bottom:16px; }

    .rank-table { width:100%;border-collapse:collapse;font-size:13px; }
    .rank-table th { text-align:left;padding:10px 14px;font-weight:600;color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
    .rank-table td { padding:10px 14px;border-bottom:1px solid #f3f4f6;color:#374151; }
    .rank-table tr:hover td { background:#f9fafb; }
    .rank-badge { display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;font-size:11px;font-weight:700; }
    .rank-1 { background:#fef3c7;color:#92400e; }
    .rank-2 { background:#e5e7eb;color:#374151; }
    .rank-3 { background:#fed7aa;color:#9a3412; }
    .rank-other { background:#f3f4f6;color:#6b7280; }
    .money { font-weight:600;color:#16a34a; }
    .pct-badge { display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600; }
    .pct-high { background:#dcfce7;color:#166534; }
    .pct-med { background:#fef3c7;color:#92400e; }
    .pct-low { background:#fee2e2;color:#991b1b; }

    .card { background:#fff;border-radius:10px;border:1px solid #f3f4f6;box-shadow:0 1px 4px rgba(0,0,0,0.03);padding:0;overflow:hidden;margin-bottom:24px; }
    .card-header { padding:16px 20px;border-bottom:1px solid #f3f4f6; }

    .quick-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-bottom:28px; }
    .quick-card { background:#fff;padding:18px;border-radius:10px;border:1px solid #f3f4f6;box-shadow:0 1px 4px rgba(0,0,0,0.03);transition:all 0.15s; }
    .quick-card:hover { box-shadow:0 4px 16px rgba(0,0,0,0.06);border-color:#e0e7ff; }
    .quick-card h3 { margin:0 0 4px;font-size:14px;font-weight:700;color:#111827; }
    .quick-card p { margin:0 0 10px;font-size:11px;color:#6b7280; }
    .quick-card a { text-decoration:none;color:#4f46e5;font-weight:600;font-size:12px; }
    .quick-card a:hover { text-decoration:underline; }
</style>

<div class="dash-header">
    <div>
        <h2>Organisation Dashboard</h2>
        <div class="org-info"><strong>{{ $organisation->name }}</strong> &middot; {{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
    </div>
</div>

{{-- ═══════ TOP METRICS ═══════ --}}
<div class="metrics-row">
    <div class="metric metric--highlight">
        <div class="label">Total Revenue</div>
        <div class="value">₹{{ number_format($totalRevenue) }}</div>
        <div class="sub">₹{{ number_format($monthRevenue) }} this month</div>
    </div>
    <div class="metric">
        <div class="label">Appointments</div>
        <div class="value">{{ number_format($totalAppointments) }}</div>
        <div class="sub">{{ $monthAppointments }} this month · {{ $completedAppointments }} completed</div>
    </div>
    <div class="metric metric--green">
        <div class="label">Clients</div>
        <div class="value">{{ number_format($totalClients) }}</div>
        <div class="sub">{{ $repeatClients }} repeat ({{ $repeatPercentage }}%)</div>
    </div>
    <div class="metric">
        <div class="label">Clinics</div>
        <div class="value">{{ $totalClinics }}</div>
    </div>
    <div class="metric">
        <div class="label">Doctors</div>
        <div class="value">{{ $totalVets }}</div>
    </div>
    <div class="metric">
        <div class="label">Staff</div>
        <div class="value">{{ $totalUsers }}</div>
    </div>
</div>

{{-- ═══════ CLINIC RANKING ═══════ --}}
<div class="section-title">Clinic Performance</div>
<div class="section-sub">Ranked by total revenue — all time</div>

<div class="card">
    <table class="rank-table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Clinic</th>
                <th>City</th>
                <th>Revenue (Total)</th>
                <th>Revenue (Month)</th>
                <th>Appointments</th>
                <th>This Month</th>
                <th>Doctors</th>
                <th>Clients</th>
                <th>Inventory Used</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clinicPerformance as $i => $cp)
                <tr>
                    <td>
                        <span class="rank-badge {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-other')) }}">
                            {{ $i + 1 }}
                        </span>
                    </td>
                    <td style="font-weight:600;">{{ $cp['clinic']->name }}</td>
                    <td>{{ $cp['clinic']->city ?? '—' }}</td>
                    <td class="money">₹{{ number_format($cp['revenue']) }}</td>
                    <td class="money">₹{{ number_format($cp['month_revenue']) }}</td>
                    <td>{{ $cp['appointments'] }}</td>
                    <td>{{ $cp['month_appointments'] }}</td>
                    <td>{{ $cp['doctors'] }}</td>
                    <td>{{ $cp['clients'] }}</td>
                    <td>{{ number_format($cp['inventory_used']) }} units</td>
                </tr>
            @empty
                <tr><td colspan="10" style="text-align:center;color:#9ca3af;padding:20px;">No clinic data yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ═══════ VET RANKING ═══════ --}}
<div class="section-title">Vet Performance</div>
<div class="section-sub">Ranked by revenue generated — repeat % shows client retention</div>

<div class="card">
    <table class="rank-table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Doctor</th>
                <th>Specialization</th>
                <th>Revenue</th>
                <th>Appointments</th>
                <th>Completed</th>
                <th>Unique Clients</th>
                <th>Repeat Clients</th>
                <th>Repeat %</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vetPerformance as $i => $vet)
                <tr>
                    <td>
                        <span class="rank-badge {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-other')) }}">
                            {{ $i + 1 }}
                        </span>
                    </td>
                    <td style="font-weight:600;">{{ $vet->name }}</td>
                    <td>{{ $vet->specialization ?? '—' }}</td>
                    <td class="money">₹{{ number_format($vet->revenue) }}</td>
                    <td>{{ $vet->total_appointments }}</td>
                    <td>{{ $vet->completed }}</td>
                    <td>{{ $vet->unique_clients }}</td>
                    <td>{{ $vet->repeat_clients }}</td>
                    <td>
                        <span class="pct-badge {{ $vet->repeat_pct >= 40 ? 'pct-high' : ($vet->repeat_pct >= 20 ? 'pct-med' : 'pct-low') }}">
                            {{ $vet->repeat_pct }}%
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center;color:#9ca3af;padding:20px;">No vet data yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ═══════ QUICK ACCESS ═══════ --}}
<div class="section-title">Quick Access</div>
<div class="quick-grid">
    @if($showClinics)
    <div class="quick-card">
        <h3>Clinics</h3>
        <p>Manage your clinic locations</p>
        <a href="{{ route('organisation.clinics.index') }}">Manage &rarr;</a>
    </div>
    @endif

    @if($showUsers)
    <div class="quick-card">
        <h3>Users & Roles</h3>
        <p>Staff accounts and permissions</p>
        <a href="{{ route('organisation.users.index') }}">Manage &rarr;</a>
    </div>
    @endif

    @if($showInventory)
    <div class="quick-card">
        <h3>Inventory</h3>
        <p>Central stock and clinic transfers</p>
        <a href="{{ route('organisation.inventory.items') }}">Manage &rarr;</a>
    </div>
    @endif

    <div class="quick-card">
        <h3>Lab Management</h3>
        <p>Test catalog and external labs</p>
        <a href="{{ route('organisation.lab-catalog.index') }}">Open &rarr;</a>
    </div>

    <div class="quick-card">
        <h3>Pricing</h3>
        <p>Price lists and fee configuration</p>
        <a href="{{ route('organisation.price-lists.index') }}">Manage &rarr;</a>
    </div>

    <div class="quick-card">
        <h3>Branding</h3>
        <p>Logo, templates, documents</p>
        <a href="{{ route('organisation.settings.branding') }}">Customize &rarr;</a>
    </div>
</div>
@endsection
