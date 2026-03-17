@extends('clinic.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">IPD — In-Patient Department</h4>
        @if(auth()->user()->hasPermission('ipd.manage'))
            <a href="{{ route('clinic.ipd.create') }}" class="btn btn-primary btn-sm">+ New Admission</a>
        @endif
    </div>

    {{-- Filter Tabs --}}
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $filter === 'admitted' ? 'active' : '' }}" href="?filter=admitted">Admitted</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $filter === 'discharged' ? 'active' : '' }}" href="?filter=discharged">Discharged</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $filter === 'all' ? 'active' : '' }}" href="?filter=all">All</a>
        </li>
    </ul>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($admissions->isEmpty())
        <div class="text-center text-muted py-5">No IPD records found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Pet</th>
                        <th>Species</th>
                        <th>Parent</th>
                        <th>Admitted</th>
                        <th>Cage / Ward</th>
                        <th>Status</th>
                        <th>Clinic</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admissions as $adm)
                    <tr>
                        <td class="fw-semibold">{{ $adm->pet->name ?? '—' }}</td>
                        <td>{{ ucfirst($adm->pet->species ?? '') }} &middot; {{ $adm->pet->breed ?? '' }}</td>
                        <td>{{ $adm->pet->petParent->name ?? '—' }}</td>
                        <td>{{ $adm->admission_date->format('d M Y, h:i A') }}</td>
                        <td>
                            {{ $adm->cage_number ?? '—' }}
                            @if($adm->ward) / {{ $adm->ward }} @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = match($adm->status) {
                                    'admitted' => 'bg-success',
                                    'discharged' => 'bg-secondary',
                                    'deceased' => 'bg-danger',
                                    default => 'bg-warning text-dark',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($adm->status) }}</span>
                        </td>
                        <td style="font-size:12px;">{{ $adm->clinic->name ?? '' }}</td>
                        <td><a href="{{ route('clinic.ipd.show', $adm->id) }}" class="btn btn-outline-primary btn-sm">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($admissions->hasPages())
            <div class="mt-3">{{ $admissions->appends(request()->query())->links() }}</div>
        @endif
    @endif
</div>
@endsection
