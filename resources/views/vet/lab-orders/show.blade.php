@extends('layouts.vet')

@section('content')
<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
    <a href="{{ route('vet.lab-orders.index') }}" class="v-btn v-btn--outline v-btn--sm">&larr; Back</a>
    <h1 class="v-page-title" style="margin:0;">Lab Order {{ $order->order_number }}</h1>
    <span class="v-badge v-badge--{{ str_replace('_', '-', $order->status) }}">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
    @if($order->priority === 'urgent')
        <span style="background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">URGENT</span>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    {{-- Left: Order Info --}}
    <div>
        <div class="v-card">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;color:var(--text-dark);">Order Details</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;">
                <div><span style="color:var(--text-muted);">Pet:</span> <strong>{{ $order->pet->name ?? '—' }}</strong> ({{ $order->pet->species ?? '' }})</div>
                <div><span style="color:var(--text-muted);">Breed:</span> {{ $order->pet->breed ?? '—' }}</div>
                <div><span style="color:var(--text-muted);">Clinic:</span> {{ $order->clinic->name ?? '—' }}</div>
                <div><span style="color:var(--text-muted);">Ordered:</span> {{ $order->created_at->format('d M Y, h:i A') }}</div>
                <div><span style="color:var(--text-muted);">Lab:</span> {{ $order->lab->name ?? ($order->routing === 'in_house' ? 'In-house' : 'Pending routing') }}</div>
                <div><span style="color:var(--text-muted);">Routing:</span> {{ ucfirst($order->routing) }}</div>
            </div>
            @if($order->notes)
                <div style="margin-top:12px;padding:10px;background:var(--bg-soft);border-radius:8px;font-size:13px;">
                    <strong>Notes:</strong> {{ $order->notes }}
                </div>
            @endif
        </div>

        {{-- Tests --}}
        <div class="v-card" style="margin-top:16px;">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;color:var(--text-dark);">Tests Ordered ({{ $order->tests->count() }})</h3>
            @foreach($order->tests as $test)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;{{ !$loop->last ? 'border-bottom:1px solid var(--border-light);' : '' }}">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="background:var(--primary);color:#fff;font-size:10px;font-weight:700;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;">{{ $loop->iteration }}</span>
                        <span style="font-weight:600;font-size:13px;">{{ $test->test_name }}</span>
                    </div>
                    <span class="v-badge v-badge--{{ $test->status === 'completed' ? 'approved' : ($test->status === 'processing' ? 'processing' : 'ordered') }}">
                        {{ ucfirst($test->status) }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Right: Results & Actions --}}
    <div>
        @if($order->results->isNotEmpty())
            <div class="v-card">
                <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;color:var(--text-dark);">Results</h3>
                @foreach($order->results as $result)
                    <div style="padding:10px;background:var(--bg-soft);border-radius:8px;margin-bottom:8px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <span style="font-weight:600;font-size:13px;">
                                {{ $result->test ? $result->test->test_name : 'General Result' }}
                            </span>
                            <a href="{{ route('vet.lab-orders.result.download', $result) }}" class="v-btn v-btn--outline v-btn--sm" style="font-size:11px;">Download</a>
                        </div>
                        @if($result->original_filename)
                            <div style="font-size:12px;color:var(--text-muted);">{{ $result->original_filename }}</div>
                        @endif
                        @if($result->summary)
                            <div style="font-size:13px;margin-top:6px;">{{ $result->summary }}</div>
                        @endif
                        @if($result->vet_approved)
                            <div style="margin-top:6px;font-size:11px;color:#16a34a;font-weight:600;">Approved {{ $result->vet_approved_at?->format('d M Y') }}</div>
                        @endif
                        @if($result->retest_requested)
                            <div style="margin-top:6px;font-size:11px;color:#dc2626;font-weight:600;">Retest requested: {{ $result->retest_reason }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Approve / Retest actions --}}
        @if(in_array($order->status, ['results_uploaded', 'vet_review']))
            <div class="v-card" style="margin-top:16px;">
                <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;color:var(--text-dark);">Review Actions</h3>

                {{-- Approve --}}
                <form method="POST" action="{{ route('vet.lab-orders.approve', $order) }}" style="margin-bottom:16px;">
                    @csrf
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:6px;">Vet Notes (optional)</label>
                    <textarea name="vet_notes" class="v-input" rows="2" placeholder="Any notes on these results..." style="margin-bottom:8px;"></textarea>
                    <button type="submit" class="v-btn v-btn--sm" style="background:#16a34a;color:#fff;">Approve Results</button>
                </form>

                {{-- Retest --}}
                <form method="POST" action="{{ route('vet.lab-orders.retest', $order) }}">
                    @csrf
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:6px;">Retest Reason (required)</label>
                    <textarea name="retest_reason" class="v-input" rows="2" placeholder="Why is a retest needed..." style="margin-bottom:8px;"></textarea>
                    <button type="submit" class="v-btn v-btn--sm" style="background:#dc2626;color:#fff;">Request Retest</button>
                </form>
            </div>
        @endif

        @if($order->status === 'approved')
            <div class="v-card" style="margin-top:16px;background:#f0fdf4;border-color:#bbf7d0;">
                <div style="font-size:14px;font-weight:700;color:#166534;margin-bottom:4px;">Results Approved</div>
                <div style="font-size:13px;color:#166534;">Lab results are now visible on the pet profile.</div>
                @if($order->results->first()?->vet_notes)
                    <div style="margin-top:8px;font-size:13px;color:#374151;">
                        <strong>Notes:</strong> {{ $order->results->first()->vet_notes }}
                    </div>
                @endif
            </div>
        @endif

        @if($order->status === 'ordered')
            <div class="v-card" style="margin-top:16px;background:#fef3c7;border-color:#fde68a;">
                <div style="font-size:13px;color:#92400e;">
                    Awaiting routing by clinic staff. The order will be assigned to a lab for processing.
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('head')
<style>
    .v-badge { display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;text-transform:capitalize; }
    .v-badge--ordered { background:#fef3c7;color:#92400e; }
    .v-badge--routed { background:#dbeafe;color:#1d4ed8; }
    .v-badge--processing { background:#e0e7ff;color:#4338ca; }
    .v-badge--results-uploaded { background:#d1fae5;color:#065f46; }
    .v-badge--vet-review { background:#fef3c7;color:#92400e; }
    .v-badge--approved { background:#dcfce7;color:#166534; }
    .v-badge--retest-requested { background:#fee2e2;color:#991b1b; }
</style>
@endsection
