@extends('layouts.vet')

@section('content')

<div class="v-form-card">
    <div class="v-card">
        <h2 style="text-align:center;font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 20px;">
            Add Pet for {{ $parent->name }}
        </h2>

        <form method="POST" action="{{ route('vet.pets.store', $parent->id) }}">
            @csrf

            <div class="v-form-group">
                <label>Pet Name</label>
                <input name="name" required class="v-input">
            </div>

            <div class="v-form-group">
                <label>Species</label>
                <select name="species" required class="v-input">
                    <option value="">Select Species</option>
                    <option value="dog">Dog</option>
                    <option value="cat">Cat</option>
                    <option value="rabbit">Rabbit</option>
                    <option value="bird">Bird</option>
                    <option value="horse">Horse</option>
                    <option value="cow">Cow</option>
                    <option value="goat">Goat</option>
                </select>
            </div>

            <div class="v-form-group">
                <label>Breed</label>
                <input name="breed" class="v-input">
            </div>

            <div class="v-form-group">
                <label>Age</label>
                <div class="v-form-row">
                    <input type="number" name="age" min="0" placeholder="Years" required class="v-input">
                    <input type="number" name="age_months" min="0" max="11" placeholder="Months" class="v-input">
                </div>
                <p class="v-form-hint">Example: 3 years 6 months</p>
            </div>

            <div class="v-form-group">
                <label>Gender</label>
                <input name="gender" class="v-input">
            </div>

            <button type="submit" class="v-btn v-btn--primary v-btn--block">Add Pet</button>

            <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
        </form>
    </div>
</div>

@endsection
