@extends('layouts.vet')

@section('content')

<div class="v-form-card v-form-card--narrow">
    <div class="v-card">
        <h2 style="text-align:center;font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 22px;">
            Create Pet Parent
        </h2>

        <form method="POST" action="{{ route('vet.petparent.store') }}">
            @csrf

            <div class="v-form-group">
                <label>Name</label>
                <input type="text" name="name" required class="v-input">
            </div>

            <div class="v-form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="{{ session('prefill_mobile') }}" required class="v-input">
            </div>

            <button type="submit" class="v-btn v-btn--primary v-btn--block">Next: Add Pet</button>
        </form>
    </div>
</div>

@endsection
