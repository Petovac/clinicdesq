@extends('organisation.layout')

@section('content')

<h2>Create Role</h2>

<div class="card" style="max-width:600px;padding:20px;background:#fff;border-radius:8px;">

    <form method="POST" action="{{ route('organisation.roles.store') }}">
        @csrf

        <div style="margin-bottom:15px;">
            <label>Role Name</label>
            <input 
                type="text" 
                name="name" 
                required 
                style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
        </div>

        <div style="margin-bottom:15px;">
            <label>Clinic Scope</label>

            <select name="clinic_scope" required 
                style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">

                <option value="none">No Clinic (Central Role)</option>

                <option value="single">
                    Single Clinic (Clinic Manager / Receptionist)
                </option>

                <option value="multiple">
                    Multiple Clinics (Area / Regional Manager)
                </option>

            </select>
        </div>

        <div style="margin-top:20px;">

            <h3>Permissions</h3>

            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:8px; margin-top:10px;">

                @foreach($permissions as $permission)
                    <label style="display:flex; gap:6px; align-items:center;">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}">
                        {{ $permission->name }}
                    </label>
                @endforeach

            </div>

        </div>
        
        <button 
            type="submit" 
            style="background:#4f46e5;color:white;padding:10px 16px;border:none;border-radius:6px;">
            Create Role
        </button>

    </form>

</div>

@endsection