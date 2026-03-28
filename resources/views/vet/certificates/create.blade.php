@extends('layouts.vet')

@section('content')
<style>
.cert-form-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
.cert-form-header h2 { font-size:20px; font-weight:700; margin:0; }
.card { background:#fff; border-radius:10px; padding:20px; border:1px solid var(--border); margin-bottom:14px; max-width:720px; }
.form-group { margin-bottom:14px; }
.form-group label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:3px; }
.form-group input, .form-group select, .form-group textarea { width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; font-family:inherit; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 2px rgba(37,99,235,0.12); }
.form-group textarea { min-height:70px; resize:vertical; }
.form-hint { font-size:10px; color:#9ca3af; margin-top:2px; }
.type-tabs { display:flex; gap:6px; margin-bottom:16px; flex-wrap:wrap; }
.type-tab { padding:8px 14px; border-radius:6px; text-decoration:none; font-size:12px; font-weight:600; border:1px solid #e5e7eb; color:#374151; background:#fff; }
.type-tab:hover { background:#f3f4f6; }
.type-tab.active { background:var(--primary); color:#fff; border-color:var(--primary); }
.btn { padding:10px 18px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; border:none; text-decoration:none; }
.btn-primary { background:var(--primary); color:#fff; }
.btn-success { background:#16a34a; color:#fff; }
.btn-outline { background:#fff; color:#374151; border:1px solid #d1d5db; }
.form-actions { display:flex; gap:10px; margin-top:16px; }
.pet-bar { background:var(--primary-soft); border:1px solid var(--primary-border); border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:13px; max-width:720px; }
.pet-bar strong { color:var(--text-dark); }
.two-col { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
</style>

<div class="cert-form-header">
    <h2>Issue Certificate — {{ $pet->name }}</h2>
    <a href="{{ route('vet.certificates.index', $pet) }}" class="btn btn-outline">← Back</a>
</div>

<div class="pet-bar">
    <strong>{{ $pet->name }}</strong> · {{ ucfirst($pet->species) }} · {{ $pet->breed }} · {{ $pet->current_age ?? $pet->age.'y' }} · {{ ucfirst($pet->gender) }}
    @if($pet->petParent) · Owner: {{ $pet->petParent->name }} ({{ $pet->petParent->phone }}) @endif
</div>

{{-- Type selector tabs --}}
<div class="type-tabs">
    @foreach($types as $t)
    <a href="{{ route('vet.certificates.create', ['pet' => $pet->id, 'type' => $t->type, 'appointment_id' => $appointmentId]) }}"
       class="type-tab {{ $type === $t->type ? 'active' : '' }}">
        {{ $t->name }}
    </a>
    @endforeach
</div>

<div class="card">
    <form method="POST" action="{{ route('vet.certificates.store') }}">
        @csrf
        <input type="hidden" name="pet_id" value="{{ $pet->id }}">
        <input type="hidden" name="appointment_id" value="{{ $appointmentId }}">
        <input type="hidden" name="template_id" value="{{ $template->id }}">
        <input type="hidden" name="certificate_type" value="{{ $type }}">

        <div class="form-group">
            <label>Certificate Title</label>
            <input type="text" name="title" value="{{ $template->name }}" required>
        </div>

        <div class="two-col">
            <div class="form-group">
                <label>Valid Until (optional)</label>
                <input type="date" name="valid_until">
            </div>
            <div class="form-group">
                <label>Issuing Vet</label>
                <input type="text" value="{{ str_starts_with($vet->name, 'Dr') ? $vet->name : 'Dr. '.$vet->name }}{{ $vet->registration_number ? ' (Reg: '.$vet->registration_number.')' : '' }}" disabled>
            </div>
        </div>

        <hr style="border:none;border-top:1px solid #f3f4f6;margin:14px 0;">

        {{-- Dynamic template fields --}}
        @foreach($fields as $field)
            @if($field['type'] === 'auto_vaccinations')
                {{-- Show vaccination records (read-only) --}}
                <div class="form-group">
                    <label>{{ $field['label'] }}</label>
                    @if(isset($pet) && $pet->vaccinations && $pet->vaccinations->count())
                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:10px;font-size:12px;">
                        <table style="width:100%;border-collapse:collapse;">
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <th style="text-align:left;padding:3px 6px;font-size:10px;color:#6b7280;">Vaccine</th>
                                <th style="text-align:left;padding:3px 6px;font-size:10px;color:#6b7280;">Brand</th>
                                <th style="text-align:left;padding:3px 6px;font-size:10px;color:#6b7280;">Dose</th>
                                <th style="text-align:left;padding:3px 6px;font-size:10px;color:#6b7280;">Date</th>
                                <th style="text-align:left;padding:3px 6px;font-size:10px;color:#6b7280;">Next Due</th>
                            </tr>
                            @foreach($pet->vaccinations as $v)
                            <tr>
                                <td style="padding:3px 6px;">{{ $v->vaccine_name }}</td>
                                <td style="padding:3px 6px;">{{ $v->brand_name ?? '—' }}</td>
                                <td style="padding:3px 6px;">{{ $v->dose_number }}</td>
                                <td style="padding:3px 6px;">{{ $v->administered_date->format('d/m/Y') }}</td>
                                <td style="padding:3px 6px;">{{ $v->next_due_date?->format('d/m/Y') ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="form-hint">This table will be auto-included in the certificate PDF.</div>
                    @else
                    <div style="background:#fef3c7;padding:8px;border-radius:6px;font-size:12px;color:#92400e;">No vaccination records found for this pet. Add vaccinations first.</div>
                    @endif
                </div>
            @elseif($field['type'] === 'textarea')
                <div class="form-group">
                    <label>{{ $field['label'] }}</label>
                    <textarea name="content[{{ $field['key'] }}]" rows="3">{{ $field['default'] ?? '' }}</textarea>
                </div>
            @elseif($field['type'] === 'select')
                <div class="form-group">
                    <label>{{ $field['label'] }}</label>
                    <select name="content[{{ $field['key'] }}]">
                        @foreach($field['options'] ?? [] as $opt)
                        <option value="{{ $opt }}" {{ ($field['default'] ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
            @elseif($field['type'] === 'date')
                <div class="form-group">
                    <label>{{ $field['label'] }}</label>
                    <input type="date" name="content[{{ $field['key'] }}]" value="{{ $field['default'] ?? '' }}">
                </div>
            @else
                <div class="form-group">
                    <label>{{ $field['label'] }}</label>
                    <input type="text" name="content[{{ $field['key'] }}]" value="{{ $field['default'] ?? '' }}">
                </div>
            @endif
        @endforeach

        <div class="form-actions">
            <button type="submit" name="action" value="draft" class="btn btn-outline">Save as Draft</button>
            <button type="submit" name="action" value="issue" class="btn btn-success">Issue Certificate</button>
        </div>
    </form>
</div>
@endsection
