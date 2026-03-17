@extends('layouts.vet')

@section('content')

<style>
    .pet-block {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 18px 22px;
        margin-top: 20px;
    }

    .appt {
        background: var(--bg-soft);
        border-left: 4px solid var(--border);
        padding: 14px 18px;
        border-radius: var(--radius-md);
        margin-top: 14px;
        margin-left: 14px;
    }

    .appt.completed { border-color: var(--success); }
    .appt.scheduled { border-color: var(--primary); }

    .view-btn {
        margin-top: 6px;
        background: none;
        border: none;
        color: var(--primary);
        font-weight: 600;
        cursor: pointer;
        padding: 0;
        font-size: 14px;
        font-family: var(--font);
    }

    .view-btn:hover { text-decoration: underline; }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-card {
        background: var(--bg-card);
        width: 90%;
        max-width: 720px;
        max-height: 85vh;
        overflow-y: auto;
        border-radius: var(--radius-lg);
        padding: 22px 26px;
        position: relative;
        box-shadow: var(--shadow-lg);
    }

    .modal-close {
        position: absolute;
        top: 14px;
        right: 16px;
        border: none;
        background: transparent;
        font-size: 20px;
        cursor: pointer;
        color: var(--text-muted);
    }

    .modal-appt {
        background: var(--bg-soft);
        border-left: 4px solid var(--success);
        padding: 10px 14px;
        border-radius: var(--radius-md);
        font-size: 14px;
        margin-top: 10px;
    }
</style>

<div style="max-width:1080px;margin:0 auto;">

@if(!$petParent)
    <div class="v-empty v-empty--bordered">
        No pet parent found for this mobile number.
    </div>
@else

<div class="v-card">
    <div class="v-page-header">
        <h1>Pet History</h1>
        <p><strong>Pet Parent:</strong> {{ $petParent->name }}</p>
    </div>

    <hr class="v-divider">

    @forelse($petParent->pets as $pet)
        <div class="pet-block">
            <h3 style="font-size:18px;font-weight:600;color:var(--primary);margin:0 0 6px;">{{ $pet->name }}</h3>

            <p style="font-size:13px;color:var(--text-muted);margin:0 0 10px;">
                Species: {{ ucfirst($pet->species) }}
                @if($pet->breed) &middot; Breed: {{ $pet->breed }} @endif
                @if($pet->gender) &middot; Gender: {{ ucfirst($pet->gender) }} @endif
            </p>

            @forelse($pet->appointments as $appointment)
                <div class="appt {{ $appointment->status }}">
                    <p style="font-size:14px;margin:0;">
                        <strong>Date:</strong>
                        {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}
                        &middot; <strong>Status:</strong> {{ ucfirst($appointment->status) }}

                        @if($appointment->pet_age_at_visit)
                            &middot; <strong>Age:</strong> {{ $appointment->pet_age_at_visit }}
                        @endif

                        @if($appointment->weight)
                            &middot; <strong>Wt:</strong> {{ $appointment->weight }} kg
                        @endif
                    </p>

                    <button
                        class="view-btn"
                        onclick="openHistoryModal(this)"
                        data-pet-name="{{ $pet->name }}"
                        data-species="{{ ucfirst($pet->species) }}"
                        data-breed="{{ $pet->breed ?? '-' }}"
                        data-gender="{{ ucfirst($pet->gender ?? '-') }}"
                        data-date="{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}"
                        data-age="{{ $appointment->pet_age_at_visit ?? '-' }}"
                        data-weight="{{ $appointment->weight ?? '-' }}"
                    >
                        View Details
                    </button>

                    <div class="case-template" style="display:none;">
                        @include('vet.appointments.partials.history_case', [
                            'appointment' => $appointment
                        ])
                    </div>
                </div>
            @empty
                <p style="color:var(--text-muted);font-size:13px;margin-left:14px;">No appointments found for this pet.</p>
            @endforelse
        </div>
    @empty
        <p style="color:var(--text-muted);">No pets found for this pet parent.</p>
    @endforelse
</div>

@endif

</div>

<div id="historyModal" class="modal-overlay" onclick="closeHistoryModal(event)">
    <div class="modal-card">
        <button class="modal-close" onclick="closeHistoryModal()">&#x2715;</button>

        <h3 id="modalPetName" style="font-size:18px;color:var(--text-dark);margin:0 0 4px;"></h3>
        <p id="modalPetMeta" style="font-size:13px;color:var(--text-muted);margin:0;"></p>

        <div class="modal-appt">
            <span id="modalApptMeta"></span>
        </div>

        <hr class="v-divider">

        <div id="modalCaseContent"></div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openHistoryModal(btn) {
    const petName = btn.dataset.petName;
    const species = btn.dataset.species;
    const breed = btn.dataset.breed;
    const gender = btn.dataset.gender;
    const date = btn.dataset.date;
    const age = btn.dataset.age;
    const weight = btn.dataset.weight;

    const caseHtml = btn.nextElementSibling.innerHTML;

    document.getElementById('modalPetName').innerText = petName;
    document.getElementById('modalPetMeta').innerText =
        `Species: ${species} · Breed: ${breed} · Gender: ${gender}`;
    document.getElementById('modalApptMeta').innerText =
        `Date: ${date} · Age: ${age} · Wt: ${weight} kg`;
    document.getElementById('modalCaseContent').innerHTML = caseHtml;

    document.getElementById('historyModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeHistoryModal(event = null) {
    if (!event || event.target.id === 'historyModal') {
        document.getElementById('historyModal').style.display = 'none';
        document.body.style.overflow = '';
    }
}
</script>
@endsection
