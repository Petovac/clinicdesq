@extends('clinic.layout')

@section('content')
<style>
.dash-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px; }
.dash-header h2 { font-size:22px;font-weight:700;color:#111827;margin:0; }
.controls { display:flex;gap:10px;flex-wrap:wrap; }
.period-bar { display:flex;gap:4px;background:#f3f4f6;padding:3px;border-radius:8px; }
.period-btn { padding:6px 12px;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;background:transparent;color:#6b7280;text-decoration:none; }
.period-btn:hover { color:#111827; }
.period-btn.active { background:#fff;color:#111827;box-shadow:0 1px 3px rgba(0,0,0,0.1); }
.clinic-filter { padding:6px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;background:#fff; }

.kpi-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px; }
.kpi { background:#fff;padding:14px 16px;border-radius:10px;border:1px solid #f0f0f0; }
.kpi .label { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-bottom:3px; }
.kpi .value { font-size:22px;font-weight:700;color:#111827; }
.kpi .trend { font-size:11px;font-weight:600;margin-top:3px; }
.kpi .trend.up { color:#16a34a; }
.kpi .trend.down { color:#ef4444; }
.kpi .benchmark { font-size:10px;color:#6b7280;margin-top:2px; }
.kpi--blue { border-left:3px solid #2563eb; }
.kpi--green { border-left:3px solid #16a34a; }
.kpi--purple { border-left:3px solid #7c3aed; }
.kpi--amber { border-left:3px solid #f59e0b; }
.kpi--teal { border-left:3px solid #0d9488; }
.kpi--red { border-left:3px solid #ef4444; }

.chart-grid { display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px; }
.chart-card { background:#fff;border-radius:10px;border:1px solid #f0f0f0;padding:16px; }
.chart-card.full { grid-column:1/-1; }
.chart-card h3 { font-size:14px;font-weight:700;color:#111827;margin:0 0 10px; }

.lb-table { width:100%;border-collapse:collapse;font-size:13px; }
.lb-table th { text-align:left;padding:8px 10px;font-weight:600;color:#6b7280;font-size:10px;text-transform:uppercase;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.lb-table td { padding:8px 10px;border-bottom:1px solid #f3f4f6; }
.lb-table tr:hover td { background:#f9fafb; }
.rank-badge { display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:50%;font-size:10px;font-weight:700; }
.rank-1 { background:#fef3c7;color:#92400e; } .rank-2 { background:#e5e7eb;color:#374151; } .rank-3 { background:#fed7aa;color:#9a3412; } .rank-n { background:#f3f4f6;color:#9ca3af; }
.money { font-weight:600;color:#16a34a; }
.pct-pill { display:inline-block;padding:2px 7px;border-radius:10px;font-size:10px;font-weight:600; }
.pct-high { background:#dcfce7;color:#166534; } .pct-mid { background:#fef3c7;color:#92400e; } .pct-low { background:#fee2e2;color:#991b1b; }

.insights-grid { display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:18px; }
.insight { padding:10px 12px;border-radius:8px;font-size:12px;display:flex;gap:8px;align-items:flex-start; }
.insight .icon { font-size:16px;flex-shrink:0; }
.insight-success { background:#f0fdf4;border:1px solid #bbf7d0;color:#166534; }
.insight-danger { background:#fef2f2;border:1px solid #fecaca;color:#991b1b; }
.insight-warning { background:#fffbeb;border:1px solid #fde68a;color:#92400e; }
.insight-info { background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af; }

.alert-list { font-size:12px; }
.alert-item { padding:5px 0;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between; }
.alert-item:last-child { border-bottom:none; }
.stock-low { color:#f59e0b;font-weight:600; }
.stock-out { color:#ef4444;font-weight:600; }

@media (max-width:768px) { .chart-grid,.insights-grid { grid-template-columns:1fr; } .kpi-grid { grid-template-columns:repeat(2,1fr); } }
</style>

<div class="dash-header">
    <h2>Clinic Analytics</h2>
    <div class="controls">
        @if($clinics->count() > 1)
        <select class="clinic-filter" onchange="location.href='?period={{ $days }}&clinic='+this.value">
            <option value="">All My Clinics ({{ $clinics->count() }})</option>
            @foreach($clinics as $c)
            <option value="{{ $c->id }}" {{ $filterClinicId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        @endif
        <div class="period-bar">
            <a href="?period=7{{ $filterClinicId ? '&clinic='.$filterClinicId : '' }}" class="period-btn {{ $days==7 ? 'active' : '' }}">7D</a>
            <a href="?period=30{{ $filterClinicId ? '&clinic='.$filterClinicId : '' }}" class="period-btn {{ $days==30 ? 'active' : '' }}">30D</a>
            <a href="?period=90{{ $filterClinicId ? '&clinic='.$filterClinicId : '' }}" class="period-btn {{ $days==90 ? 'active' : '' }}">90D</a>
            <a href="?period=365{{ $filterClinicId ? '&clinic='.$filterClinicId : '' }}" class="period-btn {{ $days==365 ? 'active' : '' }}">12M</a>
        </div>
    </div>
</div>

{{-- KPIs with org benchmark --}}
<div class="kpi-grid">
    <div class="kpi kpi--green">
        <div class="label">Revenue</div>
        <div class="value">₹{{ number_format($kpis->revenue) }}</div>
        <div class="trend {{ $kpis->revTrend >= 0 ? 'up' : 'down' }}">{{ $kpis->revTrend >= 0 ? '↑' : '↓' }} {{ abs($kpis->revTrend) }}%</div>
        @if($orgKpis)<div class="benchmark">Org avg: ₹{{ number_format($orgKpis->revenue / max(1, $clinics->count())) }}</div>@endif
    </div>
    <div class="kpi kpi--blue">
        <div class="label">Appointments</div>
        <div class="value">{{ $kpis->appointments }}</div>
        <div class="trend {{ $kpis->apptTrend >= 0 ? 'up' : 'down' }}">{{ $kpis->apptTrend >= 0 ? '↑' : '↓' }} {{ abs($kpis->apptTrend) }}%</div>
    </div>
    <div class="kpi kpi--purple">
        <div class="label">Clients</div>
        <div class="value">{{ $kpis->newClients }}</div>
        <div class="trend {{ $kpis->clientTrend >= 0 ? 'up' : 'down' }}">{{ $kpis->clientTrend >= 0 ? '↑' : '↓' }} {{ abs($kpis->clientTrend) }}%</div>
    </div>
    <div class="kpi kpi--teal">
        <div class="label">Repeat Rate</div>
        <div class="value">{{ $kpis->repeatRate }}%</div>
    </div>
    <div class="kpi kpi--amber">
        <div class="label">Avg/Visit</div>
        <div class="value">₹{{ number_format($kpis->avgRevenuePerAppt) }}</div>
    </div>
    <div class="kpi kpi--red">
        <div class="label">Cancel Rate</div>
        <div class="value">{{ $kpis->cancelRate }}%</div>
    </div>
</div>

@if(count($insights))
<div class="insights-grid">
    @foreach($insights as $ins)
    <div class="insight insight-{{ $ins['type'] }}"><span class="icon">{{ $ins['icon'] }}</span><span>{{ $ins['text'] }}</span></div>
    @endforeach
</div>
@endif

<div class="chart-grid">
    <div class="chart-card"><h3>Revenue Trend</h3><canvas id="revenueChart" height="200"></canvas></div>
    <div class="chart-card"><h3>Appointments</h3><canvas id="appointmentChart" height="200"></canvas></div>
</div>

<div class="chart-grid">
    <div class="chart-card"><h3>Revenue Breakdown</h3><canvas id="sourceChart" height="200"></canvas></div>
    <div class="chart-card"><h3>Patient Species</h3><canvas id="speciesChart" height="200"></canvas></div>
</div>

@if(count($clinicComparison) > 1)
<div class="chart-card full" style="margin-bottom:18px;">
    <h3>My Clinics Comparison</h3>
    <canvas id="compChart" height="160"></canvas>
</div>
@endif

{{-- Vet Leaderboard --}}
@if(count($vetLeaderboard))
<div class="chart-card full" style="margin-bottom:18px;">
    <h3>Doctor Performance</h3>
    <table class="lb-table">
        <thead><tr><th>#</th><th>Doctor</th><th>Appts</th><th>Revenue</th><th>Clients</th><th>Repeat%</th><th>Avg Time</th></tr></thead>
        <tbody>
        @foreach($vetLeaderboard as $i => $vet)
        <tr>
            <td><span class="rank-badge {{ $i < 3 ? 'rank-'.($i+1) : 'rank-n' }}">{{ $i+1 }}</span></td>
            <td><strong>{{ str_starts_with($vet->name, 'Dr') ? $vet->name : 'Dr. ' . $vet->name }}</strong></td>
            <td>{{ $vet->appointments }}</td>
            <td class="money">₹{{ number_format($vet->revenue) }}</td>
            <td>{{ $vet->unique_clients }}</td>
            <td><span class="pct-pill {{ $vet->repeat_pct >= 50 ? 'pct-high' : ($vet->repeat_pct >= 25 ? 'pct-mid' : 'pct-low') }}">{{ $vet->repeat_pct }}%</span></td>
            <td>{{ $vet->avg_consult_min ? $vet->avg_consult_min.'m' : '—' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Retention + Inventory --}}
<div class="chart-grid">
    <div class="chart-card">
        <h3>Client Retention</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:6px;">
            <div style="text-align:center;padding:12px;background:#f0fdf4;border-radius:8px;">
                <div style="font-size:24px;font-weight:700;color:#16a34a;">{{ $clientRetention->returning_clients }}</div>
                <div style="font-size:10px;color:#6b7280;">Returning</div>
            </div>
            <div style="text-align:center;padding:12px;background:#eff6ff;border-radius:8px;">
                <div style="font-size:24px;font-weight:700;color:#2563eb;">{{ $clientRetention->new_clients }}</div>
                <div style="font-size:10px;color:#6b7280;">New</div>
            </div>
        </div>
        <div style="text-align:center;margin-top:10px;padding:10px;background:#f8fafc;border-radius:8px;">
            <div style="font-size:28px;font-weight:700;">{{ $clientRetention->retention_rate }}%</div>
            <div style="font-size:11px;color:#6b7280;">Retention Rate</div>
        </div>
    </div>
    <div class="chart-card">
        <h3>Inventory Alerts</h3>
        @if($inventoryAlerts->out_of_stock_count > 0)
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:8px;margin-bottom:8px;font-size:12px;">
            <span class="stock-out">{{ $inventoryAlerts->out_of_stock_count }} out of stock</span>
        </div>
        @endif
        @if($inventoryAlerts->low_stock->count())
        <div class="alert-list">
            @foreach($inventoryAlerts->low_stock as $item)
            <div class="alert-item"><span>{{ $item->name }}</span><span class="stock-low">{{ $item->stock }} left</span></div>
            @endforeach
        </div>
        @elseif($inventoryAlerts->out_of_stock_count == 0)
        <p style="color:#16a34a;font-size:12px;text-align:center;padding:20px 0;">✓ Inventory healthy</p>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
<script>
const C = { blue:'#2563eb', green:'#16a34a', red:'#ef4444', amber:'#f59e0b', purple:'#7c3aed', teal:'#0d9488', pink:'#ec4899' };
Chart.defaults.font.family = 'Inter,system-ui,sans-serif';
Chart.defaults.font.size = 11;

new Chart(document.getElementById('revenueChart'), {
    type:'line',
    data:{ labels:{!! json_encode(array_map(fn($r)=>\Carbon\Carbon::parse($r->date)->format('d M'),$revenueTrend)) !!},
        datasets:[{data:{!! json_encode(array_map(fn($r)=>(float)$r->amount,$revenueTrend)) !!},borderColor:C.green,backgroundColor:'rgba(22,163,74,0.1)',fill:true,tension:.3,pointRadius:2,borderWidth:2}]},
    options:{scales:{y:{beginAtZero:true,ticks:{callback:v=>'₹'+v.toLocaleString()}}},plugins:{legend:{display:false}}}
});

new Chart(document.getElementById('appointmentChart'), {
    type:'bar',
    data:{ labels:{!! json_encode(array_map(fn($a)=>\Carbon\Carbon::parse($a->date)->format('d M'),$appointmentTrend)) !!},
        datasets:[
            {label:'Done',data:{!! json_encode(array_map(fn($a)=>$a->completed,$appointmentTrend)) !!},backgroundColor:C.green,borderRadius:3},
            {label:'Cancelled',data:{!! json_encode(array_map(fn($a)=>$a->cancelled,$appointmentTrend)) !!},backgroundColor:C.red,borderRadius:3},
            {label:'Other',data:{!! json_encode(array_map(fn($a)=>$a->other,$appointmentTrend)) !!},backgroundColor:'#d1d5db',borderRadius:3},
        ]},
    options:{scales:{x:{stacked:true},y:{stacked:true,beginAtZero:true}},plugins:{legend:{position:'bottom',labels:{usePointStyle:true,pointStyleWidth:8}}}}
});

new Chart(document.getElementById('sourceChart'), {
    type:'doughnut',
    data:{ labels:{!! json_encode(array_map(fn($s)=>ucfirst(str_replace('_',' ',$s->source??'Other')),$revenueBySource)) !!},
        datasets:[{data:{!! json_encode(array_map(fn($s)=>(float)$s->amount,$revenueBySource)) !!},backgroundColor:[C.blue,C.green,C.purple,C.amber,C.teal,C.pink],borderWidth:0}]},
    options:{cutout:'60%',plugins:{legend:{position:'bottom',labels:{usePointStyle:true,pointStyleWidth:8}}}}
});

new Chart(document.getElementById('speciesChart'), {
    type:'pie',
    data:{ labels:{!! json_encode(array_map(fn($s)=>ucfirst($s->species??'Unknown'),$speciesBreakdown)) !!},
        datasets:[{data:{!! json_encode(array_map(fn($s)=>$s->count,$speciesBreakdown)) !!},backgroundColor:[C.blue,C.amber,C.purple,C.teal],borderWidth:0}]},
    options:{plugins:{legend:{position:'bottom',labels:{usePointStyle:true,pointStyleWidth:8}}}}
});

@if(count($clinicComparison) > 1)
new Chart(document.getElementById('compChart'), {
    type:'bar',
    data:{ labels:{!! json_encode(array_map(fn($c)=>$c->name,$clinicComparison)) !!},
        datasets:[{label:'Revenue',data:{!! json_encode(array_map(fn($c)=>$c->revenue,$clinicComparison)) !!},backgroundColor:[C.blue,C.green,C.purple,C.amber,C.teal],borderRadius:4}]},
    options:{indexAxis:'y',scales:{x:{beginAtZero:true,ticks:{callback:v=>'₹'+v.toLocaleString()}}},plugins:{legend:{display:false}}}
});
@endif
</script>
@endsection
