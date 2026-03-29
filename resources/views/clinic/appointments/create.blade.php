@extends('clinic.layout')

@section('content')

<style>
.search-container { max-width: 520px; }
.search-card {
    background: #fff; border-radius: 12px; padding: 32px;
    border: 1px solid #e5e7eb; box-shadow: 0 4px 15px rgba(0,0,0,0.04);
}
.search-title {
    font-size: 22px; font-weight: 700; color: #1e293b; margin: 0 0 4px;
}
.search-subtitle {
    font-size: 13px; color: #64748b; margin: 0 0 24px;
}
.search-label {
    display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;
}
.search-input {
    width: 100%; padding: 12px 14px; border: 1.5px solid #d1d5db; border-radius: 8px;
    font-size: 15px; background: #f8fafc; transition: all 0.2s;
}
.search-input:focus {
    outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background: #fff;
}
.search-input::placeholder { color: #9ca3af; }
.btn-search {
    width: 100%; padding: 12px; border: none; border-radius: 8px; font-size: 14px;
    font-weight: 600; cursor: pointer; margin-top: 16px; transition: all 0.2s;
    background: #2563eb; color: #fff;
}
.btn-search:hover { background: #1d4ed8; transform: translateY(-1px); }

/* Not Found State */
.not-found-box {
    margin-top: 20px; padding: 20px; border-radius: 10px;
    background: #fef2f2; border: 1px solid #fecaca;
}
.not-found-text {
    font-size: 14px; font-weight: 600; color: #991b1b; margin: 0 0 4px;
}
.not-found-hint {
    font-size: 13px; color: #b91c1c; margin: 0 0 16px;
}
.btn-register {
    display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px;
    border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none;
    background: #16a34a; color: #fff; transition: all 0.2s; border: none; cursor: pointer;
}
.btn-register:hover { background: #15803d; color: #fff; transform: translateY(-1px); }

.back-link {
    font-size: 13px; color: #64748b; text-decoration: none;
    display: inline-flex; align-items: center; gap: 4px; margin-bottom: 16px;
}
.back-link:hover { color: #2563eb; }

.phone-hint {
    font-size: 11px; color: #9ca3af; margin-top: 4px;
}
</style>

<div class="search-container">
    <a href="{{ route('clinic.appointments.index') }}" class="back-link">&larr; Back to Appointments</a>

    <div class="search-card">
        <h2 class="search-title">New Appointment</h2>
        <p class="search-subtitle">Search for an existing pet parent by their phone number to get started.</p>

        <form method="POST" action="{{ route('clinic.appointments.search') }}">
            @csrf

            <label class="search-label">Phone Number</label>
            <input type="text" name="mobile" value="{{ $mobile ?? '' }}" required
                   class="search-input" placeholder="e.g. 9876543210" autofocus>
            <div class="phone-hint">Enter the pet parent's registered mobile number</div>

            <button type="submit" class="btn-search">
                Search Pet Parent
            </button>
        </form>

        @if(isset($notFound))
        <div class="not-found-box">
            <p class="not-found-text">No pet parent found with this number</p>
            <p class="not-found-hint">The number <strong>{{ $mobile }}</strong> is not registered yet. You can register them as a new pet parent.</p>

            <a href="{{ route('clinic.petparent.create') }}?phone={{ $mobile }}&redirect=clinic" class="btn-register">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Register New Pet Parent
            </a>
        </div>
        @endif
    </div>
</div>

@endsection
