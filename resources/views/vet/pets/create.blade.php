@php
    $isClinic = request()->is('clinic/*') || session('from_clinic');
@endphp

@extends($isClinic ? 'clinic.layout' : 'layouts.vet')

@section('content')

<style>
.pet-form-container { max-width: 520px; margin: 0 auto; }
.pet-form-card {
    background: #fff; border-radius: 12px; padding: 32px;
    border: 1px solid #e5e7eb; box-shadow: 0 4px 15px rgba(0,0,0,0.04);
}
.pet-form-title { font-size: 22px; font-weight: 700; color: #1e293b; margin: 0 0 4px; text-align: center; }
.pet-form-subtitle { font-size: 13px; color: #64748b; margin: 0 0 24px; text-align: center; }
.pf-group { margin-bottom: 16px; }
.pf-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
.pf-input, .pf-select {
    width: 100%; padding: 12px 14px; border: 1.5px solid #d1d5db; border-radius: 8px;
    font-size: 14px; background: #f8fafc; transition: all 0.2s;
}
.pf-input:focus, .pf-select:focus {
    outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background: #fff;
}
.pf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.pf-hint { font-size: 11px; color: #9ca3af; margin-top: 4px; }
.pf-btn {
    width: 100%; padding: 12px; border: none; border-radius: 8px; font-size: 14px;
    font-weight: 600; cursor: pointer; margin-top: 8px; transition: all 0.2s;
    background: #2563eb; color: #fff;
}
.pf-btn:hover { background: #1d4ed8; }
.back-link {
    font-size: 13px; color: #64748b; text-decoration: none;
    display: inline-flex; align-items: center; gap: 4px; margin-bottom: 16px;
}
.back-link:hover { color: #2563eb; }
</style>

<div class="pet-form-container">
    @if($isClinic)
        <a href="{{ route('clinic.appointments.create') }}" class="back-link">&larr; Back to Appointment Search</a>
    @endif

    <div class="pet-form-card">
        <h2 class="pet-form-title">Add Pet</h2>
        <p class="pet-form-subtitle">for {{ $parent->name }} ({{ $parent->phone }})</p>

        <form method="POST" action="{{ $isClinic ? route('clinic.pets.store', $parent->id) : route('vet.pets.store', $parent->id) }}">
            @csrf

            <div class="pf-group">
                <label>Pet Name <span style="color:#dc2626;">*</span></label>
                <input name="name" required class="pf-input" placeholder="e.g. Bruno">
            </div>

            <div class="pf-group">
                <label>Species <span style="color:#dc2626;">*</span></label>
                <select name="species" required class="pf-select">
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

            <div class="pf-group">
                <label>Breed</label>
                <input name="breed" class="pf-input" placeholder="e.g. Labrador Retriever">
            </div>

            <div class="pf-group">
                <label>Age <span style="color:#dc2626;">*</span></label>
                <div class="pf-row">
                    <input type="number" name="age" min="0" placeholder="Years" required class="pf-input">
                    <input type="number" name="age_months" min="0" max="11" placeholder="Months" class="pf-input">
                </div>
                <div class="pf-hint">Example: 3 years 6 months</div>
            </div>

            <div class="pf-group">
                <label>Gender</label>
                <select name="gender" class="pf-select">
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            @if($isClinic)
                <input type="hidden" name="redirect_to" value="{{ route('clinic.appointments.create') }}">
            @else
                <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
            @endif

            <button type="submit" class="pf-btn">Add Pet & Continue</button>
        </form>
    </div>
</div>

@endsection
