@extends('organisation.layout')

@section('content')
<style>
/* ═══ Dashboard Layout ═══ */
.dash-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px; }
.dash-header h2 { font-size:22px;font-weight:700;color:#111827;margin:0; }
.dash-header .org-badge { font-size:12px;color:#6b7280;margin-top:2px; }

/* Period selector */
.period-bar { display:flex;gap:6px;background:#f3f4f6;padding:4px;border-radius:8px; }
.period-btn { padding:6px 14px;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:transparent;color:#6b7280;transition:all .15s; }
.period-btn:hover { color:#111827; }
.period-btn.active { background:#fff;color:#111827;box-shadow:0 1px 3px rgba(0,0,0,0.1); }

/* KPI Cards */
.kpi-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(155px,1fr));gap:12px;margin-bottom:24px; }
.kpi { background:#fff;padding:16px 18px;border-radius:10px;border:1px solid #f0f0f0;position:relative; }
.kpi .label { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-bottom:4px; }
.kpi .value { font-size:24px;font-weight:700;color:#111827; }
.kpi .trend { font-size:11px;font-weight:600;margin-top:4px; }
.kpi .trend.up { color:#16a34a; }
.kpi .trend.down { color:#ef4444; }
.kpi .trend.flat { color:#6b7280; }
.kpi--blue { border-left:3px solid #2563eb; }
.kpi--green { border-left:3px solid #16a34a; }
.kpi--purple { border-left:3px solid #7c3aed; }
.kpi--amber { border-left:3px solid #f59e0b; }
.kpi--teal { border-left:3px solid #0d9488; }
.kpi--red { border-left:3px solid #ef4444; }

/* Chart cards */
.chart-grid { display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px; }
.chart-card { background:#fff;border-radius:10px;border:1px solid #f0f0f0;padding:18px; }
.chart-card.full { grid-column:1/-1; }
.chart-card h3 { font-size:14px;font-weight:700;color:#111827;margin:0 0 12px; }
.chart-card .chart-sub { font-size:11px;color:#9ca3af;margin:-8px 0 12px; }

/* Table */
.lb-table { width:100%;border-collapse:collapse;font-size:13px; }
.lb-table th { text-align:left;padding:8px 12px;font-weight:600;color:#6b7280;font-size:10px;text-transform:uppercase;letter-spacing:.5px;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.lb-table td { padding:8px 12px;border-bottom:1px solid #f3f4f6;color:#374151; }
.lb-table tr:hover td { background:#f9fafb; }
.rank-badge { display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;font-size:10px;font-weight:700; }
.rank-1 { background:#fef3c7;color:#92400e; }
.rank-2 { background:#e5e7eb;color:#374151; }
.rank-3 { background:#fed7aa;color:#9a3412; }
.rank-n { background:#f3f4f6;color:#9ca3af; }
.money { font-weight:600;color:#16a34a; }
.pct-pill { display:inline-block;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600; }
.pct-high { background:#dcfce7;color:#166534; }
.pct-mid { background:#fef3c7;color:#92400e; }
.pct-low { background:#fee2e2;color:#991b1b; }

/* Insights */
.insights-grid { display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px; }
.insight { padding:12px 14px;border-radius:8px;font-size:13px;display:flex;gap:10px;align-items:flex-start; }
.insight .icon { font-size:18px;flex-shrink:0;margin-top:1px; }
.insight-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#166534; }
.insight-danger { background:#fef2f2;border:1px solid #fecaca;color:#991b1b; }
.insight-warning { background:#fffbeb;border:1px solid #fde68a;color:#92400e; }
.insight-info { background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af; }

/* Inventory alerts */
.alert-list { font-size:13px; }
.alert-item { padding:6px 0;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between; }
.alert-item:last-child { border-bottom:none; }
.stock-low { color:#f59e0b;font-weight:600; }
.stock-out { color:#ef4444;font-weight:600; }
.expiry-warn { color:#f59e0b;font-size:11px; }

@media (max-width:768px) {
    .chart-grid { grid-template-columns:1fr; }
    .insights-grid { grid-template-columns:1fr; }
    .kpi-grid { grid-template-columns:repeat(2,1fr); }
}
</style>

{{-- HEADER --}}
<div class="dash-header">
    <div>
        <h2>{{ $organisation->name }} Dashboard</h2>
        <div class="org-badge">{{ ucfirst(str_replace('_', ' ', $user->role)) }} &middot; {{ $days == 365 ? '12 months' : $days . ' days' }}</div>
    </div>
    <div class="period-bar">
        <a href="?period=7" class="period-btn {{ $days==7 ? 'active' : '' }}">7 Days</a>
        <a href="?period=30" class="period-btn {{ $days==30 ? 'active' : '' }}">30 Days</a>
        <a href="?period=90" class="period-btn {{ $days==90 ? 'active' : '' }}">90 Days</a>
        <a href="?period=365" class="period-btn {{ $days==365 ? 'active' : '' }}">12 Months</a>
    </div>
</div>

{{-- KPI CARDS --}}
<div class="kpi-grid">
    <div class="kpi kpi--green">
        <div class="label">Revenue</div>
        <div class="value">₹{{ number_format($kpis->revenue) }}</div>
        <div class="trend {{ $kpis->revTrend >= 0 ? 'up' : 'down' }}">
            {{ $kpis->revTrend >= 0 ? '↑' : '↓' }} {{ abs($kpis->revTrend) }}% vs prev period
        </div>
    </div>
    <div class="kpi kpi--blue">
        <div class="label">Appointments</div>
        <div class="value">{{ number_format($kpis->appointments) }}</div>
        <div class="trend {{ $kpis->apptTrend >= 0 ? 'up' : 'down' }}">
            {{ $kpis->apptTrend >= 0 ? '↑' : '↓' }} {{ abs($kpis->apptTrend) }}% vs prev
        </div>
    </div>
    <div class="kpi kpi--purple">
        <div class="label">Clients</div>
        <div class="value">{{ number_format($kpis->newClients) }}</div>
        <div class="trend {{ $kpis->clientTrend >= 0 ? 'up' : 'down' }}">
            {{ $kpis->clientTrend >= 0 ? '↑' : '↓' }} {{ abs($kpis->clientTrend) }}%
        </div>
    </div>
    <div class="kpi kpi--teal">
        <div class="label">Repeat Rate</div>
        <div class="value">{{ $kpis->repeatRate }}%</div>
        <div class="trend flat">Returning clients</div>
    </div>
    <div class="kpi kpi--amber">
        <div class="label">Avg/Appointment</div>
        <div class="value">₹{{ number_format($kpis->avgRevenuePerAppt) }}</div>
        <div class="trend flat">Revenue per visit</div>
    </div>
    <div class="kpi kpi--red">
        <div class="label">Cancellations</div>
        <div class="value">{{ $kpis->cancelRate }}%</div>
        <div class="trend {{ $kpis->cancelRate > 15 ? 'down' : 'up' }}">
            {{ $kpis->cancellations }} cancelled
        </div>
    </div>
</div>

{{-- INSIGHTS --}}
@if(count($insights))
<div class="insights-grid">
    @foreach($insights as $ins)
    <div class="insight insight-{{ $ins['type'] }}">
        <span class="icon">{{ $ins['icon'] }}</span>
        <span>{{ $ins['text'] }}</span>
    </div>
    @endforeach
</div>
@endif

{{-- CHARTS ROW 1: Revenue + Appointments Trend --}}
<div class="chart-grid">
    <div class="chart-card">
        <h3>Revenue Trend</h3>
        <canvas id="revenueChart" height="200"></canvas>
    </div>
    <div class="chart-card">
        <h3>Appointment Trend</h3>
        <canvas id="appointmentChart" height="200"></canvas>
    </div>
</div>

{{-- CHARTS ROW 2: Revenue Source + Clinic Comparison --}}
<div class="chart-grid">
    <div class="chart-card">
        <h3>Revenue Breakdown</h3>
        <p class="chart-sub">By billing source</p>
        <canvas id="revenueSourceChart" height="220"></canvas>
    </div>
    <div class="chart-card">
        <h3>Clinic Comparison</h3>
        <p class="chart-sub">Revenue by clinic</p>
        <canvas id="clinicCompChart" height="220"></canvas>
    </div>
</div>

{{-- CHARTS ROW 3: Species + Top Diagnoses --}}
<div class="chart-grid">
    <div class="chart-card">
        <h3>Patient Species</h3>
        <canvas id="speciesChart" height="200"></canvas>
    </div>
    <div class="chart-card">
        <h3>Top Diagnoses</h3>
        <canvas id="diagnosisChart" height="200"></canvas>
    </div>
</div>

{{-- VET LEADERBOARD --}}
<div class="chart-card full" style="margin-bottom:20px;">
    <h3>Vet Performance Leaderboard</h3>
    @if(count($vetLeaderboard))
    <table class="lb-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Doctor</th>
                <th>Appointments</th>
                <th>Revenue</th>
                <th>Clients</th>
                <th>Repeat %</th>
                <th>Avg Consult</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vetLeaderboard as $i => $vet)
            <tr>
                <td><span class="rank-badge {{ $i < 3 ? 'rank-'.($i+1) : 'rank-n' }}">{{ $i+1 }}</span></td>
                <td>
                    <strong>{{ str_starts_with($vet->name, 'Dr') ? $vet->name : 'Dr. ' . $vet->name }}</strong>
                    @if($vet->specialization)<br><span style="font-size:11px;color:#9ca3af;">{{ $vet->specialization }}</span>@endif
                </td>
                <td>{{ $vet->appointments }} <span style="color:#9ca3af;font-size:11px;">({{ $vet->completed }} done)</span></td>
                <td class="money">₹{{ number_format($vet->revenue) }}</td>
                <td>{{ $vet->unique_clients }}</td>
                <td>
                    <span class="pct-pill {{ $vet->repeat_pct >= 50 ? 'pct-high' : ($vet->repeat_pct >= 25 ? 'pct-mid' : 'pct-low') }}">
                        {{ $vet->repeat_pct }}%
                    </span>
                </td>
                <td>{{ $vet->avg_consult_min ? $vet->avg_consult_min . ' min' : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="color:#9ca3af;font-size:13px;padding:20px 0;text-align:center;">No vet data for this period.</p>
    @endif
</div>

{{-- CLIENT RETENTION + INVENTORY ALERTS --}}
<div class="chart-grid">
    <div class="chart-card">
        <h3>Client Retention</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:8px;">
            <div style="text-align:center;padding:14px;background:#f0fdf4;border-radius:8px;">
                <div style="font-size:28px;font-weight:700;color:#16a34a;">{{ $clientRetention->returning_clients }}</div>
                <div style="font-size:11px;color:#6b7280;">Returning</div>
            </div>
            <div style="text-align:center;padding:14px;background:#eff6ff;border-radius:8px;">
                <div style="font-size:28px;font-weight:700;color:#2563eb;">{{ $clientRetention->new_clients }}</div>
                <div style="font-size:11px;color:#6b7280;">New Clients</div>
            </div>
        </div>
        <div style="text-align:center;margin-top:12px;padding:12px;background:#f8fafc;border-radius:8px;">
            <div style="font-size:32px;font-weight:700;color:#111827;">{{ $clientRetention->retention_rate }}%</div>
            <div style="font-size:12px;color:#6b7280;">Retention Rate</div>
            @if($clientRetention->growth != 0)
            <div style="font-size:11px;font-weight:600;color:{{ $clientRetention->growth >= 0 ? '#16a34a' : '#ef4444' }};margin-top:4px;">
                {{ $clientRetention->growth >= 0 ? '↑' : '↓' }} {{ abs($clientRetention->growth) }}% client growth
            </div>
            @endif
        </div>
    </div>

    <div class="chart-card">
        <h3>Inventory Alerts</h3>
        @if($inventoryAlerts->out_of_stock_count > 0)
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:10px;margin-bottom:10px;font-size:13px;">
            <span class="stock-out">{{ $inventoryAlerts->out_of_stock_count }} items out of stock</span>
        </div>
        @endif

        @if($inventoryAlerts->low_stock->count())
        <div style="font-size:12px;font-weight:600;color:#6b7280;margin-bottom:6px;">LOW STOCK</div>
        <div class="alert-list">
            @foreach($inventoryAlerts->low_stock as $item)
            <div class="alert-item">
                <span>{{ $item->name }}</span>
                <span class="stock-low">{{ $item->stock }} left</span>
            </div>
            @endforeach
        </div>
        @endif

        @if($inventoryAlerts->expiring_soon->count())
        <div style="font-size:12px;font-weight:600;color:#6b7280;margin:10px 0 6px;">EXPIRING SOON</div>
        <div class="alert-list">
            @foreach($inventoryAlerts->expiring_soon as $item)
            <div class="alert-item">
                <span>{{ $item->name }}</span>
                <span class="expiry-warn">{{ \Carbon\Carbon::parse($item->expiry_date)->format('d M') }}</span>
            </div>
            @endforeach
        </div>
        @endif

        @if($inventoryAlerts->out_of_stock_count == 0 && $inventoryAlerts->low_stock->isEmpty() && $inventoryAlerts->expiring_soon->isEmpty())
        <p style="color:#16a34a;font-size:13px;text-align:center;padding:30px 0;">✓ All inventory levels healthy</p>
        @endif
    </div>
</div>

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
<script>
const colors = {
    blue: '#2563eb', green: '#16a34a', red: '#ef4444', amber: '#f59e0b',
    purple: '#7c3aed', teal: '#0d9488', pink: '#ec4899', indigo: '#4f46e5',
    blueBg: 'rgba(37,99,235,0.1)', greenBg: 'rgba(22,163,74,0.1)',
};

Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
Chart.defaults.font.size = 11;
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.pointStyleWidth = 8;

// ═══ Revenue Trend ═══
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode(array_map(fn($r) => \Carbon\Carbon::parse($r->date)->format('d M'), $revenueTrend)) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode(array_map(fn($r) => (float)$r->amount, $revenueTrend)) !!},
            borderColor: colors.green,
            backgroundColor: colors.greenBg,
            fill: true,
            tension: 0.3,
            pointRadius: 2,
            borderWidth: 2,
        }]
    },
    options: { scales: { y: { beginAtZero: true, ticks: { callback: v => '₹' + v.toLocaleString() } } }, plugins: { legend: { display: false } } }
});

// ═══ Appointment Trend ═══
new Chart(document.getElementById('appointmentChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_map(fn($a) => \Carbon\Carbon::parse($a->date)->format('d M'), $appointmentTrend)) !!},
        datasets: [
            { label: 'Completed', data: {!! json_encode(array_map(fn($a) => $a->completed, $appointmentTrend)) !!}, backgroundColor: colors.green, borderRadius: 3 },
            { label: 'Cancelled', data: {!! json_encode(array_map(fn($a) => $a->cancelled, $appointmentTrend)) !!}, backgroundColor: colors.red, borderRadius: 3 },
            { label: 'Other', data: {!! json_encode(array_map(fn($a) => $a->other, $appointmentTrend)) !!}, backgroundColor: '#d1d5db', borderRadius: 3 },
        ]
    },
    options: { scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }, plugins: { legend: { position: 'bottom' } } }
});

// ═══ Revenue Source Doughnut ═══
new Chart(document.getElementById('revenueSourceChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_map(fn($s) => ucfirst(str_replace('_',' ',$s->source ?? 'Other')), $revenueBySource)) !!},
        datasets: [{
            data: {!! json_encode(array_map(fn($s) => (float)$s->amount, $revenueBySource)) !!},
            backgroundColor: [colors.blue, colors.green, colors.purple, colors.amber, colors.teal, colors.pink],
            borderWidth: 0,
        }]
    },
    options: { cutout: '60%', plugins: { legend: { position: 'bottom' } } }
});

// ═══ Clinic Comparison ═══
new Chart(document.getElementById('clinicCompChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_map(fn($c) => $c->name, $clinicComparison)) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode(array_map(fn($c) => $c->revenue, $clinicComparison)) !!},
            backgroundColor: {!! json_encode(array_map(fn($i) => ['#2563eb','#16a34a','#7c3aed','#f59e0b','#0d9488','#ec4899','#4f46e5'][$i % 7], array_keys($clinicComparison))) !!},
            borderRadius: 4,
        }]
    },
    options: { indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { callback: v => '₹' + v.toLocaleString() } } }, plugins: { legend: { display: false } } }
});

// ═══ Species Pie ═══
new Chart(document.getElementById('speciesChart'), {
    type: 'pie',
    data: {
        labels: {!! json_encode(array_map(fn($s) => ucfirst($s->species ?? 'Unknown'), $speciesBreakdown)) !!},
        datasets: [{
            data: {!! json_encode(array_map(fn($s) => $s->count, $speciesBreakdown)) !!},
            backgroundColor: [colors.blue, colors.amber, colors.purple, colors.teal, colors.pink],
            borderWidth: 0,
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

// ═══ Top Diagnoses ═══
new Chart(document.getElementById('diagnosisChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_map(fn($d) => strlen($d->diagnosis) > 25 ? substr($d->diagnosis,0,25).'...' : $d->diagnosis, $topDiagnoses)) !!},
        datasets: [{
            label: 'Cases',
            data: {!! json_encode(array_map(fn($d) => $d->count, $topDiagnoses)) !!},
            backgroundColor: colors.blue,
            borderRadius: 4,
        }]
    },
    options: { indexAxis: 'y', scales: { x: { beginAtZero: true } }, plugins: { legend: { display: false } } }
});
</script>
@endsection
