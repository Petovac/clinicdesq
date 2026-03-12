@extends('admin.layout')

@section('content')

<h2>Create Organisation</h2>

@if ($errors->any())
    <div style="color: red; margin-bottom: 10px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ url('/admin/organisations') }}">
    @csrf

    <h4>Organisation Details</h4>

    <div style="margin-bottom: 10px;">
        <label>Organisation Name</label><br>
        <input type="text" name="org_name" value="{{ old('org_name') }}" required>
    </div>

    <div style="margin-bottom: 10px;">
        <label>Organisation Type</label><br>
        <select name="org_type" required>
            <option value="">-- Select --</option>
            <option value="single_clinic" {{ old('org_type') == 'single_clinic' ? 'selected' : '' }}>
                Single Clinic
            </option>
            <option value="corporate" {{ old('org_type') == 'corporate' ? 'selected' : '' }}>
                Corporate
            </option>
        </select>
    </div>

    <h4>Organisation Owner</h4>

    <div style="margin-bottom: 10px;">
        <label>Owner Name</label><br>
        <input type="text" name="owner_name" value="{{ old('owner_name') }}" required>
    </div>

    <div style="margin-bottom: 10px;">
        <label>Owner Email</label><br>
        <input type="email" name="owner_email" value="{{ old('owner_email') }}" required>
    </div>

    <div style="margin-bottom: 10px;">
        <label>Owner Phone</label><br>
        <input type="text" name="owner_phone" value="{{ old('owner_phone') }}" required>
    </div>

    <button type="submit">Create Organisation</button>
</form>

<br>
<a href="{{ url('/admin/organisations') }}">← Back to Organisations</a>

@endsection
