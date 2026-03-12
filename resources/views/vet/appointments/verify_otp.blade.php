@extends('layouts.vet')

@section('content')
<div class="card">
    <h2>Verify Access</h2>

    {{-- DEV ONLY: Show OTP on screen --}}
    @if(isset($devOtp))
        <div style="background:#fff3cd; padding:10px; margin-bottom:15px; border-radius:4px;">
            <strong>DEV OTP:</strong> {{ $devOtp }}
        </div>
    @endif

    <form method="POST" action="{{ route('vet.appointments.verifyOtp') }}">
        @csrf

        <input type="hidden" name="mobile" value="{{ $mobile }}">

        <label>Enter OTP</label>
        <input name="otp" required>

        <button type="submit">Verify & Continue</button>
    </form>
</div>
@endsection
