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
.btn { padding:10px 18px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; border:none; text-decoration:none; }
.btn-primary { background:var(--primary); color:#fff; }
.btn-success { background:#16a34a; color:#fff; }
.btn-outline { background:#fff; color:#374151; border:1px solid #d1d5db; }
.form-actions { display:flex; gap:10px; margin-top:16px; }
.two-col { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
</style>

<div class="cert-form-header">
    <h2>Edit Certificate — {{ $certificate->certificate_number }}</h2>
    <a href="{{ route('vet.certificates.index', $pet) }}" class="btn btn-outline">← Back</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('vet.certificates.update', $certificate) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Certificate Title</label>
            <input type="text" name="title" value="{{ $certificate->title }}" required>
        </div>

        <div class="two-col">
            <div class="form-group">
                <label>Valid Until</label>
                <input type="date" name="valid_until" value="{{ $certificate->valid_until?->format('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label>Type</label>
                <input type="text" value="{{ ucfirst($certificate->certificate_type) }}" disabled>
            </div>
        </div>

        <hr style="border:none;border-top:1px solid #f3f4f6;margin:14px 0;">

        @php $content = $certificate->content ?? []; @endphp
        @foreach($fields as $field)
            @if($field['type'] === 'auto_vaccinations')
                <div class="form-group">
                    <label>{{ $field['label'] }}</label>
                    <div style="background:#f0fdf4;padding:8px;border-radius:6px;font-size:12px;color:#166534;">Auto-populated from pet's vaccination records.</div>
                </div>
            @elseif($field['type'] === 'textarea')
                <div class="form-group">
                    <label>{{ $field['label'] }}</label>
                    <textarea name="content[{{ $field['key'] }}]" rows="3">{{ $content[$field['key']] ?? $field['default'] ?? '' }}</textarea>
                </div>
            @elseif($field['type'] === 'select')
                <div class="form-group">
                    <label>{{ $field['label'] }}</label>
                    <select name="content[{{ $field['key'] }}]">
                        @foreach($field['options'] ?? [] as $opt)
                        <option value="{{ $opt }}" {{ ($content[$field['key']] ?? $field['default'] ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
            @elseif($field['type'] === 'date')
                <div class="form-group">
                    <label>{{ $field['label'] }}</label>
                    <input type="date" name="content[{{ $field['key'] }}]" value="{{ $content[$field['key']] ?? $field['default'] ?? '' }}">
                </div>
            @else
                <div class="form-group">
                    <label>{{ $field['label'] }}</label>
                    <input type="text" name="content[{{ $field['key'] }}]" value="{{ $content[$field['key']] ?? $field['default'] ?? '' }}">
                </div>
            @endif
        @endforeach

        <div class="form-actions">
            <button type="submit" name="action" value="draft" class="btn btn-outline">Save Draft</button>
            <button type="submit" name="action" value="issue" class="btn btn-success">Issue Certificate</button>
        </div>
    </form>
</div>
@endsection
