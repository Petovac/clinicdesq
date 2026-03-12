@extends('admin.layout')

<style>

/* Page Title */

h2{
font-size:26px;
color:#111827;
margin-bottom:15px;
font-weight:600;
}


/* Add Drug Button */

a[href="/admin/drugs/create"]{
display:inline-block;
margin-bottom:18px;
padding:10px 16px;
background:#2563eb;
color:white !important;
text-decoration:none;
border-radius:6px;
font-size:14px;
font-weight:600;
transition:all .15s ease;
}

a[href="/admin/drugs/create"]:hover{
background:#1d4ed8;
}


/* Table */

table{
width:100%;
background:white;
border-collapse:collapse;
border-radius:8px;
overflow:hidden;
box-shadow:0 1px 3px rgba(0,0,0,0.08);
font-size:14px;
}


/* Table Header */

table tr:first-child{
background:#f3f4f6;
}

th{
text-align:left;
padding:14px;
color:#374151;
font-weight:600;
border-bottom:1px solid #e5e7eb;
}


/* Table Rows */

td{
padding:14px;
border-bottom:1px solid #f1f5f9;
color:#374151;
}

tr:hover td{
background:#f9fafb;
}


/* Drug Name Links */

td a{
color:#2563eb;
font-weight:500;
text-decoration:none;
}

td a:hover{
text-decoration:underline;
}

</style>

@section('content')

<h2>Drug Knowledge Base</h2>

<a href="/admin/drugs/create"
   style="display:inline-block;margin-bottom:15px;padding:8px 12px;background:#2563eb;color:white;text-decoration:none;border-radius:4px;">
   + Add Drug
</a>

<table style="width:100%;background:white;border-collapse:collapse">

<tr style="background:#f9fafb">
<th style="padding:10px;border-bottom:1px solid #e5e7eb">Drug</th>
<th style="padding:10px;border-bottom:1px solid #e5e7eb">Class</th>
</tr>

@foreach($drugs as $drug)

<tr>
<td style="padding:10px;border-bottom:1px solid #eee">
<a href="/admin/drugs/{{ $drug->id }}/edit" style="color:#2563eb;text-decoration:none">
    {{ $drug->name }}
</a>
</td>

<td style="padding:10px;border-bottom:1px solid #eee">
{{ $drug->drug_class }}
</td>
</tr>

@endforeach

</table>

@endsection