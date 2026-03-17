@extends('layouts.vet')

@section('content')

<div class="v-form-card v-form-card--narrow">
    <div class="v-card">
        <h2 style="text-align:center;font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 20px;">
            Verify Access
        </h2>

        @if(isset($devOtp))
            <div class="v-flash" style="background:var(--warning-soft);color:#92400e;border:1px solid var(--warning-border);">
                <strong>DEV OTP:</strong> {{ $devOtp }}
            </div>
        @endif

        <form method="POST" action="{{ route('vet.appointments.verifyOtp') }}">
            @csrf
            <input type="hidden" name="mobile" value="{{ $mobile }}">

            <div class="v-form-group">
                <label>Enter OTP</label>
                <input name="otp" required class="v-input">
            </div>

            <button type="submit" class="v-btn v-btn--primary v-btn--block">Verify & Continue</button>
        </form>
    </div>
</div>

@endsection
