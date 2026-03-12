@extends('clinic.layout')

@section('content')

<div class="container">

<h2>Pet Parent</h2>

<div class="card" style="padding:20px;margin-bottom:20px">
    <p><strong>Name:</strong> {{ $petParent->name }}</p>
    <p><strong>Phone:</strong> {{ $petParent->phone }}</p>
</div>

<h3>Pets</h3>

@if($petParent->pets->count() == 0)

<p>No pets found.</p>

<a href="{{ route('vet.pets.create', $petParent->id) }}"
class="btn btn-primary">
+ Add Pet
</a>

@else

<table class="table table-bordered">

<thead>
<tr>
<th>Pet</th>
<th>Species</th>
<th>Age</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@foreach($petParent->pets as $pet)

<tr>

<td>{{ $pet->name }}</td>

<td>{{ ucfirst($pet->species) }}</td>

<td>
{{ $pet->age ?? '-' }}
</td>

<td>

<a href="{{ route('clinic.appointments.createForPet',$pet->id) }}"
class="btn btn-success btn-sm">
Create Appointment
</a>

</td>

</tr>

@endforeach

</tbody>

</table>

@endif

</div>

@endsection