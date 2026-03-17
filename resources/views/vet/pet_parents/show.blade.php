@extends('layouts.vet')

@section('content')

<div style="max-width:1000px;margin:0 auto;">
    <div class="v-card">
        <h2 style="text-align:center;font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 14px;">
            Pet Parent Profile
        </h2>

        <p style="margin-bottom:6px;"><strong style="color:var(--text-dark);">Name:</strong> {{ $petParent->name }}</p>
        <p style="margin-bottom:16px;"><strong style="color:var(--text-dark);">Mobile:</strong> {{ $petParent->phone }}</p>

        <a href="{{ route('vet.pets.create', $petParent->id) }}?redirect_to={{ urlencode(url()->current()) }}"
           class="v-btn v-btn--primary v-btn--sm">
            + Add New Pet
        </a>

        <hr class="v-divider">

        <h3 class="v-section-title">Pets</h3>

        @if($petParent->pets->count() === 0)
            <p style="color:var(--text-muted);">No pets added yet.</p>
        @else
            <div class="v-grid v-grid--3">
                @foreach($petParent->pets as $pet)
                    <div class="v-card v-card--compact" style="margin-bottom:0;">
                        <h4 style="font-size:16px;font-weight:600;color:var(--text-dark);margin:0 0 6px;text-transform:capitalize;">
                            {{ $pet->name }}
                        </h4>

                        <p style="font-size:14px;color:var(--text);margin:0 0 12px;">
                            {{ ucfirst($pet->species) }}
                            @if($pet->age) &middot; Age: {{ $pet->age }} @endif
                        </p>

                        <div style="border-top:1px solid var(--border);padding-top:10px;display:flex;flex-direction:column;gap:6px;">
                            <a href="{{ route('vet.pet.show', $pet->id) }}" class="v-link" style="font-size:14px;">
                                View Pet Profile
                            </a>
                            <a href="{{ route('vet.appointments.createForPet', $pet->id) }}" class="v-link" style="font-size:14px;color:var(--success);">
                                Create Appointment
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
