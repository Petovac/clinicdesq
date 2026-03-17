@extends('layouts.vet')

@section('content')

<div class="v-page-header v-page-header--row">
    <h1>IPD &mdash; In-Patient Department</h1>
</div>

@if($admissions->isEmpty())
    <div class="v-empty v-empty--bordered">No IPD patients at this clinic currently.</div>
@else
    <div class="v-grid v-grid--2">
        @foreach($admissions as $adm)
            <div class="v-card v-card--compact">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <h3 style="font-size:16px;font-weight:600;color:var(--text-dark);margin:0;">{{ $adm->pet->name ?? '—' }}</h3>
                    <span class="v-badge
                        @if($adm->status === 'admitted') v-badge--green
                        @elseif($adm->status === 'deceased') v-badge--red
                        @else v-badge--gray
                        @endif">
                        {{ ucfirst($adm->status) }}
                    </span>
                </div>

                <p style="font-size:13px;color:var(--text-muted);margin:0 0 8px;">
                    {{ ucfirst($adm->pet->species ?? '') }} &middot;
                    {{ $adm->pet->breed ?? '' }} &middot;
                    Parent: {{ $adm->pet->petParent->name ?? '—' }}
                </p>

                <p style="font-size:13px;margin:0 0 12px;">
                    <strong>Admitted:</strong> {{ $adm->admission_date->format('d M Y, h:i A') }}
                    @if($adm->cage_number) &middot; <strong>Cage:</strong> {{ $adm->cage_number }} @endif
                    @if($adm->ward) &middot; <strong>Ward:</strong> {{ $adm->ward }} @endif
                </p>

                <a href="{{ route('vet.ipd.show', $adm->id) }}" class="v-link">View Details &rarr;</a>
            </div>
        @endforeach
    </div>

    @if($admissions->hasPages())
        <div style="margin-top:20px;">{{ $admissions->links() }}</div>
    @endif
@endif

@endsection
