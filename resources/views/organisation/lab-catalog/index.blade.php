@extends('organisation.layout')

@section('content')

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.page-header h2 {
    font-size: 22px;
    font-weight: 600;
    margin: 0;
    color: #111827;
}
.card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.btn-primary {
    background: #2563eb;
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    display: inline-block;
}
.btn-primary:hover { background: #1d4ed8; }
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
thead th {
    background: #f9fafb;
    text-transform: uppercase;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-align: left;
    padding: 10px 14px;
    border-bottom: 1px solid #e5e7eb;
}
tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f1f5f9;
    color: #111827;
}
tbody tr:hover { background: #f9fafb; }
.badge-active {
    background: #dcfce7;
    color: #166534;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    display: inline-block;
}
.badge-inactive {
    background: #fee2e2;
    color: #991b1b;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    display: inline-block;
}
.action-link {
    color: #2563eb;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
}
.action-link:hover { text-decoration: underline; }
.action-link-danger {
    color: #dc2626;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}
.action-link-danger:hover { text-decoration: underline; }
.empty-state {
    text-align: center;
    padding: 40px 0;
    color: #6b7280;
    font-size: 14px;
}
</style>

<div class="page-header">
    <h2>Lab Test Catalog</h2>
    <a href="{{ route('organisation.lab-catalog.create') }}" class="btn-primary">+ Add Test</a>
</div>

@if(session('success'))
    <div style="background:#dcfce7; color:#166534; padding:10px 16px; border-radius:8px; margin-bottom:16px; font-size:13px;">
        {{ session('success') }}
    </div>
@endif

<div class="card" style="overflow-x:auto;">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Category</th>
                <th>Sample Type</th>
                <th>Parameters</th>
                <th>Est. Time</th>
                <th>Price (&#8377;)</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tests as $test)
                <tr>
                    <td style="font-weight:500;">{{ $test->name }}</td>
                    <td>{{ $test->code ?? '-' }}</td>
                    <td style="text-transform:capitalize;">{{ $test->category ?? '-' }}</td>
                    <td style="text-transform:capitalize;">{{ $test->sample_type ?? '-' }}</td>
                    <td>
                        @if($test->parameters)
                            {{ is_array($test->parameters) ? implode(', ', $test->parameters) : $test->parameters }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $test->estimated_time ?? '-' }}</td>
                    <td>{{ $test->price ? number_format($test->price, 2) : '-' }}</td>
                    <td>
                        @if($test->is_active)
                            <span class="badge-active">Active</span>
                        @else
                            <span class="badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('organisation.lab-catalog.edit', $test) }}" class="action-link">Edit</a>
                        &nbsp;|&nbsp;
                        <form method="POST" action="{{ route('organisation.lab-catalog.destroy', $test) }}" style="display:inline;" onsubmit="return confirm('Delete this test?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-link-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-state">No lab tests added yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
