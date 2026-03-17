@extends('clinic.layout')

@section('title', 'Lab Orders')

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <h1 style="font-size:20px;font-weight:700;">Lab Orders</h1>
    @if($pendingCount > 0)
        <span style="background:#fef3c7;color:#92400e;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;">
            {{ $pendingCount }} pending routing
        </span>
    @endif
</div>

{{-- Status filters --}}
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('clinic.lab-orders.index') }}"
       style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;{{ !$status ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#374151;border:1px solid #e5e7eb;' }}">All</a>
    @foreach(['ordered' => 'Needs Routing', 'routed' => 'Routed', 'processing' => 'Processing', 'results_uploaded' => 'Results Ready', 'approved' => 'Approved'] as $key => $label)
        <a href="{{ route('clinic.lab-orders.index', ['status' => $key]) }}"
           style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;{{ $status === $key ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#374151;border:1px solid #e5e7eb;' }}">{{ $label }}</a>
    @endforeach
</div>

@if($orders->isEmpty())
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:40px;text-align:center;color:#6b7280;">
        No lab orders found.
    </div>
@else
    @foreach($orders as $order)
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:12px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-weight:700;color:#2563eb;font-size:14px;">{{ $order->order_number }}</span>
                    @if($order->priority === 'urgent')
                        <span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:12px;font-size:10px;font-weight:700;">URGENT</span>
                    @endif
                    <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;
                        @if($order->status === 'ordered') background:#fef3c7;color:#92400e;
                        @elseif($order->status === 'routed') background:#dbeafe;color:#1d4ed8;
                        @elseif($order->status === 'processing') background:#e0e7ff;color:#4338ca;
                        @elseif($order->status === 'results_uploaded') background:#d1fae5;color:#065f46;
                        @elseif($order->status === 'approved') background:#dcfce7;color:#166534;
                        @else background:#fee2e2;color:#991b1b;
                        @endif">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
                </div>
                <span style="font-size:12px;color:#6b7280;">{{ $order->created_at->format('d M Y, h:i A') }}</span>
            </div>

            <div style="display:flex;gap:20px;font-size:13px;margin-bottom:10px;">
                <div><span style="color:#6b7280;">Pet:</span> <strong>{{ $order->pet->name ?? '—' }}</strong> ({{ $order->pet->species ?? '' }})</div>
                <div><span style="color:#6b7280;">Vet:</span> {{ $order->vet->name ?? '—' }}</div>
                <div><span style="color:#6b7280;">Lab:</span> {{ $order->lab->name ?? ($order->routing === 'in_house' ? 'In-house' : '—') }}</div>
            </div>

            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px;">
                @foreach($order->tests as $test)
                    <span style="background:#eff6ff;color:#1e40af;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;">{{ $test->test_name }}</span>
                @endforeach
            </div>

            {{-- Routing action for pending orders --}}
            @if($order->status === 'ordered')
                <form method="POST" action="{{ route('clinic.lab-orders.route', $order) }}" style="display:flex;align-items:center;gap:10px;padding-top:10px;border-top:1px solid #f3f4f6;">
                    @csrf
                    @method('PUT')
                    <select name="routing" id="routing-{{ $order->id }}" onchange="toggleLabSelect({{ $order->id }})"
                        style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;background:#fff;">
                        <option value="">— Route to —</option>
                        <option value="in_house">In-House</option>
                        <option value="external">External Lab</option>
                    </select>
                    <select name="lab_id" id="lab-select-{{ $order->id }}" style="display:none;padding:7px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;background:#fff;">
                        <option value="">— Select Lab —</option>
                        @foreach($labs as $lab)
                            <option value="{{ $lab->id }}">{{ $lab->name }} ({{ $lab->type }})</option>
                        @endforeach
                    </select>
                    <button type="submit" style="padding:7px 16px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Route</button>
                </form>
            @endif

            {{-- In-house result upload for routed/processing orders --}}
            @if($order->routing === 'in_house' && in_array($order->status, ['routed', 'processing']))
                <div style="padding-top:10px;border-top:1px solid #f3f4f6;">
                    <div style="font-size:12px;font-weight:600;color:#6b7280;margin-bottom:8px;">Upload In-House Results</div>
                    @foreach($order->tests as $test)
                        @if($test->status !== 'completed')
                            <form method="POST" action="{{ route('clinic.lab-orders.upload-result', [$order, $test]) }}" enctype="multipart/form-data"
                                style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                                @csrf
                                <span style="font-size:12px;font-weight:600;min-width:120px;">{{ $test->test_name }}</span>
                                <input type="file" name="file" required style="font-size:12px;">
                                <input type="text" name="notes" placeholder="Notes" style="padding:5px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;width:150px;">
                                <button type="submit" style="padding:5px 12px;background:#16a34a;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">Upload</button>
                            </form>
                        @else
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-size:12px;">
                                <span style="font-weight:600;min-width:120px;">{{ $test->test_name }}</span>
                                <span style="color:#16a34a;font-weight:600;">Completed</span>
                            </div>
                        @endif
                    @endforeach

                    @if($order->tests->every(fn($t) => $t->status === 'completed'))
                        <form method="POST" action="{{ route('clinic.lab-orders.complete', $order) }}" style="margin-top:8px;">
                            @csrf
                            <button type="submit" style="padding:7px 16px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                                Submit All Results to Vet
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    @endforeach

    <div style="margin-top:16px;">{{ $orders->appends(request()->query())->links() }}</div>
@endif
@endsection

@section('scripts')
<script>
function toggleLabSelect(orderId) {
    const routing = document.getElementById('routing-' + orderId).value;
    const labSelect = document.getElementById('lab-select-' + orderId);
    labSelect.style.display = routing === 'external' ? 'inline-block' : 'none';
}
</script>
@endsection
