@extends('layouts.vet')

@section('content')

<div class="casesheet-wrapper">

    {{-- LEFT COLUMN --}}
    <div class="casesheet-left">

        {{-- CASE SHEET --}}
        @include('vet.partials.case-sheet', [
            'appointment' => $appointment
        ])

        {{-- DIAGNOSTICS --}}
        @include('vet.partials.diagnostics', [
            'appointment' => $appointment
        ])

        {{-- PRESCRIPTION --}}
        @include('vet.partials.prescription', [
            'appointment' => $appointment
        ])

    </div>

    {{-- RIGHT COLUMN --}}
    <div class="casesheet-right">
        @include('vet.partials.ai-clinical-panel', [
            'appointment' => $appointment
        ])
    </div>

</div>

@endsection