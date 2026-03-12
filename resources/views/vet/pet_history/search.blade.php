@extends('layouts.vet')

@section('content')

<style>
    .history-wrapper {
        max-width: 520px;
        margin: 40px auto;
        background: #ffffff;
        padding: 28px 32px;
        border-radius: 12px;
        box-shadow: 0 10px 26px rgba(0,0,0,0.08);
    }

    h2 {
        text-align: center;
        font-size: 22px;
        margin-bottom: 18px;
        color: #111827;
    }

    label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        display: block;
        margin-bottom: 6px;
    }

    input {
        width: 100%;
        padding: 10px 12px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        font-size: 14px;
        margin-bottom: 18px;
    }

    button {
        width: 100%;
        padding: 12px;
        background: #2563eb;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
    }

    button:hover {
        background: #1e40af;
    }

    .note {
        font-size: 13px;
        color: #6b7280;
        margin-top: 14px;
        text-align: center;
    }
</style>

<div class="history-wrapper">

    <h2>Pet Medical History</h2>

    <form method="POST" action="{{ route('vet.pet.history.result') }}">
        @csrf

        <label>Pet Parent Mobile Number</label>
        <input type="text"
               name="mobile"
               placeholder="Enter registered mobile number"
               required>

        <button type="submit">View History</button>
    </form>

    <p class="note">
        Read-only access · No appointment creation · No contact details
    </p>

</div>

@endsection