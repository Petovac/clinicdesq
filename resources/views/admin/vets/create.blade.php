@extends('admin.layout')

<style>

/* Page Title */

h2{
font-size:26px;
font-weight:600;
color:#111827;
margin-bottom:20px;
}


/* Form Card */

form{
background:#ffffff;
padding:25px;
border-radius:8px;
box-shadow:0 1px 3px rgba(0,0,0,0.08);
max-width:500px;
}


/* Field spacing */

form p{
margin-bottom:18px;
font-size:14px;
color:#374151;
}


/* Inputs */

input{
width:100%;
margin-top:6px;
padding:10px 12px;
border:1px solid #d1d5db;
border-radius:6px;
font-size:14px;
box-sizing:border-box;
}

input:focus{
outline:none;
border-color:#10b981;
box-shadow:0 0 0 2px rgba(16,185,129,0.15);
}


/* Button */

button{
margin-top:5px;
padding:10px 16px;
background:#10b981;
color:white;
border:none;
border-radius:6px;
font-weight:600;
cursor:pointer;
transition:all .15s ease;
}

button:hover{
background:#059669;
}


/* Back link */

a{
display:inline-block;
margin-top:15px;
color:#2563eb;
text-decoration:none;
font-size:14px;
}

a:hover{
text-decoration:underline;
}

</style>

@section('content')
<h2>Add Vet</h2>

<form method="POST" action="/admin/vets">
    @csrf

    <p>
        Name<br>
        <input type="text" name="name">
    </p>

    <p>
        Phone<br>
        <input type="text" name="phone">
    </p>

    <p>
        Email<br>
        <input type="email" name="email">
    </p>

    <p>
        Registration Number<br>
        <input type="text" name="registration_number">
    </p>

    <p>
        Specialization<br>
        <input type="text" name="specialization">
    </p>

    <button type="submit">Create Vet</button>
</form>

<a href="/admin/vets">← Back</a>
@endsection
