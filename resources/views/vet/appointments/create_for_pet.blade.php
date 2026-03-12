@extends('layouts.vet')

@section('content')

<style>
    .card {
        max-width: 520px;
        margin: 32px auto;
        padding: 26px 28px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1f2937;
    }

    h2 {
        text-align: center;
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 18px;
        color: #111827;
    }

    p {
        font-size: 14px;
        margin-bottom: 6px;
        color: #374151;
    }

    p strong {
        color: #111827;
        font-weight: 600;
    }

    label {
        display: block;
        margin-top: 16px;
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
    }

    input[type="datetime-local"] {
        width: 100%;
        padding: 10px 12px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    input[type="datetime-local"]:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }

    button {
        width: 100%;
        margin-top: 24px;
        padding: 12px;
        font-size: 15px;
        font-weight: 500;
        color: #ffffff;
        background-color: #2563eb;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }

    button:hover {
        background-color: #1e40af;
    }

    button:active {
        transform: scale(0.98);
    }

    @media (max-width: 480px) {
        .card {
            margin: 16px;
            padding: 22px;
        }
    }
</style>

<div class="card">
    <h2>Create Appointment</h2>

    <p><strong>Pet:</strong> {{ $pet->name }}</p>
    <p><strong>Pet Parent:</strong> {{ $pet->petParent->name }}</p>

    <form method="POST" action="{{ route('vet.appointments.store') }}">
    @csrf

    <input type="hidden" name="pet_id" value="{{ $pet->id }}">
    <input type="hidden" name="pet_parent_id" value="{{ $pet->pet_parent_id }}">

    <label>Appointment Date & Time</label>
    <input type="datetime-local" name="scheduled_at" required>

    <br><br>

    <label>Pet Weight (kg) <span style="color:red;">*</span></label>

    <input
        type="number"
        step="0.1"
        name="weight"
        required
        value="{{ old('weight', $lastWeight) }}"
        placeholder="Enter weight in kg"
        style="
            width:100%;
            padding:10px 12px;
            border:1px solid #d1d5db;
            border-radius:6px;
            font-size:14px;
        "
    >

    @if($lastWeight)
        <p style="font-size:12px;color:#6b7280;margin-top:4px;">
            Last recorded weight: {{ $lastWeight }} kg
        </p>
    @endif

    <button type="submit">Create Appointment</button>
    </form>

</div>

@endsection
