@extends('clinic.layout')

@section('content')

<div class="container">

<h2>Search Pet Parent</h2>

<form method="POST" action="{{ route('clinic.appointments.search') }}">
@csrf

<div style="max-width:400px">

<label>Phone Number</label>

<input 
type="text"
name="mobile"
value="{{ $mobile ?? '' }}"
required
class="form-control"
>

<button class="btn btn-primary mt-2">
Search
</button>

</div>

</form>

@if(isset($notFound))

<div style="margin-top:20px;color:red">
Pet parent not found.
</div>

@endif

</div>

@endsection