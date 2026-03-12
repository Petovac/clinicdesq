@extends('layouts.vet')

@section('content')

<style>
    /* ===== Card Layout ===== */
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

    /* ===== Heading ===== */
    h2 {
        text-align: center;
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #111827;
    }

    /* ===== Labels & Inputs ===== */
    label {
        display: block;
        margin-top: 14px;
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
    }

    input {
        width: 100%;
        padding: 10px 12px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }

    /* ===== Button ===== */
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

    /* ===== Responsive ===== */
    @media (max-width: 480px) {
        .card {
            margin: 16px;
            padding: 22px;
        }
    }
</style>

<div class="card">
    <h2>Add Pet for {{ $parent->name }}</h2>

    <form method="POST" action="{{ route('vet.pets.store', $parent->id) }}">
        @csrf

        <label>Pet Name</label>
        <input name="name" required>

        <label>Species</label>

        <select name="species" required>
            <option value="">Select Species</option>
            <option value="dog">Dog</option>
            <option value="cat">Cat</option>
            <option value="rabbit">Rabbit</option>
            <option value="bird">Bird</option>
            <option value="horse">Horse</option>
            <option value="cow">Cow</option>
            <option value="goat">Goat</option>
        </select>

        <label>Breed</label>
        <input name="breed">

        <label>Age</label>

        <div style="display:flex; gap:12px;">
            <input
                type="number"
                name="age"
                min="0"
                placeholder="Years"
                required
                style="flex:1;"
            >

            <input
                type="number"
                name="age_months"
                min="0"
                max="11"
                placeholder="Months"
                style="flex:1;"
            >
        </div>

        <p style="font-size:12px;color:#6b7280;margin-top:4px;">
            Example: 3 years 6 months → enter 3 and 6
        </p>

        <label>Gender</label>
        <input name="gender">

        <button type="submit">Add Pet</button>

        <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">

    </form>
</div>
@endsection
