@extends('organisation.layout')

@section('content')

<h2>Edit Role</h2>

<form method="POST" action="{{ route('organisation.roles.update',$role->id) }}">
@csrf
@method('PUT')

<div style="background:#fff;padding:20px;border-radius:8px;max-width:700px;">

<label>Role Name</label>
<br>
<input type="text" name="name"
value="{{ $role->name }}"
style="width:100%;padding:8px;margin-bottom:15px">

<label>Clinic Scope</label>
<br>
<select name="clinic_scope" style="width:100%;padding:8px;margin-bottom:20px">
<option value="none" {{ $role->clinic_scope=='none'?'selected':'' }}>None</option>
<option value="single" {{ $role->clinic_scope=='single'?'selected':'' }}>Single Clinic</option>
<option value="multiple" {{ $role->clinic_scope=='multiple'?'selected':'' }}>Multiple Clinics</option>
</select>

<h4>Permissions</h4>

@foreach($permissions as $permission)

<div style="margin-bottom:6px">

<label>
<input type="checkbox"
name="permissions[]"
value="{{ $permission->id }}"
{{ in_array($permission->id,$rolePermissions) ? 'checked' : '' }}>

{{ $permission->name }}

</label>

</div>

@endforeach

<br>

<button style="background:#4f46e5;color:#fff;padding:10px 16px;border-radius:6px;border:none">
Update Role
</button>

</div>

</form>

@endsection