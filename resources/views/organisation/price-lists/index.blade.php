@extends('organisation.layout')

@section('content')

<style>
.page-title {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 20px;
}

.card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    padding: 20px;
}

.header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.btn {
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 14px;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.btn-primary {
    background: #4f46e5;
    color: #fff;
}

.btn-secondary {
    background: #e5e7eb;
    color: #111827;
}

.btn-success {
    background: #16a34a;
    color: #fff;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    text-align: left;
    font-size: 13px;
    color: #6b7280;
    padding: 10px;
    border-bottom: 1px solid #e5e7eb;
}

.table td {
    padding: 12px 10px;
    border-bottom: 1px solid #f1f5f9;
}

.badge {
    padding: 4px 10px;
    font-size: 12px;
    border-radius: 999px;
}

.badge-active {
    background: #dcfce7;
    color: #166534;
}

.badge-inactive {
    background: #f1f5f9;
    color: #475569;
}

.actions {
    display: flex;
    gap: 8px;
}
</style>

<div class="header-row">
    <h2 class="page-title">Price Lists</h2>

    <a href="{{ route('organisation.price-lists.create') }}" class="btn btn-primary">
        + Create Price List
    </a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Items</th>
                <th>Status</th>
                <th width="220">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lists as $list)
                <tr>
                    <td>{{ $list->name }}</td>
                    <td>{{ $list->items_count }}</td>
                    <td>
                        @if($list->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td class="actions">
                        <a href="{{ route('organisation.price-lists.edit', $list) }}"
                           class="btn btn-secondary">
                            Edit
                        </a>

                        @if(!$list->is_active)
                            <form method="POST"
                                  action="{{ route('organisation.price-lists.activate', $list) }}">
                                @csrf
                                <button class="btn btn-success">
                                    Activate
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection