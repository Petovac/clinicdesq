@extends('organisation.layout')

@section('content')

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.page-header h2 {
    font-size: 22px;
    font-weight: 600;
    margin: 0;
    color: #111827;
}
.btn-primary {
    background: #2563eb;
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    display: inline-block;
}
.btn-primary:hover { background: #1d4ed8; }
.labs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
}
.lab-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.lab-card h3 {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 10px 0;
}
.lab-meta {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.7;
}
.lab-meta span {
    display: block;
}
.lab-card-footer {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.test-count {
    font-size: 12px;
    font-weight: 500;
    color: #2563eb;
    background: #eff6ff;
    padding: 2px 10px;
    border-radius: 12px;
}
.edit-link {
    color: #2563eb;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
}
.edit-link:hover { text-decoration: underline; }
.empty-state {
    text-align: center;
    padding: 40px 0;
    color: #6b7280;
    font-size: 14px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
}
</style>

<div class="page-header">
    <h2>External Labs (Tie-ups)</h2>
    <a href="{{ route('organisation.labs.create') }}" class="btn-primary">+ Onboard New Lab</a>
</div>

@if(session('success'))
    <div style="background:#dcfce7; color:#166534; padding:10px 16px; border-radius:8px; margin-bottom:16px; font-size:13px;">
        {{ session('success') }}
    </div>
@endif

@if($tiedUpLabs->count())
    <div class="labs-grid">
        @foreach($tiedUpLabs as $lab)
            <div class="lab-card">
                <h3>{{ $lab->name }}</h3>
                <div class="lab-meta">
                    @if($lab->city) <span>{{ $lab->city }}</span> @endif
                    @if($lab->phone) <span>{{ $lab->phone }}</span> @endif
                    @if($lab->email) <span>{{ $lab->email }}</span> @endif
                </div>
                <div class="lab-card-footer">
                    <span class="test-count">{{ $lab->test_offerings_count ?? $lab->testOfferings->count() ?? 0 }} tests</span>
                    <a href="{{ route('organisation.labs.edit', $lab) }}" class="edit-link">Edit</a>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">No external labs onboarded yet.</div>
@endif

@endsection
