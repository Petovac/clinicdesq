@extends('admin.layout')

<style>

/* Page title */

h2{
font-size:26px;
font-weight:600;
color:#111827;
margin-bottom:18px;
}


/* Top bar */

.top-bar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:15px;
}


/* Add Vet button */

.add-btn{
background:#2563eb;
color:white;
padding:9px 14px;
border-radius:6px;
text-decoration:none;
font-size:14px;
font-weight:600;
}

.add-btn:hover{
background:#1d4ed8;
}


/* Search */

.search-box input{
padding:8px 12px;
border:1px solid #d1d5db;
border-radius:6px;
width:260px;
}

.search-box input:focus{
outline:none;
border-color:#10b981;
box-shadow:0 0 0 2px rgba(16,185,129,0.15);
}


/* Table */

table{
width:100%;
border-collapse:collapse;
background:white;
border-radius:8px;
overflow:hidden;
box-shadow:0 1px 3px rgba(0,0,0,0.08);
}

th{
background:#f3f4f6;
text-align:left;
padding:12px;
font-size:14px;
color:#374151;
border-bottom:1px solid #e5e7eb;
}

td{
padding:12px;
border-bottom:1px solid #f1f5f9;
font-size:14px;
}

tr:hover td{
background:#f9fafb;
}

</style>


@section('content')

<h2>Vets</h2>


<div class="top-bar">

<a href="/admin/vets/create" class="add-btn">
+ Add Vet
</a>

<form method="GET" class="search-box">
<input type="text" name="search" placeholder="Search name, phone, reg no..." value="{{ request('search') }}">
</form>

</div>


<table>

<tr>
<th>Name</th>
<th>Phone</th>
<th>Registration No.</th>
<th>Specialization</th>
</tr>

@foreach($vets as $vet)

<tr>
<td>{{ $vet->name }}</td>
<td>{{ $vet->phone }}</td>
<td>{{ $vet->registration_number }}</td>
<td>{{ $vet->specialization }}</td>
</tr>

@endforeach

</table>

@endsection