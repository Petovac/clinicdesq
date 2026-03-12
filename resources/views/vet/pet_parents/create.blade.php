@extends('layouts.vet')

@section('content')
<div style="
    max-width: 420px;
    margin: 40px auto;
    background: #ffffff;
    border-radius: 12px;
    padding: 28px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
">

    <h2 style="
        margin-bottom: 22px;
        font-size: 22px;
        font-weight: 600;
        color: #1f2937;
        text-align: center;
    ">
        Create Pet Parent
    </h2>

    <form method="POST" action="{{ route('vet.petparent.store') }}">
        @csrf

        <div style="margin-bottom: 18px;">
            <label style="
                display: block;
                margin-bottom: 6px;
                font-size: 14px;
                font-weight: 500;
                color: #374151;
            ">
                Name
            </label>
            <input
                type="text"
                name="name"
                required
                style="
                    width: 100%;
                    padding: 11px 12px;
                    border-radius: 8px;
                    border: 1px solid #d1d5db;
                    font-size: 14px;
                    outline: none;
                "
            >
        </div>

        <div style="margin-bottom: 22px;">
            <label style="
                display: block;
                margin-bottom: 6px;
                font-size: 14px;
                font-weight: 500;
                color: #374151;
            ">
                Phone
            </label>
            <input
                type="text"
                name="phone"
                value="{{ session('prefill_mobile') }}"
                required
                style="
                    width: 100%;
                    padding: 11px 12px;
                    border-radius: 8px;
                    border: 1px solid #d1d5db;
                    font-size: 14px;
                    outline: none;
                "
            >
        </div>

        <button
            type="submit"
            style="
                width: 100%;
                padding: 12px;
                background: #2563eb;
                color: #ffffff;
                border: none;
                border-radius: 8px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
            "
        >
            Next: Add Pet
        </button>
    </form>
</div>
@endsection