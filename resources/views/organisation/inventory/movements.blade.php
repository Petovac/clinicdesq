@extends('organisation.layout')

@section('content')
<style>
/* ── Page ── */
.mov-header { margin-bottom: 24px; }
.mov-header h2 { font-size: 22px; font-weight: 700; margin: 0 0 4px; }
.mov-header p { color: #6b7280; font-size: 14px; margin: 0; }

/* ── Selector bar ── */
.mov-filters { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding: 16px 20px; background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.06); flex-wrap: wrap; }
.mov-filters label { font-size: 13px; font-weight: 600; color: #374151; white-space: nowrap; }
.mov-filters select { padding: 9px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff; cursor: pointer; min-width: 200px; }
.mov-filters select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.mov-filters .btn-clear { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; background: #f3f4f6; color: #374151; text-decoration: none; border: 1px solid #e5e7eb; transition: all .15s; }
.mov-filters .btn-clear:hover { background: #e5e7eb; }

/* ── Prompt card ── */
.mov-prompt { text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.mov-prompt .icon { font-size: 48px; margin-bottom: 12px; opacity: .3; }
.mov-prompt h3 { font-size: 17px; font-weight: 600; color: #374151; margin: 0 0 6px; }
.mov-prompt p { font-size: 14px; color: #9ca3af; margin: 0; }

/* ── Table ── */
.mov-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.mov-table th { background: #f8fafc; padding: 12px 16px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .04em; text-align: left; border-bottom: 2px solid #e5e7eb; }
.mov-table td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #f3f4f6; color: #334155; }
.mov-table tr:hover td { background: #f9fafb; }
.mov-table tr:last-child td { border-bottom: none; }

/* ── Badges ── */
.badge-m { padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
.bg-purchase { background: #d1fae5; color: #065f46; }
.bg-adjustment { background: #fef3c7; color: #92400e; }
.bg-usage { background: #fee2e2; color: #991b1b; }
.bg-transfer { background: #dbeafe; color: #1e40af; }
.bg-expired { background: #fce7f3; color: #9d174d; }
.clinic-badge { padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #f3e8ff; color: #6b21a8; }
.org-badge { padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #fef3c7; color: #92400e; }

/* ── Pagination ── */
.mov-pagination { margin-top: 16px; display: flex; gap: 6px; flex-wrap: wrap; }
.mov-pagination a, .mov-pagination span { padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; border: 1px solid #e5e7eb; }
.mov-pagination span.current { background: #2563eb; color: #fff; border-color: #2563eb; }

/* ── Empty ── */
.mov-empty { text-align: center; padding: 40px 20px; color: #9ca3af; font-size: 14px; }
</style>

{{-- Header --}}
<div class="mov-header">
    <h2>Inventory Log</h2>
    <p>View inventory movement history for central stock or individual clinics.</p>
</div>

{{-- Filters --}}
<div class="mov-filters">
    <form method="GET" action="{{ route('organisation.inventory.movements') }}" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; width:100%;">
        <label>View logs for:</label>
        <select name="clinic_id" onchange="this.form.submit()">
            <option value="">-- Select Location --</option>
            <option value="0" {{ $clinicId === '0' ? 'selected' : '' }}>Central Stock (Organisation)</option>
            @foreach($clinics as $c)
                <option value="{{ $c->id }}" {{ $clinicId == $c->id && $clinicId !== '' ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>

        @if($clinicId !== null && $clinicId !== '')
            <select name="type" onchange="this.form.submit()">
                <option value="">All Types</option>
                @foreach(['purchase','transfer_in','transfer_out','treatment_usage','manual_adjustment','expired'] as $t)
                    <option value="{{ $t }}" {{ $type === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                @endforeach
            </select>
        @endif

        @if($clinicId !== null && $clinicId !== '')
            <a href="{{ route('organisation.inventory.movements') }}" class="btn-clear" style="margin-left:auto;">Clear</a>
        @endif
    </form>
</div>

@if($clinicId === null || $clinicId === '')
    {{-- No selection yet --}}
    <div class="mov-prompt">
        <div class="icon">📋</div>
        <h3>Select a Location</h3>
        <p>Choose "Central Stock" or a specific clinic above to view its inventory movement log.</p>
    </div>
@elseif($movements && $movements->count())
    {{-- Results table --}}
    <table class="mov-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Item</th>
                <th>Type</th>
                <th style="text-align:right;">Qty</th>
                <th>Notes</th>
                <th>By</th>
            </tr>
        </thead>
        <tbody>
        @foreach($movements as $m)
            <tr>
                <td style="font-size:13px; white-space:nowrap;">{{ $m->created_at ? $m->created_at->format('d M Y H:i') : '—' }}</td>
                <td style="font-weight:500;">{{ optional($m->inventoryItem)->name ?? '—' }}</td>
                <td>
                    @php
                        $cls = match($m->movement_type) {
                            'purchase' => 'bg-purchase',
                            'manual_adjustment' => 'bg-adjustment',
                            'treatment_usage' => 'bg-usage',
                            'transfer_in','transfer_out' => 'bg-transfer',
                            'expired' => 'bg-expired',
                            default => 'bg-adjustment',
                        };
                    @endphp
                    <span class="badge-m {{ $cls }}">{{ ucfirst(str_replace('_',' ',$m->movement_type)) }}</span>
                </td>
                <td style="text-align:right; font-weight:600; {{ $m->quantity < 0 ? 'color:#dc2626;' : 'color:#065f46;' }}">
                    {{ $m->quantity > 0 ? '+' : '' }}{{ number_format($m->quantity, 1) }}
                </td>
                <td style="font-size:13px; color:#6b7280; max-width:280px;">{{ $m->notes ?: '—' }}</td>
                <td style="font-size:13px;">{{ optional($m->createdBy)->name ?? '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($movements->hasPages())
        <div class="mov-pagination">
            {{ $movements->appends(request()->query())->links('pagination::simple-bootstrap-5') }}
        </div>
    @endif
@else
    {{-- Empty state --}}
    <div class="mov-prompt">
        <div class="icon">📭</div>
        <h3>No Movements Found</h3>
        <p>No inventory movements recorded for this location{{ $type ? ' with the selected filter' : '' }}.</p>
    </div>
@endif

@endsection
