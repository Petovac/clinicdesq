@extends('layouts.vet')

@section('content')

<div style="
    max-width:1200px;
    margin:0 auto;
    padding:12px 0 32px;
    display:flex;
    flex-direction:column;
    align-items:center;
">

<div style="width:100%;max-width:1000px;">

    {{-- PAGE TITLE --}}
    <div style="margin-bottom:28px;">
        <h2 style="
            font-size:26px;
            font-weight:600;
            color:#111827;
            margin-bottom:6px;
        ">
            My Clinics
        </h2>
        <p style="font-size:14px;color:#6b7280;">
            Select a clinic to continue working
        </p>
    </div>

    {{-- ACTIVE CLINIC BANNER --}}
    @if($activeClinic)
        <div style="
            margin-bottom:28px;
            padding:14px 18px;
            background:#eef2ff;
            border:1px solid #c7d2fe;
            border-radius:10px;
            font-size:14px;
            display:flex;
            align-items:center;
            gap:10px;
        ">
            <span style="
                background:#2563eb;
                color:#ffffff;
                font-size:12px;
                padding:4px 10px;
                border-radius:999px;
                font-weight:500;
            ">
                Active Clinic
            </span>

            <div>
                <strong style="color:#111827;">
                    {{ $activeClinic->name }}
                </strong>
                <span style="color:#6b7280;">
                    ({{ $activeClinic->organisation?->name }})
                </span>
            </div>
        </div>
    @endif

    {{-- NO CLINIC ASSIGNED --}}
    @if($clinics->isEmpty())
        <div style="
            background:#ffffff;
            border:1px dashed #d1d5db;
            padding:48px;
            text-align:center;
            border-radius:12px;
        ">
            <h3 style="margin-bottom:10px;font-size:18px;">
                No Active Clinic Assignment
            </h3>
            <p style="color:#6b7280;font-size:14px;">
                You are currently not assigned to any clinic.<br>
                Once a clinic onboards you, it will appear here.
            </p>
        </div>
    @else

        {{-- CLINIC GRID --}}
        <div style="
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
            gap:24px;
            width:100%;
        ">
            @foreach($clinics as $clinic)

                @php
                    $isActive = session('active_clinic_id') == $clinic->id;
                @endphp

                <div style="
                    background:#ffffff;
                    border:2px solid {{ $isActive ? '#2563eb' : '#e5e7eb' }};
                    border-radius:14px;
                    padding:22px;
                    position:relative;
                    box-shadow: {{ $isActive ? '0 10px 25px rgba(37,99,235,0.15)' : '0 6px 18px rgba(0,0,0,0.06)' }};
                    transition: transform 0.15s ease, box-shadow 0.15s ease;
                ">

                    {{-- ACTIVE BADGE --}}
                    @if($isActive)
                        <span style="
                            position:absolute;
                            top:14px;
                            right:14px;
                            background:#2563eb;
                            color:#ffffff;
                            font-size:11px;
                            padding:4px 10px;
                            border-radius:999px;
                            font-weight:500;
                        ">
                            Active
                        </span>
                    @endif

                    <h3 style="
                        font-size:18px;
                        font-weight:600;
                        margin-bottom:6px;
                        color:#111827;
                    ">
                        {{ $clinic->name }}
                    </h3>

                    <p style="
                        font-size:13px;
                        color:#6b7280;
                        margin-bottom:18px;
                    ">
                        {{ $clinic->organisation?->name }}
                    </p>

                    <form method="POST" action="{{ route('vet.selectClinic', $clinic->id) }}">
                        @csrf
                        <button type="submit"
                            style="
                                padding:9px 16px;
                                background:{{ $isActive ? '#1e40af' : '#2563eb' }};
                                color:#ffffff;
                                border:none;
                                border-radius:8px;
                                font-size:14px;
                                cursor:pointer;
                            ">
                            {{ $isActive ? 'Enter Clinic' : 'Switch to Clinic' }}
                        </button>
                    </form>

                </div>

            @endforeach
        </div>

    @endif

    </div>

</div>

@endsection