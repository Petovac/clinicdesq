@extends('layouts.vet')

@section('content')

<div style="max-width:700px;margin:0 auto;">
    <a href="{{ url()->previous() }}" class="v-back">&larr; Back</a>

    <div class="v-card">
        <h2 style="font-size:22px;font-weight:700;color:var(--text-dark);margin:0 0 6px;">Admit to IPD</h2>
        <p style="font-size:13px;color:var(--text-muted);margin:0 0 20px;">Create a new in-patient admission record</p>

        @if(isset($appointment))
        <div style="background:var(--bg-soft);border:1px solid var(--border);border-radius:var(--radius-md);padding:14px 16px;margin-bottom:20px;font-size:13px;">
            <strong>From OPD Appointment #{{ $appointment->id }}</strong><br>
            Pet: <strong>{{ $appointment->pet->name ?? '—' }}</strong> &middot;
            {{ ucfirst($appointment->pet->species ?? '') }} &middot; {{ $appointment->pet->breed ?? '' }}<br>
            Parent: {{ $appointment->pet->petParent->name ?? '—' }} &middot; {{ $appointment->pet->petParent->phone ?? '' }}
        </div>
        @endif

        <form method="POST" action="{{ route('vet.ipd.store') }}">
            @csrf

            @if(isset($appointment))
                <input type="hidden" name="pet_id" value="{{ $appointment->pet_id }}">
                <input type="hidden" name="pet_parent_id" value="{{ $appointment->pet->pet_parent_id }}">
                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
            @else
                <div class="v-form-row">
                    <div class="v-form-group">
                        <label>Pet ID</label>
                        <input type="number" name="pet_id" required placeholder="Enter Pet ID" class="v-input">
                    </div>
                    <div class="v-form-group">
                        <label>Pet Parent ID</label>
                        <input type="number" name="pet_parent_id" required placeholder="Enter Pet Parent ID" class="v-input">
                    </div>
                </div>
            @endif

            <div class="v-form-group">
                <label>Admission Date & Time</label>
                <input type="datetime-local" name="admission_date" value="{{ now()->format('Y-m-d\TH:i') }}" required class="v-input">
            </div>

            <div class="v-form-group">
                <label>Reason for Admission *</label>
                <textarea name="admission_reason" required placeholder="Presenting complaint, reason for hospitalisation..." class="v-input">{{ isset($appointment) && $appointment->caseSheet ? $appointment->caseSheet->chief_complaint ?? '' : '' }}</textarea>
            </div>

            <div class="v-form-group">
                <label>Tentative Diagnosis</label>
                <textarea name="tentative_diagnosis" placeholder="Working diagnosis at time of admission..." class="v-input">{{ isset($appointment) && $appointment->caseSheet ? $appointment->caseSheet->tentative_diagnosis ?? '' : '' }}</textarea>
            </div>

            <div class="v-form-row">
                <div class="v-form-group">
                    <label>Cage Number</label>
                    <input type="text" name="cage_number" placeholder="e.g. C-12" class="v-input">
                </div>
                <div class="v-form-group">
                    <label>Ward</label>
                    <input type="text" name="ward" placeholder="e.g. ICU, General" class="v-input">
                </div>
            </div>

            @if ($errors->any())
                <div class="v-flash v-flash--error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <button type="submit" class="v-btn v-btn--primary">Admit Patient</button>
        </form>
    </div>
</div>

@endsection
