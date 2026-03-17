@extends('layouts.lab')

@section('content')
<h1 class="page-title">Dashboard</h1>

<div style="display:flex;gap:16px;margin-bottom:24px;">
    @foreach(['pending' => ['Pending', '#f59e0b'], 'processing' => ['Processing', '#2563eb'], 'uploaded' => ['Submitted', '#065f46'], 'completed' => ['Completed', '#166534'], 'retest' => ['Retest', '#dc2626']] as $key => [$label, $color])
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px;flex:1;text-align:center;">
        <div style="font-size:28px;font-weight:700;color:{{ $color }};">{{ $counts[$key] }}</div>
        <div style="font-size:12px;color:var(--text-muted);font-weight:600;">{{ $label }}</div>
    </div>
    @endforeach
</div>

<h2 style="font-size:16px;font-weight:700;margin-bottom:14px;">Active Orders</h2>
@if($recentOrders->isEmpty())
    <div class="card" style="text-align:center;padding:40px;color:var(--text-muted);">No active orders.</div>
@else
    <div class="card" style="padding:0;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f9fafb;border-bottom:1px solid var(--border);">
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Order #</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Pet</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Clinic</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Tests</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Status</th>
                    <th style="padding:10px 14px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:10px 14px;font-weight:600;color:var(--primary);">{{ $order->order_number }}</td>
                    <td style="padding:10px 14px;">{{ $order->pet->name ?? '—' }}</td>
                    <td style="padding:10px 14px;">{{ $order->clinic->name ?? '—' }}</td>
                    <td style="padding:10px 14px;">
                        @foreach($order->tests as $t)
                            <span style="background:#eff6ff;color:#1e40af;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;margin:1px;">{{ $t->test_name }}</span>
                        @endforeach
                    </td>
                    <td style="padding:10px 14px;"><span class="status-badge status-{{ $order->status }}">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span></td>
                    <td style="padding:10px 14px;"><a href="{{ route('lab.orders.show', $order) }}" class="btn btn-outline btn-sm">View</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@if(isset($allTests))
<h2 style="font-size:16px;font-weight:700;margin-top:28px;margin-bottom:14px;">Test Availability</h2>
<div class="card">
    @foreach($allTests as $test)
        @php $isAvail = (bool)($availability[$test->id] ?? false); @endphp
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;{{ !$loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }}">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-weight:600;font-size:13px;">{{ $test->name }}</span>
                <span style="background:#eff6ff;color:#1e40af;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600;">{{ $test->category }}</span>
                <span style="background:{{ $isAvail ? '#dcfce7' : '#fee2e2' }};color:{{ $isAvail ? '#166534' : '#991b1b' }};padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600;">
                    {{ $isAvail ? 'Available' : 'Unavailable' }}
                </span>
            </div>
            <form method="POST" action="{{ route('lab.toggle-availability') }}" style="display:flex;align-items:center;gap:6px;">
                @csrf
                <input type="hidden" name="test_id" value="{{ $test->id }}">
                <input type="hidden" name="is_available" value="{{ $isAvail ? '0' : '1' }}">
                @if(!$isAvail)
                    <input type="text" name="reason" placeholder="Reason..." style="padding:5px 8px;border:1px solid var(--border);border-radius:6px;font-size:12px;width:160px;">
                @endif
                <button type="submit" class="btn btn-sm {{ $isAvail ? 'btn-outline' : 'btn-primary' }}">{{ $isAvail ? 'Mark Unavailable' : 'Mark Available' }}</button>
            </form>
        </div>
    @endforeach
</div>
@endif
@endsection
