@extends('clinic.layout')

@section('content')

<div class="card">

<h2>Create Bill</h2>

<p>
<strong>Pet:</strong> {{ $appointment->pet->name }}
</p>

<hr>

<h4>Treatments Performed</h4>

@if($appointment->treatments->count())

<ul>

@foreach($appointment->treatments as $treatment)

<li>
{{ $treatment->priceItem->name }}
 - ₹{{ $treatment->priceItem->price }}
</li>

@endforeach

</ul>

@else

<p>No treatments added</p>

@endif

<hr>

<h4>Add Extra Item</h4>

<select>

<option value="">Select item</option>

@foreach($priceItems as $item)

<option value="{{ $item->id }}">
{{ $item->name }} — ₹{{ $item->price }}
</option>

@endforeach

</select>

<button>Add Item</button>

</div>

@endsection