@extends('layouts.vet')

@section('content')

<style>
    .profile-card {
        max-width: 900px;
        margin: 30px auto;
        background: #ffffff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        font-family: Arial, sans-serif;
    }

    .profile-card h2 {
        margin-bottom: 25px;
        color: #2c3e50;
        font-size: 22px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        color: #34495e;
        font-size: 14px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #dcdcdc;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #3498db;
        outline: none;
    }

    textarea {
        min-height: 90px;
        resize: vertical;
    }

    .form-actions {
        margin-top: 25px;
        text-align: right;
    }

    .btn-save {
        background-color: #3498db;
        color: #fff;
        border: none;
        padding: 10px 22px;
        font-size: 14px;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-save:hover {
        background-color: #2980b9;
    }
</style>

<div class="profile-card">
    <h2>Edit Vet Profile</h2>

    <form method="POST" action="{{ route('vet.profile.update') }}">
        @csrf

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name', $vet->name) }}" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $vet->email) }}">
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $vet->phone) }}">
        </div>

        <div class="form-group">
            <label>Registration Number</label>
            <input type="text" name="registration_number" value="{{ old('registration_number', $vet->registration_number) }}">
        </div>

        <div class="form-group">
            <label>Specialisation</label>
            <input type="text" name="specialisation" value="{{ old('specialization', $vet->specialization) }}">
        </div>

        <div class="form-group">
            <label>Degree</label>
            <input type="text" name="degree" value="{{ old('degree', $vet->degree) }}">
        </div>

        <div class="form-group">
            <label>Skills</label>
            <textarea name="skills">{{ old('skills', $vet->skills) }}</textarea>
        </div>

        <div class="form-group">
            <label>Certifications</label>
            <textarea name="certifications">{{ old('certifications', $vet->certifications) }}</textarea>
        </div>

        <div class="form-group">
            <label>Experience</label>
            <textarea name="experience">{{ old('experience', $vet->experience) }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">Save Profile</button>
        </div>
    </form>
</div>

@endsection