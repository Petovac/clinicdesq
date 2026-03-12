@extends('admin.layout')

@section('content')

<h2>Organisations</h2>

<p>
    <a href="{{ url('/admin/organisations/create') }}">+ Create Organisation</a>
</p>

@if(session('success'))
    <div style="color: green; margin-bottom: 10px;">
        {{ session('success') }}
    </div>
@endif

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <th>ID</th>
        <th>Organisation Name</th>
        <th>Type</th>
        <th>Primary Email</th>
        <th>Primary Phone</th>
        <th>Created At</th>
    </tr>

    @forelse($organisations as $org)
        <tr>
            <td>{{ $org->id }}</td>
            <td>{{ $org->name }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $org->type)) }}</td>
            <td>{{ $org->primary_email }}</td>
            <td>{{ $org->primary_phone }}</td>
            <td>{{ $org->created_at }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6">No organisations created yet.</td>
        </tr>
    @endforelse
</table>

@endsection
