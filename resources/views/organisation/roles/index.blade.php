@extends('organisation.layout')

@section('content')

<h2>Roles</h2>

<a href="{{ route('organisation.roles.create') }}" 
   style="display:inline-block;margin-bottom:15px;background:#4f46e5;color:#fff;padding:8px 14px;border-radius:6px;text-decoration:none;">
   + Create Role
</a>

<div style="background:#fff;padding:20px;border-radius:8px;max-width:700px;">

@if($roles->count() == 0)
    <p>No roles created yet.</p>
@else

<table width="100%" cellpadding="10">
    <tr style="border-bottom:1px solid #ddd;">
    <th align="left">Role Name</th>
    <th align="left">Actions</th>
    </tr>

@foreach($roles as $role)

<tr style="border-bottom:1px solid #eee;">

<td>{{ $role->name }}</td>

<td>
<a href="{{ route('organisation.roles.edit', $role->id) }}"
style="background:#10b981;color:#fff;padding:6px 10px;border-radius:5px;text-decoration:none;">
Edit
</a>
</td>

</tr>

@endforeach

</table>

@endif

</div>

@endsection