@extends('clinic.layout')

@section('content')

<style>
.sel-container { max-width: 640px; }
.back-link {
    font-size: 13px; color: #64748b; text-decoration: none;
    display: inline-flex; align-items: center; gap: 4px; margin-bottom: 16px;
}
.back-link:hover { color: #2563eb; }

.parent-card {
    background: #fff; border-radius: 12px; padding: 24px;
    border: 1px solid #e5e7eb; box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-bottom: 20px;
}
.parent-header {
    display: flex; align-items: center; gap: 14px; margin-bottom: 4px;
}
.parent-avatar {
    width: 48px; height: 48px; border-radius: 50%; background: #eff6ff; color: #2563eb;
    display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700;
    flex-shrink: 0;
}
.parent-name { font-size: 18px; font-weight: 700; color: #1e293b; margin: 0; }
.parent-phone { font-size: 13px; color: #64748b; margin: 2px 0 0; }

.section-title {
    font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 12px;
    display: flex; align-items: center; justify-content: space-between;
}

.pet-list { display: flex; flex-direction: column; gap: 10px; }

.pet-row {
    display: flex; align-items: center; justify-content: space-between;
    background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px 20px;
    transition: all 0.2s;
}
.pet-row:hover { border-color: #2563eb; box-shadow: 0 2px 8px rgba(37,99,235,0.08); }

.pet-info { display: flex; align-items: center; gap: 12px; }
.pet-icon {
    width: 40px; height: 40px; border-radius: 8px; background: #f0fdf4; color: #16a34a;
    display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;
}
.pet-name { font-size: 14px; font-weight: 600; color: #1e293b; }
.pet-detail { font-size: 12px; color: #64748b; margin-top: 1px; }

.btn-appt {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
    border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;
    background: #2563eb; color: #fff; transition: all 0.2s; white-space: nowrap;
}
.btn-appt:hover { background: #1d4ed8; color: #fff; transform: translateY(-1px); }

.btn-add-pet {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
    border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;
    background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; transition: all 0.2s;
}
.btn-add-pet:hover { background: #dcfce7; color: #15803d; }

.empty-pets {
    text-align: center; padding: 32px 20px; color: #64748b;
}
.empty-pets-icon { font-size: 36px; margin-bottom: 8px; }
.empty-pets-text { font-size: 14px; margin-bottom: 16px; }
</style>

<div class="sel-container">
    <a href="{{ route('clinic.appointments.create') }}" class="back-link">&larr; Search another number</a>

    {{-- Pet Parent Info --}}
    <div class="parent-card">
        <div class="parent-header">
            <div class="parent-avatar">{{ strtoupper(substr($petParent->name, 0, 1)) }}</div>
            <div>
                <h3 class="parent-name">{{ $petParent->name }}</h3>
                <p class="parent-phone">{{ $petParent->phone }}@if($petParent->email) &middot; {{ $petParent->email }}@endif</p>
            </div>
        </div>
    </div>

    {{-- Pets --}}
    <div class="section-title">
        <span>Select a Pet ({{ $petParent->pets->count() }})</span>
        <a href="{{ route('vet.pets.create', $petParent->id) }}" class="btn-add-pet">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Add Pet
        </a>
    </div>

    @if($petParent->pets->count() == 0)
    <div class="parent-card">
        <div class="empty-pets">
            <div class="empty-pets-icon">🐾</div>
            <div class="empty-pets-text">No pets registered yet. Add a pet to create an appointment.</div>
            <a href="{{ route('vet.pets.create', $petParent->id) }}" class="btn-appt">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Add First Pet
            </a>
        </div>
    </div>
    @else
    <div class="pet-list">
        @foreach($petParent->pets as $pet)
        <div class="pet-row">
            <div class="pet-info">
                <div class="pet-icon">
                    @if(strtolower($pet->species ?? '') === 'dog') 🐕
                    @elseif(strtolower($pet->species ?? '') === 'cat') 🐈
                    @elseif(strtolower($pet->species ?? '') === 'bird') 🐦
                    @else 🐾
                    @endif
                </div>
                <div>
                    <div class="pet-name">{{ $pet->name }}</div>
                    <div class="pet-detail">
                        {{ ucfirst($pet->species ?? 'Unknown') }}
                        @if($pet->breed) &middot; {{ $pet->breed }} @endif
                        @if($pet->age) &middot; {{ $pet->age }} @endif
                        @if($pet->gender) &middot; {{ ucfirst($pet->gender) }} @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('clinic.appointments.createForPet', $pet->id) }}" class="btn-appt">
                Book Appointment
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection
