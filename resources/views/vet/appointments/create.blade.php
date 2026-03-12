@extends('layouts.vet')

@section('content')

<style>
    .card {
        max-width: 520px;
        margin: 30px auto;
        padding: 24px 28px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .card h2 {
        margin-bottom: 18px;
        font-size: 22px;
        font-weight: 600;
        color: #1f2937;
        text-align: center;
    }

    .card h3 {
        margin-top: 20px;
        margin-bottom: 10px;
        font-size: 18px;
        color: #2563eb;
        font-weight: 600;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
    }

    input[type="text"] {
        width: 100%;
        padding: 10px 12px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        margin-bottom: 14px;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    input[type="text"]:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }

    button {
        display: inline-block;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 500;
        color: #ffffff;
        background-color: #2563eb;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }

    button:hover {
        background-color: #1e40af;
    }

    button:active {
        transform: scale(0.97);
    }

    hr {
        margin: 22px 0;
        border: none;
        border-top: 1px solid #e5e7eb;
    }

    p {
        font-size: 14px;
        color: #374151;
        margin-bottom: 8px;
    }

    p strong {
        color: #111827;
    }

    .error-text {
        color: #dc2626;
        font-size: 14px;
        margin-top: 14px;
        font-weight: 500;
    }

    .secondary-btn {
        background-color: #059669;
    }

    .secondary-btn:hover {
        background-color: #047857;
    }

    @media (max-width: 480px) {
        .card {
            margin: 16px;
            padding: 20px;
        }
    }
</style>

<div class="card">
    <h2>Create Appointment</h2>

    {{-- STEP 1: SEARCH --}}
    <form method="POST" action="{{ route('vet.appointments.search') }}">
        @csrf

        <label>Pet Parent Mobile Number</label>
        <input
            type="text"
            name="mobile"
            value="{{ old('mobile', $mobile ?? '') }}"
            required
        >

        <button type="submit">Search</button>
    </form>

    <hr>

    {{-- STEP 2A: PET PARENT FOUND --}}
    @isset($petParent)
        <h3>Pet Parent Found</h3>

        <p><strong>Name:</strong> {{ $petParent->name }}</p>
        <p><strong>Mobile:</strong> {{ $petParent->phone }}</p>

        <form method="POST" action="{{ route('vet.appointments.requestAccess') }}">
            @csrf
            <input type="hidden" name="mobile" value="{{ $petParent->phone }}">
            <button type="submit" class="secondary-btn">Request Access</button>
        </form>
    @endisset

    {{-- STEP 2B: PET PARENT NOT FOUND --}}
    @isset($notFound)
        <p class="error-text">
            No pet parent found with this number.
        </p>

        <form method="GET" action="{{ route('vet.petparent.create') }}">
            <button type="submit">Create Pet Parent Profile</button>
        </form>
    @endisset
</div>

@endsection