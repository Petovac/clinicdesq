@extends('layouts.lab')

@section('content')
<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
    <a href="{{ route('lab.orders.index') }}" class="btn btn-outline btn-sm">&larr; Back</a>
    <h1 class="page-title" style="margin:0;">Order {{ $order->order_number }}</h1>
    <span class="status-badge status-{{ $order->status }}">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
    @if($order->priority === 'urgent')
        <span style="background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">URGENT</span>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    {{-- Left: Order info & tests --}}
    <div>
        <div class="card">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;">Order Details</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;">
                <div><span style="color:var(--text-muted);">Pet:</span> <strong>{{ $order->pet->name ?? '—' }}</strong> ({{ $order->pet->species ?? '' }})</div>
                <div><span style="color:var(--text-muted);">Breed:</span> {{ $order->pet->breed ?? '—' }}</div>
                <div><span style="color:var(--text-muted);">Clinic:</span> {{ $order->clinic->name ?? '—' }}</div>
                <div><span style="color:var(--text-muted);">Vet:</span> {{ $order->vet->name ?? '—' }}</div>
                <div><span style="color:var(--text-muted);">Ordered:</span> {{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>
            @if($order->notes)
                <div style="margin-top:12px;padding:10px;background:#f9fafb;border-radius:8px;font-size:13px;">
                    <strong>Vet Notes:</strong> {{ $order->notes }}
                </div>
            @endif
        </div>

        {{-- Start Processing button --}}
        @if(in_array($order->status, ['routed', 'retest_requested']))
            <form method="POST" action="{{ route('lab.orders.start', $order) }}" style="margin-top:12px;">
                @csrf
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    {{ $order->status === 'retest_requested' ? 'Start Retest Processing' : 'Start Processing' }}
                </button>
            </form>
        @endif

        @if($order->status === 'retest_requested')
            <div class="card" style="margin-top:12px;background:#fee2e2;border-color:#fecaca;">
                <div style="font-size:13px;font-weight:600;color:#991b1b;margin-bottom:4px;">Retest Requested</div>
                <div style="font-size:13px;color:#991b1b;">
                    {{ $order->results->first()?->retest_reason ?? 'Vet requested a retest.' }}
                </div>
            </div>
        @endif
    </div>

    {{-- Right: Tests & result upload --}}
    <div>
        <div class="card">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;">Tests ({{ $order->tests->count() }})</h3>

            @foreach($order->tests as $test)
                <div style="padding:12px;background:#f9fafb;border-radius:8px;margin-bottom:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                        <span style="font-weight:600;font-size:14px;">{{ $test->test_name }}</span>
                        <span class="status-badge status-{{ $test->status === 'completed' ? 'approved' : ($test->status === 'processing' ? 'processing' : 'ordered') }}">
                            {{ ucfirst($test->status) }}
                        </span>
                    </div>

                    {{-- Show existing results --}}
                    @foreach($test->results as $result)
                        <div style="padding:8px;background:#fff;border:1px solid var(--border);border-radius:6px;margin-bottom:6px;font-size:12px;">
                            <div style="font-weight:600;">{{ $result->original_filename }}</div>
                            @if($result->summary)
                                <div style="color:var(--text-muted);margin-top:2px;">{{ $result->summary }}</div>
                            @endif
                            @if($result->vet_approved)
                                <div style="color:#16a34a;font-weight:600;margin-top:4px;">Vet Approved</div>
                            @endif
                        </div>
                    @endforeach

                    {{-- Upload form (only when processing) --}}
                    @if($order->status === 'processing' && $test->status !== 'completed')
                        <form method="POST" action="{{ route('lab.orders.upload-result', [$order, $test]) }}" enctype="multipart/form-data"
                            style="margin-top:8px;padding-top:8px;border-top:1px solid var(--border);">
                            @csrf
                            <div style="margin-bottom:6px;">
                                <input type="file" name="file" required style="font-size:12px;">
                            </div>
                            <div style="display:flex;gap:8px;">
                                <input type="text" name="notes" placeholder="Result notes..." style="flex:1;padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:12px;">
                                <button type="submit" class="btn btn-primary btn-sm">Upload Result</button>
                            </div>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Mark complete button --}}
        @if($order->status === 'processing' && $order->tests->every(fn($t) => $t->status === 'completed'))
            <form method="POST" action="{{ route('lab.orders.complete', $order) }}" style="margin-top:12px;">
                @csrf
                <button type="submit" class="btn btn-primary" style="width:100%;">Submit All Results to Vet</button>
            </form>
        @endif

        @if($order->status === 'results_uploaded')
            <div class="card" style="margin-top:12px;background:#d1fae5;border-color:#a7f3d0;">
                <div style="font-size:13px;font-weight:600;color:#065f46;">Results submitted. Awaiting vet review.</div>
            </div>
        @endif

        @if($order->status === 'approved')
            <div class="card" style="margin-top:12px;background:#dcfce7;border-color:#bbf7d0;">
                <div style="font-size:13px;font-weight:600;color:#166534;">Results approved by vet.</div>
            </div>
        @endif
    </div>
</div>
@endsection
