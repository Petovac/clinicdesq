@extends('organisation.layout')

@section('content')

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.page-header h2 {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
}

.btn {
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 13px;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: #4f46e5;
    color: #fff;
}

.btn-secondary {
    background: #e5e7eb;
    color: #111827;
}

.card {
    background: #fff;
    border-radius: 10px;
    padding: 16px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.04);
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead th {
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    padding: 12px 10px;
    border-bottom: 1px solid #e5e7eb;
}

.table tbody td {
    padding: 12px 10px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
}

.role-badge {
    background: #eef2ff;
    color: #3730a3;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    display: inline-block;
}

.empty {
    text-align: center;
    padding: 30px;
    color: #6b7280;
    font-size: 14px;
}
</style>

<div class="page-header">
    <h2>Users</h2>

    <a href="{{ route('organisation.users.create') }}" class="btn btn-primary">
        + Create User
    </a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Clinics</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>

                    <td>
                        <span class="role-badge">
                        {{ $user->role_name }}
                        </span>
                    </td>

                    <td>
                    {{ $user->clinic_name ?? '—' }}
                    </td>
             
                    <td>
                    <a href="{{ route('organisation.users.edit', $user->id) }}"
                    class="btn btn-secondary">
                    Edit
                    </a>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="4" class="empty">
                        No users found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
