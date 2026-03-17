@extends('clinic.layout')

@section('title', 'Lab Orders')

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <h2 style="font-size:20px;font-weight:700;color:#111827;">Lab Orders</h2>
    @if($pendingCount > 0)
        <span style="background:#fef3c7;color:#92400e;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:600;">
            {{ $pendingCount }} Awaiting Routing
        </span>
    @endif
</div>

{{-- Status filters --}}
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('clinic.lab-orders.index') }}"
       style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;{{ !$status ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#374151;border:1px solid #e5e7eb;' }}">All</a>
    @foreach(['ordered' => 'Pending', 'routed' => 'Routed', 'processing' => 'Processing', 'results_uploaded' => 'Results Ready', 'approved' => 'Approved'] as $key => $label)
        <a href="{{ route('clinic.lab-orders.index', ['status' => $key]) }}"
           style="padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;{{ $status === $key ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#374151;border:1px solid #e5e7eb;' }}">{{ $label }}</a>
    @endforeach
</div>

@if($orders->isEmpty())
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:40px;text-align:center;color:#6b7280;">
        No lab orders found.
    </div>
@else
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">Order #</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">Pet</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">Doctor</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">Tests</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">Priority</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">Status</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">Lab</th>
                    <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;">Date</th>
                    <th style="padding:10px 14px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:10px 14px;font-weight:600;color:#2563eb;">{{ $order->order_number }}</td>
                        <td style="padding:10px 14px;">{{ $order->pet->name ?? '—' }}</td>
                        <td style="padding:10px 14px;">{{ $order->vet->name ?? '—' }}</td>
                        <td style="padding:10px 14px;">
                            @foreach($order->tests->take(2) as $test)
                                <span style="display:inline-block;background:#eff6ff;color:#1e40af;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;margin:1px;">{{ $test->test_name }}</span>
                            @endforeach
                            @if($order->tests->count() > 2)
                                <span style="font-size:11px;color:#6b7280;">+{{ $order->tests->count() - 2 }}</span>
                            @endif
                        </td>
                        <td style="padding:10px 14px;">
                            @if($order->priority !== 'routine')
                                <span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">{{ strtoupper($order->priority) }}</span>
                            @else
                                <span style="color:#6b7280;font-size:12px;">Routine</span>
                            @endif
                        </td>
                        <td style="padding:10px 14px;">
                            @php
                                $sc = ['ordered'=>'#fef3c7;color:#92400e','routed'=>'#dbeafe;color:#1d4ed8','processing'=>'#e0e7ff;color:#4338ca','results_uploaded'=>'#d1fae5;color:#065f46','approved'=>'#dcfce7;color:#166534','retest_requested'=>'#fee2e2;color:#991b1b'];
                            @endphp
                            <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{{ explode(';',$sc[$order->status] ?? '#f3f4f6;color:#6b7280')[0] }};{{ $sc[$order->status] ?? '' }}">
                                {{ str_replace('_', ' ', ucfirst($order->status)) }}
                            </span>
                        </td>
                        <td style="padding:10px 14px;font-size:12px;color:#6b7280;">
                            {{ $order->lab->name ?? ($order->routing === 'in_house' ? 'In-house' : '—') }}
                        </td>
                        <td style="padding:10px 14px;font-size:12px;color:#6b7280;">{{ $order->created_at->format('d M') }}</td>
                        <td style="padding:10px 14px;white-space:nowrap;">
                            @if($order->status === 'ordered')
                                <button onclick="document.getElementById('rf-{{ $order->id }}').style.display='table-row'"
                                        style="background:#2563eb;color:#fff;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;border:none;cursor:pointer;">Route</button>
                            @endif
                            @if(in_array($order->status, ['routed', 'processing']))
                                <button onclick="document.getElementById('uf-{{ $order->id }}').style.display='table-row'"
                                        style="background:#16a34a;color:#fff;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;border:none;cursor:pointer;">Upload PDF</button>
                            @endif
                        </td>
                    </tr>

                    {{-- Route form row --}}
                    @if($order->status === 'ordered')
                    <tr id="rf-{{ $order->id }}" style="display:none;background:#f9fafb;">
                        <td colspan="9" style="padding:14px;">
                            <form method="POST" action="{{ route('clinic.lab-orders.route', $order) }}" style="display:flex;align-items:center;gap:12px;">
                                @csrf @method('PUT')
                                <select name="routing" onchange="this.nextElementSibling.style.display=this.value==='external'?'inline-block':'none'" style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;">
                                    <option value="in_house">In-house</option>
                                    <option value="external">External Lab</option>
                                </select>
                                <select name="lab_id" style="display:none;padding:7px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;">
                                    <option value="">Select Lab...</option>
                                    @foreach($labs as $lab)
                                        <option value="{{ $lab->id }}">{{ $lab->name }}{{ $lab->city ? " ({$lab->city})" : '' }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" style="background:#2563eb;color:#fff;padding:7px 16px;border-radius:6px;font-size:12px;font-weight:600;border:none;cursor:pointer;">Assign</button>
                                <button type="button" onclick="this.closest('tr').style.display='none'" style="color:#6b7280;background:none;border:none;cursor:pointer;font-size:12px;">Cancel</button>
                            </form>
                        </td>
                    </tr>
                    @endif

                    {{-- Upload form row --}}
                    @if(in_array($order->status, ['routed', 'processing']))
                    <tr id="uf-{{ $order->id }}" style="display:none;background:#f0fdf4;">
                        <td colspan="9" style="padding:14px;">
                            <form method="POST" action="{{ route('clinic.lab-orders.direct-upload', $order) }}" enctype="multipart/form-data" style="display:flex;align-items:center;gap:12px;">
                                @csrf
                                <span style="font-size:12px;color:#374151;font-weight:600;">Upload lab report PDF:</span>
                                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required style="font-size:12px;">
                                <input type="text" name="notes" placeholder="Notes" style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;width:180px;">
                                <button type="submit" style="background:#16a34a;color:#fff;padding:7px 16px;border-radius:6px;font-size:12px;font-weight:600;border:none;cursor:pointer;">Upload</button>
                                <button type="button" onclick="this.closest('tr').style.display='none'" style="color:#6b7280;background:none;border:none;cursor:pointer;font-size:12px;">Cancel</button>
                            </form>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:16px;">{{ $orders->appends(request()->query())->links() }}</div>
@endif
@endsection
