@extends('clinic.layout')

@section('content')
<div class="container-fluid" style="max-width:700px;">

    <a href="{{ route('clinic.ipd.index') }}" class="text-decoration-none text-muted small">&larr; Back to IPD List</a>

    <div class="card mt-2">
        <div class="card-body">
            <h5 class="fw-bold mb-1">Admit to IPD</h5>
            <p class="text-muted small mb-3">Create a new in-patient admission</p>

            @if ($errors->any())
                <div class="alert alert-danger small">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('clinic.ipd.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Clinic</label>
                    <select name="clinic_id" class="form-select" required>
                        @foreach(auth()->user()->clinics as $clinic)
                            <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                        @endforeach
                        @if(auth()->user()->clinic_id && !auth()->user()->clinics->contains('id', auth()->user()->clinic_id))
                            @php $primaryClinic = \App\Models\Clinic::find(auth()->user()->clinic_id); @endphp
                            @if($primaryClinic)
                                <option value="{{ $primaryClinic->id }}">{{ $primaryClinic->name }}</option>
                            @endif
                        @endif
                    </select>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Pet ID</label>
                        <input type="number" name="pet_id" class="form-control" required placeholder="Enter Pet ID" value="{{ old('pet_id') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Pet Parent ID</label>
                        <input type="number" name="pet_parent_id" class="form-control" required placeholder="Enter Parent ID" value="{{ old('pet_parent_id') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Admission Date & Time</label>
                    <input type="datetime-local" name="admission_date" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Reason for Admission *</label>
                    <textarea name="admission_reason" class="form-control" rows="3" required placeholder="Presenting complaint, reason for hospitalisation...">{{ old('admission_reason') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tentative Diagnosis</label>
                    <textarea name="tentative_diagnosis" class="form-control" rows="2" placeholder="Working diagnosis...">{{ old('tentative_diagnosis') }}</textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Cage Number</label>
                        <input type="text" name="cage_number" class="form-control" placeholder="e.g. C-12" value="{{ old('cage_number') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Ward</label>
                        <input type="text" name="ward" class="form-control" placeholder="e.g. ICU, General" value="{{ old('ward') }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Admit Patient</button>
            </form>
        </div>
    </div>
</div>
@endsection
