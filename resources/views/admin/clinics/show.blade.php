@extends('admin.layout')

@section('content')
<h2>{{ $clinic->name }}</h2>

<p><strong>Phone:</strong> {{ $clinic->phone }}</p>
<p><strong>Email:</strong> {{ $clinic->email }}</p>
<p><strong>City:</strong> {{ $clinic->city }}</p>
<p><strong>State:</strong> {{ $clinic->state }}</p>

<hr>

<h3>Assigned Vets</h3>

@if($clinic->vets->count())
    <ul>
        @foreach($clinic->vets as $vet)
            <li>{{ $vet->name }} ({{ $vet->specialization }})</li>
        @endforeach
    </ul>
@else
    <p>No vets assigned</p>
@endif

<a href="/admin/clinics">← Back</a>
@endsection
