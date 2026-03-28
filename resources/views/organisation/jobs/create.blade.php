@extends('organisation.layout')
@section('content')
<style>
.page-hdr { display:flex;align-items:center;gap:12px;margin-bottom:20px; }
.page-hdr h2 { font-size:22px;font-weight:700;margin:0; }
.card { background:#fff;border-radius:10px;padding:24px;border:1px solid #e5e7eb;max-width:700px; }
.form-group { margin-bottom:16px; }
.form-group label { display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:4px; }
.form-group input,.form-group select,.form-group textarea { width:100%;padding:9px 11px;border:1px solid #d1d5db;border-radius:6px;font-size:14px; }
.form-group input:focus,.form-group select:focus,.form-group textarea:focus { outline:none;border-color:#4f46e5;box-shadow:0 0 0 2px rgba(79,70,229,0.12); }
.form-group textarea { min-height:80px;font-family:inherit; }
.form-row { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
.form-row3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px; }
.btn { padding:10px 18px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;border:none; }
.btn-primary { background:#4f46e5;color:#fff; }
.btn-secondary { background:#e5e7eb;color:#374151;text-decoration:none; }
.hint { font-size:11px;color:#9ca3af;margin-top:2px; }
</style>

<div class="page-hdr">
    <a href="{{ route('organisation.jobs.index') }}" style="color:#6b7280;text-decoration:none;font-size:18px;">←</a>
    <h2>Post a New Job</h2>
</div>

<div class="card">
    <form method="POST" action="{{ route('organisation.jobs.store') }}">
        @csrf

        <div class="form-group">
            <label>Job Title *</label>
            <input name="title" required placeholder="e.g. Veterinarian, Senior Vet Surgeon" value="{{ old('title') }}">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Role Type</label>
                <select name="role_type">
                    <option value="vet">Veterinarian</option>
                    <option value="vet_surgeon">Veterinary Surgeon</option>
                    <option value="vet_specialist">Vet Specialist</option>
                    <option value="vet_intern">Vet Intern</option>
                </select>
            </div>
            <div class="form-group">
                <label>Employment Type</label>
                <select name="employment_type">
                    <option value="full_time">Full Time</option>
                    <option value="part_time">Part Time</option>
                    <option value="locum">Locum / Relief</option>
                    <option value="contract">Contract</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Specific Clinic (optional)</label>
            <select name="clinic_id">
                <option value="">Any clinic / Organisation-level</option>
                @foreach($clinics as $c)
                <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->city ?? '' }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4" placeholder="Describe the role, responsibilities, work culture...">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label>Requirements</label>
            <textarea name="requirements" rows="3" placeholder="Degree required, skills, certifications...">{{ old('requirements') }}</textarea>
        </div>

        <div class="form-row3">
            <div class="form-group">
                <label>Specialization</label>
                <input name="specialization_required" placeholder="e.g. Surgery, Dermatology">
            </div>
            <div class="form-group">
                <label>Min Experience (years)</label>
                <input name="min_experience_years" type="number" min="0" placeholder="0">
            </div>
            <div class="form-group">
                <label>Closes On</label>
                <input name="closes_at" type="date">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Salary Min (₹/month)</label>
                <input name="salary_min" type="number" placeholder="e.g. 30000">
            </div>
            <div class="form-group">
                <label>Salary Max (₹/month)</label>
                <input name="salary_max" type="number" placeholder="e.g. 60000">
            </div>
        </div>

        @php $locStates = config('locations.states', []); $locCities = config('locations.cities', []); @endphp
        <div class="form-row">
            <div class="form-group">
                <label>State</label>
                <select name="state" id="jc-state">
                    <option value="">Select</option>
                    @foreach($locStates as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
                </select>
            </div>
            <div class="form-group">
                <label>City</label>
                <select name="city" id="jc-city">
                    <option value="">Select</option>
                </select>
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:20px;">
            <button type="submit" name="publish" value="1" class="btn btn-primary">Publish Now</button>
            <button type="submit" class="btn btn-secondary">Save as Draft</button>
            <a href="{{ route('organisation.jobs.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
(function(){
    var cm=@json($locCities),ss=document.getElementById('jc-state'),cs=document.getElementById('jc-city');
    ss.addEventListener('change',function(){cs.innerHTML='<option value="">Select</option>';(cm[this.value]||[]).forEach(function(c){var o=document.createElement('option');o.value=c;o.textContent=c;cs.appendChild(o);});});
})();
</script>
@endsection
