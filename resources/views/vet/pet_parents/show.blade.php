@extends('layouts.vet')

@section('content')

<style>
    /* ===== Main Card ===== */
    .card {
        max-width: 1000px;
        margin: 30px auto;
        padding: 28px 32px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.08);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1f2937;
    }

    /* ===== Headings ===== */
    h2 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #111827;
        text-align: center;
    }

    h3 {
        font-size: 18px;
        font-weight: 600;
        margin-top: 20px;
        margin-bottom: 14px;
        color: #2563eb;
    }

    h4 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #111827;
        text-transform: capitalize;
    }

    /* ===== Text ===== */
    p {
        font-size: 14px;
        margin-bottom: 6px;
        color: #374151;
    }

    p strong {
        color: #111827;
    }

    hr {
        margin: 22px 0;
        border: none;
        border-top: 1px solid #e5e7eb;
    }

    /* ===== Primary Button ===== */
    .btn-primary {
        display: inline-block;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 500;
        color: #ffffff;
        background-color: #2563eb;
        border-radius: 8px;
        text-decoration: none;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }

    .btn-primary:hover {
        background-color: #1e40af;
    }

    .btn-primary:active {
        transform: scale(0.97);
    }

    /* ===== Pets Grid ===== */
    .pets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
        margin-top: 16px;
    }

    /* ===== Pet Card ===== */
    .pet-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        transition: box-shadow 0.2s ease, transform 0.15s ease;
    }

    .pet-card:hover {
        box-shadow: 0 10px 24px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }

    /* ===== Pet Meta ===== */
    .pet-meta {
        font-size: 14px;
        color: #374151;
        margin-bottom: 10px;
    }

    .pet-divider {
        height: 1px;
        background: #e5e7eb;
        margin: 12px 0;
    }

    /* ===== Actions ===== */
    .pet-actions a {
        display: block;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        margin-bottom: 6px;
    }

    .pet-actions a.primary {
        color: #2563eb;
    }

    .pet-actions a.secondary {
        color: #059669;
    }

    .pet-actions a:hover {
        text-decoration: underline;
    }

    /* ===== Mobile ===== */
    @media (max-width: 640px) {
        .card {
            margin: 16px;
            padding: 22px;
        }

        .pets-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="card">
    <h2>Pet Parent Profile</h2>

    <p><strong>Name:</strong> {{ $petParent->name }}</p>
    <p><strong>Mobile:</strong> {{ $petParent->phone }}</p>

    <div style="margin: 15px 0;">
        <a href="{{ route('vet.pets.create', $petParent->id) }}?redirect_to={{ urlencode(url()->current()) }}"
           class="btn-primary">
            + Add New Pet
        </a>
    </div>

    <hr>

    <h3>Pets</h3>

    @if($petParent->pets->count() === 0)
        <p>No pets added yet.</p>
    @else
        <div class="pets-grid">
            @foreach($petParent->pets as $pet)
                <div class="pet-card">
                    <h4>{{ $pet->name }}</h4>

                    <div class="pet-meta">
                        {{ ucfirst($pet->species) }} <br>
                        Age: {{ $pet->age ?? '-' }}
                    </div>

                    <div class="pet-divider"></div>

                    <div class="pet-actions">
                        <a href="{{ route('vet.pet.show', $pet->id) }}" class="primary">
                            View Pet Profile
                        </a>
                        <a href="{{ route('vet.appointments.createForPet', $pet->id) }}" class="secondary">
                            Create Appointment
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection