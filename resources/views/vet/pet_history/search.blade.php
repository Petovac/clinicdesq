@extends('layouts.vet')

@section('content')

<div class="v-form-card">
    <div class="v-card">
        <h2 style="text-align:center;font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 18px;">
            Pet Medical History
        </h2>

        <form method="POST" action="{{ route('vet.pet.history.result') }}">
            @csrf

            <div class="v-form-group">
                <label>Pet Parent Mobile Number</label>
                <input type="text" name="mobile" placeholder="Enter registered mobile number" required class="v-input">
            </div>

            <button type="submit" class="v-btn v-btn--primary v-btn--block">View History</button>
        </form>

        <p style="font-size:13px;color:var(--text-muted);margin-top:14px;text-align:center;">
            Read-only access &middot; No appointment creation &middot; No contact details
        </p>
    </div>
</div>

@endsection
