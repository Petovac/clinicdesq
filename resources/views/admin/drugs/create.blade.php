@extends('admin.layout')

@section('content')

<h2>Add Drug</h2>

<form method="POST" action="/admin/drugs">

@csrf

<div style="margin-bottom:15px">

<label>Drug Generic Name</label>

<br>

<input type="text"
       name="name"
       required
       style="width:400px;padding:8px;border:1px solid #ccc;border-radius:4px">

</div>

<div style="margin-bottom:15px">

<label>Drug Class</label>

<br>

<input type="text"
       name="drug_class"
       placeholder="NSAID / Antibiotic"
       style="width:400px;padding:8px;border:1px solid #ccc;border-radius:4px">

</div>

<button style="padding:10px 14px;background:#10b981;color:white;border:none;border-radius:4px">
Save Drug
</button>

</form>

@endsection