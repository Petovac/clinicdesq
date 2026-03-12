@extends('organisation.layout')

@section('content')

<style>
.page-title {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 20px;
}

.card {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    margin-bottom: 24px;
}

.search-bar {
    display: flex;
    gap: 10px;
    max-width: 520px;
}

.search-bar input {
    flex: 1;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
}

.search-bar button {
    padding: 10px 16px;
    border-radius: 8px;
    border: none;
    background: #4f46e5;
    color: #fff;
    cursor: pointer;
}

.vet-muted {
    font-size: 13px;
    color: #6b7280;
}

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    text-align: left;
    font-size: 13px;
    color: #6b7280;
    padding: 10px;
    border-bottom: 1px solid #e5e7eb;
}

td {
    padding: 12px 10px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
}

.actions a,
.actions button {
    font-size: 13px;
    margin-right: 10px;
    text-decoration: none;
    background: none;
    border: none;
    cursor: pointer;
}

.actions a {
    color: #2563eb;
}

.actions button {
    color: #dc2626;
}
</style>

<h2 class="page-title">Vet Onboarding</h2>

{{-- SEARCH --}}
<div class="card">
    <form method="GET" class="search-bar">
        <input
            name="q"
            placeholder="Enter exact email / phone / registration no"
            value="{{ request('q') }}"
            required
        >
        <button>Search</button>
    </form>

    <br>

    {{-- SEARCH RESULT --}}
    @if($searchedVet)
        <div class="card" style="margin-top:10px">
            <h3>{{ $searchedVet->name }}</h3>
            <p class="vet-muted">
                Registration No: {{ $searchedVet->registration_number ?? '-' }}
            </p>

            <a href="{{ route('organisation.vets.show', $searchedVet) }}">
                View / Assign Clinics
            </a>
        </div>
    @elseif(request()->filled('q'))
        <p class="vet-muted">No vet found with provided details.</p>
    @endif
</div>

{{-- ASSIGNED VETS --}}
<h3 class="page-title">Vets Working With Your Organisation</h3>

<div class="card table-wrap">
<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Clinics</th>
        <th>Total Appointments</th>
        <th>Tenure</th>
        <th>Action</th>
    </tr>

    @forelse($assignedVets as $vet)
        <tr>
            <td>{{ $vet->name }}</td>

            {{-- SAFE TO SHOW (ONLY ONBOARDED VETS) --}}
            <td>{{ $vet->email }}</td>
            <td>{{ $vet->phone }}</td>

            <td>
                {{ $vet->clinics->pluck('name')->join(', ') }}
            </td>

            <td>
                {{ \App\Models\Appointment::where('vet_id', $vet->id)
                    ->whereIn('clinic_id', $vet->clinics->pluck('id'))
                    ->count() }}
            </td>

            <td>
                {{ optional(
                    \DB::table('clinic_vet')
                        ->where('vet_id', $vet->id)
                        ->where('is_active', 1)
                        ->orderBy('created_at')
                        ->first()
                )->created_at
                    ? \Carbon\Carbon::parse(
                        \DB::table('clinic_vet')
                            ->where('vet_id', $vet->id)
                            ->where('is_active', 1)
                            ->orderBy('created_at')
                            ->value('created_at')
                    )->diffForHumans(null, true)
                    : '-' }}
            </td>

            <td class="actions">
                <a href="{{ route('organisation.vets.show', $vet) }}">
                    Manage
                </a>

                <form
                    method="POST"
                    action="{{ route('organisation.vets.offboard', $vet) }}"
                    style="display:inline"
                    onsubmit="return confirm('Offboard this vet?')"
                >
                    @csrf
                    <button>Offboard</button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="vet-muted">
                No vets onboarded yet.
            </td>
        </tr>
    @endforelse
</table>
</div>

@endsection