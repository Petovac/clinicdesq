@extends('layouts.vet')

@section('content')

<div style="max-width:900px;margin:0 auto;">
    <div class="v-card">
        <h2 style="font-size:22px;font-weight:600;color:var(--text-dark);margin:0 0 20px;">Edit Vet Profile</h2>

        <form method="POST" action="{{ route('vet.profile.update') }}">
            @csrf

            <div class="v-form-row">
                <div class="v-form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name', $vet->name) }}" required class="v-input">
                </div>
                <div class="v-form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $vet->email) }}" class="v-input">
                </div>
            </div>

            <div class="v-form-row">
                <div class="v-form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $vet->phone) }}" class="v-input">
                </div>
                <div class="v-form-group">
                    <label>Registration Number</label>
                    <input type="text" name="registration_number" value="{{ old('registration_number', $vet->registration_number) }}" class="v-input">
                </div>
            </div>

            <div class="v-form-row">
                <div class="v-form-group">
                    <label>Specialisation</label>
                    <input type="text" name="specialisation" value="{{ old('specialization', $vet->specialization) }}" class="v-input">
                </div>
                <div class="v-form-group">
                    <label>Degree</label>
                    <input type="text" name="degree" value="{{ old('degree', $vet->degree) }}" class="v-input">
                </div>
            </div>

            <div class="v-form-group">
                <label>Skills</label>
                <textarea name="skills" class="v-input">{{ old('skills', $vet->skills) }}</textarea>
            </div>

            <div class="v-form-group">
                <label>Certifications</label>
                <textarea name="certifications" class="v-input">{{ old('certifications', $vet->certifications) }}</textarea>
            </div>

            <div class="v-form-group">
                <label>Experience</label>
                <textarea name="experience" class="v-input">{{ old('experience', $vet->experience) }}</textarea>
            </div>

            <div style="text-align:right;">
                <button type="submit" class="v-btn v-btn--primary">Save Profile</button>
            </div>
        </form>
    </div>
</div>

@endsection
