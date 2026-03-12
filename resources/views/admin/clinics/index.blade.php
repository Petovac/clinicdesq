@extends('admin.layout')

<style>
/* ===== Clinics Table Styling ===== */

.table-wrapper {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    overflow: hidden;
    max-width: 900px;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

thead {
    background: #f9fafb;
}

th {
    text-align: left;
    padding: 12px 14px;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
}

td {
    padding: 12px 14px;
    color: #1f2937;
    border-bottom: 1px solid #f1f5f9;
}

tbody tr:hover {
    background: #f9fafb;
}

/* Action links */
.action-link {
    color: #4f46e5;
    font-weight: 500;
    text-decoration: none;
    margin-right: 10px;
}

.action-link:hover {
    text-decoration: underline;
}

/* Add clinic link */
.add-clinic {
    display: inline-block;
    margin-bottom: 12px;
    color: #4f46e5;
    font-weight: 600;
    text-decoration: none;
}

.add-clinic:hover {
    text-decoration: underline;
}
</style>


@section('content')
<h1>Organisations</h1>

<a href="{{ url('/admin/clinics/create') }}" class="add-clinic">
    + Add Organisation
</a>


<div class="table-wrapper" style="margin-top:16px;">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email (Login)</th>
                <th>City</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($clinics as $clinic)
                <tr>
                    <td>{{ $clinic->name }}</td>

                    {{-- clinic login email --}}
                    <td>
                        {{ $clinic->email ?? '-' }}
                    </td>

                    <td>{{ $clinic->city ?? '-' }}</td>
                    <td>{{ $clinic->phone ?? '-' }}</td>

                    <td>
                        <a href="{{ url('/admin/clinics/'.$clinic->id) }}" class="action-link">
                            View
                        </a>
                        <a href="{{ url('/admin/clinics/'.$clinic->id.'/edit') }}" class="action-link">
                            Edit
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
