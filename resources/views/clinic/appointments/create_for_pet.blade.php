@extends('clinic.layout')

@section('content')

<div class="container">

<h2>Create Appointment</h2>

<div class="card" style="padding:20px;margin-bottom:20px">

<p><strong>Owner:</strong> {{ $pet->petParent->name }}</p>
<p><strong>Pet:</strong> {{ $pet->name }}</p>

</div>

<form method="POST" action="{{ route('clinic.appointments.store') }}">

@csrf

<input type="hidden" name="pet_id" value="{{ $pet->id }}">
<input type="hidden" name="pet_parent_id" value="{{ $pet->pet_parent_id }}">

<div style="max-width:400px">

<label>Assign Vet (optional)</label>

<select name="vet_id" class="form-control">
<option value="">Unassigned</option>

@foreach($vets as $vet)
<option value="{{ $vet->id }}">
{{ $vet->name }}
</option>
@endforeach

</select>

<br>

<label>Weight (kg) – optional</label>

<input
type="number"
step="0.01"
name="weight"
class="form-control">

<br>

<label>Date & Time</label>

<input
type="datetime-local"
name="scheduled_at"
required
class="form-control">

<br>

<button class="btn btn-success">
Create Appointment
</button>

</div>

</form>

</div>

@endsection