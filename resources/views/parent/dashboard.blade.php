@extends('layouts.parent')

@section('title', 'My Pets')

@section('styles')
<style>
    .welcome { margin-bottom: 24px; }
    .welcome h2 { font-size: 22px; font-weight: 700; color: #1e293b; }
    .welcome p { font-size: 14px; color: #64748b; margin-top: 4px; }
    .pets-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
    .pet-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; transition: box-shadow 0.15s, border-color 0.15s; cursor: pointer; text-decoration: none; color: inherit; display: block; }
    .pet-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); border-color: #2563eb; text-decoration: none; }
    .pet-name { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
    .pet-detail { font-size: 13px; color: #64748b; margin-bottom: 3px; }
    .pet-species { display: inline-block; background: #eff6ff; color: #2563eb; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; margin-bottom: 10px; }
    .empty { text-align: center; padding: 60px 20px; color: #9ca3af; }
    .empty h3 { font-size: 18px; color: #64748b; margin-bottom: 6px; }
</style>
@endsection

@section('content')
    <div class="welcome">
        <h2>Hello, {{ $parent->name }}</h2>
        <p>Here are your registered pets and their health records.</p>
    </div>

    @if($pets->isEmpty())
        <div class="empty">
            <h3>No Pets Found</h3>
            <p>No pets are registered under your phone number yet.</p>
        </div>
    @else
        <div class="pets-grid">
            @foreach($pets as $pet)
                <a href="{{ route('parent.pets.show', $pet) }}" class="pet-card">
                    <span class="pet-species">{{ ucfirst($pet->species) }}</span>
                    <div class="pet-name">{{ $pet->name }}</div>
                    <div class="pet-detail"><strong>Breed:</strong> {{ $pet->breed ?? '—' }}</div>
                    <div class="pet-detail"><strong>Age:</strong> {{ $pet->current_age ?? '—' }}</div>
                    <div class="pet-detail"><strong>Gender:</strong> {{ ucfirst($pet->gender ?? '—') }}</div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
