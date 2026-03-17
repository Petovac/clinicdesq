@extends('layouts.lab')

@section('content')
<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
    <a href="{{ route('lab.orders.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
    <h1 class="page-title" style="margin:0;">Order {{ $order->order_number }}</h1>
    <span class="status-badge status-{{ $order->status }}">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
    @if($order->priority !== 'routine')
        <span style="background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">{{ strtoupper($order->priority) }}</span>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <div>
        <div class="card">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;">Order Details</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;">
                <div><span style="color:var(--text-muted);">Pet:</span> <strong>{{ $order->pet->name ?? '—' }}</strong> ({{ $order->pet->species ?? '' }})</div>
                <div><span style="color:var(--text-muted);">Breed:</span> {{ $order->pet->breed ?? '—' }}</div>
                <div><span style="color:var(--text-muted);">Clinic:</span> {{ $order->clinic->name ?? '—' }}</div>
                <div><span style="color:var(--text-muted);">Doctor:</span> {{ $order->vet->name ?? '—' }}</div>
                <div><span style="color:var(--text-muted);">Ordered:</span> {{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>
            @if($order->notes)
                <div style="margin-top:12px;padding:10px;background:#f9fafb;border-radius:8px;font-size:13px;">
                    <strong>Vet Notes:</strong> {{ $order->notes }}
                </div>
            @endif
        </div>

        <div class="card" style="margin-top:16px;">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;">Tests ({{ $order->tests->count() }})</h3>
            @foreach($order->tests as $test)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px;background:#f9fafb;border-radius:8px;margin-bottom:8px;">
                    <div>
                        <span style="font-weight:600;font-size:13px;">{{ $test->test_name }}</span>
                        @if($test->parameters)
                            <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">{{ is_array($test->parameters) ? implode(', ', $test->parameters) : $test->parameters }}</div>
                        @endif
                    </div>
                    <span class="status-badge status-{{ $test->status === 'completed' ? 'approved' : ($test->status === 'processing' ? 'processing' : 'ordered') }}">{{ ucfirst($test->status) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div>
        @if($order->status === 'retest_requested')
            <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:16px;margin-bottom:16px;">
                <div style="font-weight:700;color:#991b1b;font-size:13px;margin-bottom:4px;">Retest Requested</div>
                <div style="font-size:13px;color:#991b1b;">{{ $order->results->first()?->retest_reason ?? 'Vet requested a retest.' }}</div>
            </div>
        @endif

        @if(in_array($order->status, ['routed', 'retest_requested']))
            <form method="POST" action="{{ route('lab.orders.start', $order) }}" style="margin-bottom:16px;">
                @csrf
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    {{ $order->status === 'retest_requested' ? 'Start Retest' : 'Start Processing' }}
                </button>
            </form>
        @endif

        @if($order->status === 'processing')
            <div class="card">
                <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;">Upload Results</h3>
                @foreach($order->tests as $test)
                    <div style="padding:12px;background:#f9fafb;border-radius:8px;margin-bottom:10px;">
                        <div style="font-weight:600;font-size:13px;margin-bottom:8px;">{{ $test->test_name }}</div>
                        @foreach($test->results as $r)
                            <div style="padding:6px 10px;background:#fff;border:1px solid var(--border);border-radius:6px;margin-bottom:6px;font-size:12px;">
                                {{ $r->original_filename }} @if($r->summary)<span style="color:var(--text-muted);">— {{ $r->summary }}</span>@endif
                            </div>
                        @endforeach
                        @if($test->status !== 'completed')
                            <form method="POST" action="{{ route('lab.orders.upload-result', [$order, $test]) }}" enctype="multipart/form-data" style="margin-top:8px;">
                                @csrf
                                <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png" style="font-size:12px;margin-bottom:6px;display:block;">
                                <textarea name="notes" placeholder="Notes..." rows="2" style="width:100%;padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:12px;resize:vertical;margin-bottom:6px;font-family:inherit;"></textarea>
                                <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
            <form method="POST" action="{{ route('lab.orders.complete', $order) }}" style="margin-top:12px;">
                @csrf
                <button type="submit" class="btn btn-primary" style="width:100%;">Mark All Complete</button>
            </form>
        @endif

        @if($order->status === 'results_uploaded')
            <div class="card" style="background:#d1fae5;border-color:#a7f3d0;">
                <div style="font-size:13px;font-weight:600;color:#065f46;">Results submitted. Awaiting vet review.</div>
            </div>
        @endif

        @if($order->status === 'approved')
            <div class="card" style="background:#dcfce7;border-color:#bbf7d0;">
                <div style="font-size:13px;font-weight:600;color:#166534;">Results approved by vet.</div>
            </div>
        @endif
    </div>
</div>
@endsection
