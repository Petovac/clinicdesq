@extends(auth()->guard('vet')->check() ? 'layouts.vet' : 'clinic.layout')

@section('content')

<style>
.pp-container { max-width: 480px; margin: 0 auto; }
.pp-card {
    background: #fff; border-radius: 12px; padding: 32px;
    border: 1px solid #e5e7eb; box-shadow: 0 4px 15px rgba(0,0,0,0.04);
}
.pp-title { font-size: 22px; font-weight: 700; color: #1e293b; margin: 0 0 4px; text-align: center; }
.pp-subtitle { font-size: 13px; color: #64748b; margin: 0 0 24px; text-align: center; }
.pp-form-group { margin-bottom: 16px; }
.pp-form-group label {
    display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;
}
.pp-input {
    width: 100%; padding: 12px 14px; border: 1.5px solid #d1d5db; border-radius: 8px;
    font-size: 14px; background: #f8fafc; transition: all 0.2s;
}
.pp-input:focus {
    outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background: #fff;
}
.pp-btn {
    width: 100%; padding: 12px; border: none; border-radius: 8px; font-size: 14px;
    font-weight: 600; cursor: pointer; margin-top: 8px; transition: all 0.2s;
    background: #2563eb; color: #fff;
}
.pp-btn:hover { background: #1d4ed8; }
.back-link {
    font-size: 13px; color: #64748b; text-decoration: none;
    display: inline-flex; align-items: center; gap: 4px; margin-bottom: 16px;
}
.back-link:hover { color: #2563eb; }
</style>

<div class="pp-container">
    @if(isset($redirect) && $redirect === 'clinic')
        <a href="{{ route('clinic.appointments.create') }}" class="back-link">&larr; Back to Appointment Search</a>
    @endif

    <div class="pp-card">
        <h2 class="pp-title">Register Pet Parent</h2>
        <p class="pp-subtitle">Enter the pet parent's details to get started.</p>

        <form method="POST" action="{{ route('vet.petparent.store') }}">
            @csrf
            @if(isset($redirect))
                <input type="hidden" name="redirect" value="{{ $redirect }}">
            @endif

            <div class="pp-form-group">
                <label>Full Name <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name" required class="pp-input" placeholder="e.g. Rahul Sharma" autofocus>
            </div>

            <div class="pp-form-group">
                <label>Phone Number <span style="color:#dc2626;">*</span></label>
                <input type="text" name="phone" value="{{ $prefillPhone ?? '' }}" required class="pp-input" placeholder="e.g. 9876543210">
            </div>

            <button type="submit" class="pp-btn">Next: Add Pet</button>
        </form>
    </div>
</div>

@endsection
