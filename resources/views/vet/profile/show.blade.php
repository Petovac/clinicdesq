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

    .profile-row {
        display: flex;
        margin-bottom: 14px;
        font-size: 14px;
    }

    .profile-row strong {
        width: 200px;
        color: #34495e;
        font-weight: 600;
    }

    .profile-row span {
        color: #555;
    }

    .profile-actions {
        margin-top: 25px;
        text-align: right;
    }

    .btn-edit {
        display: inline-block;
        background-color: #3498db;
        color: #fff;
        text-decoration: none;
        padding: 10px 22px;
        border-radius: 6px;
        font-size: 14px;
    }

    .btn-edit:hover {
        background-color: #2980b9;
    }
</style>

<div class="profile-card">
    <h2>My Profile</h2>

    <div class="profile-row">
        <strong>Name</strong>
        <span>{{ $vet->name }}</span>
    </div>

    <div class="profile-row">
        <strong>Email</strong>
        <span>{{ $vet->email ?? '-' }}</span>
    </div>

    <div class="profile-row">
        <strong>Phone</strong>
        <span>{{ $vet->phone ?? '-' }}</span>
    </div>

    <div class="profile-row">
        <strong>Registration Number</strong>
        <span>{{ $vet->registration_number ?? '-' }}</span>
    </div>

    <div class="profile-row">
        <strong>Specialization</strong>
        <span>{{ $vet->specialization ?? '-' }}</span>
    </div>

    <div class="profile-row">
        <strong>Degree</strong>
        <span>{{ $vet->degree ?? '-' }}</span>
    </div>

    <div class="profile-row">
        <strong>Skills</strong>
        <span>{{ $vet->skills ?? '-' }}</span>
    </div>

    <div class="profile-row">
        <strong>Certifications</strong>
        <span>{{ $vet->certifications ?? '-' }}</span>
    </div>

    <div class="profile-row">
        <strong>Experience</strong>
        <span>{{ $vet->experience ?? '-' }}</span>
    </div>

    <div class="profile-actions">
        <a href="{{ route('vet.profile.edit') }}" class="btn-edit">
            Edit Profile
        </a>
    </div>
</div>

@endsection