@extends('layouts.vet')

@section('content')

<div style="max-width:900px;margin:0 auto;">
    <div class="v-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2 style="font-size:22px;font-weight:600;color:var(--text-dark);margin:0;">My Profile</h2>
            <a href="{{ route('vet.profile.edit') }}" class="v-btn v-btn--primary v-btn--sm">Edit Profile</a>
        </div>

        <hr class="v-divider" style="margin-top:0;">

        <dl>
            <div class="v-detail-row"><dt>Name</dt><dd>{{ $vet->name }}</dd></div>
            <div class="v-detail-row"><dt>Email</dt><dd>{{ $vet->email ?? '-' }}</dd></div>
            <div class="v-detail-row"><dt>Phone</dt><dd>{{ $vet->phone ?? '-' }}</dd></div>
            <div class="v-detail-row"><dt>Registration Number</dt><dd>{{ $vet->registration_number ?? '-' }}</dd></div>
            <div class="v-detail-row"><dt>Specialization</dt><dd>{{ $vet->specialization ?? '-' }}</dd></div>
            <div class="v-detail-row"><dt>Degree</dt><dd>{{ $vet->degree ?? '-' }}</dd></div>
            <div class="v-detail-row"><dt>Skills</dt><dd>{{ $vet->skills ?? '-' }}</dd></div>
            <div class="v-detail-row"><dt>Certifications</dt><dd>{{ $vet->certifications ?? '-' }}</dd></div>
            <div class="v-detail-row"><dt>Experience</dt><dd>{{ $vet->experience ?? '-' }}</dd></div>
        </dl>
    </div>
</div>

@endsection
