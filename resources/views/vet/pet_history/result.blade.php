@extends('layouts.vet')

@section('content')

<style>
    /* ---------- Root & Base ---------- */
    :root {
        --primary: #2563eb;
        --success: #22c55e;
        --bg-soft: #f9fafb;
        --border: #e5e7eb;
        --text-dark: #111827;
        --text: #374151;
        --text-muted: #6b7280;
    }

    * {
        box-sizing: border-box;
    }

    body {
        background: #f3f4f6;
    }

    /* ---------- Layout ---------- */
    .history-container {
        max-width: 1080px;
        margin: 36px auto;
        padding: 0 20px;
    }

    .card {
        background: #ffffff;
        padding: 26px 30px;
        border-radius: 14px;
        box-shadow: 0 12px 28px rgba(0,0,0,0.08);
        margin-bottom: 26px;
    }

    /* ---------- Typography ---------- */
    h2 {
        font-size: 24px;
        margin-bottom: 12px;
        color: var(--text-dark);
        letter-spacing: -0.3px;
    }

    h3 {
        font-size: 18px;
        margin-top: 28px;
        margin-bottom: 6px;
        color: var(--primary);
    }

    h4 {
        font-size: 15px;
        margin-bottom: 6px;
        color: var(--text-dark);
    }

    p {
        font-size: 14px;
        color: var(--text);
        margin-bottom: 6px;
        line-height: 1.5;
    }

    .muted {
        color: var(--text-muted);
        font-size: 13px;
    }

    /* ---------- Dividers ---------- */
    .section-divider {
        margin: 22px 0;
        border-top: 1px solid var(--border);
    }

    /* ---------- Appointment Timeline ---------- */
    .appt {
        background: #ffffff;
        border-left: 4px solid var(--border);
        padding: 14px 16px;
        border-radius: 8px;
        margin-top: 16px;
        transition: all 0.2s ease;
    }

    .appt:hover {
        background: #f9fafb;
    }

    .appt.completed {
        border-color: var(--success);
    }

    .appt.scheduled {
        border-color: var(--primary);
    }

    .appt p {
        margin-bottom: 4px;
        font-size: 14px;
    }

    /* ---------- Details Accordion ---------- */
    details {
        margin-top: 10px;
    }

    summary {
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        color: var(--primary);
        list-style: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    summary::-webkit-details-marker {
        display: none;
    }

    details[open] summary {
        color: #1d4ed8;
    }

    /* ---------- Content Boxes ---------- */
    .box {
        margin-top: 12px;
        padding: 14px 16px;
        border-radius: 10px;
        font-size: 13px;
        line-height: 1.6;
    }

    .case-box {
        background: var(--bg-soft);
        border: 1px solid var(--border);
    }

    .rx-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    /* ---------- Empty State ---------- */
    .empty {
        text-align: center;
        color: var(--text-muted);
        margin: 40px 0;
        font-size: 14px;
        padding: 20px;
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 768px) {
        .card {
            padding: 20px;
        }

        h2 {
            font-size: 20px;
        }

        h3 {
            font-size: 16px;
        }
    }

    /* ---------- Pet Wrapper ---------- */
.pet-block {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 18px 22px;
    margin-top: 24px;
}

/* Pet name */
.pet-block h3 {
    margin-top: 0;
    font-size: 19px;
}

/* ---------- Appointment Nesting ---------- */
.pet-block .appt {
    margin-left: 14px;
    margin-top: 14px;
    background: #f9fafb;
}

/* ---------- Appointment Row ---------- */
.appt {
    padding: 14px 18px;
    border-radius: 10px;
}

/* ---------- View Details CTA ---------- */
summary {
    margin-top: 6px;
    padding: 6px 0;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

summary:hover {
    text-decoration: underline;
}

/* ---------- Tighten spacing ---------- */
.pet-block p.muted {
    margin-bottom: 10px;
}

/* ---------- Modal Overlay ---------- */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

/* ---------- Modal Card ---------- */
.modal-card {
    background: #ffffff;
    width: 90%;
    max-width: 720px;
    max-height: 85vh;
    overflow-y: auto;
    border-radius: 14px;
    padding: 22px 26px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
}

/* Close button */
.modal-close {
    position: absolute;
    top: 14px;
    right: 16px;
    border: none;
    background: transparent;
    font-size: 20px;
    cursor: pointer;
}

/* Appointment meta */
.modal-appt {
    background: #f9fafb;
    border-left: 4px solid #22c55e;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 14px;
    margin-top: 10px;
}

/* View button */
.view-btn {
    margin-top: 6px;
    background: none;
    border: none;
    color: #2563eb;
    font-weight: 600;
    cursor: pointer;
    padding: 0;
}

.view-btn:hover {
    text-decoration: underline;
}
</style>

<div class="history-container">

@if(!$petParent)
    <div class="card empty">
        No pet parent found for this mobile number.
    </div>
@else

<div class="card">
    <h2>Pet History</h2>

    <p><strong>Pet Parent:</strong> {{ $petParent->name }}</p>

    <div class="section-divider"></div>

    @forelse($petParent->pets as $pet)
        <div class="pet-block">
            <h3>{{ $pet->name }}</h3>

            <p class="muted">
                Species: {{ ucfirst($pet->species) }}
                @if($pet->breed)
                    · Breed: {{ $pet->breed }}
                @endif
                @if($pet->gender)
                    · Gender: {{ ucfirst($pet->gender) }}
                @endif
            </p>

            @forelse($pet->appointments as $appointment)
                <div class="appt {{ $appointment->status }}">

                    <p>
                        <strong>Date:</strong>
                        {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}
                        · <strong>Status:</strong> {{ ucfirst($appointment->status) }}

                        @if($appointment->pet_age_at_visit)
                            · <strong>Age:</strong> {{ $appointment->pet_age_at_visit }}
                        @endif

                        @if($appointment->weight)
                            · <strong>Wt:</strong> {{ $appointment->weight }} kg
                        @endif
                    </p>

                    {{-- SINGLE DETAILS VIEW --}}
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
                        📁 View Details
                    </button>

                    {{-- HIDDEN CASE CONTENT --}}
                    <div class="case-template" style="display:none;">
                        @include('vet.appointments.partials.history_case', [
                            'appointment' => $appointment
                        ])
                    </div>

                </div>
            @empty
                <p class="muted">No appointments found for this pet.</p>
            @endforelse
        </div>
    @empty
        <p class="muted">No pets found for this pet parent.</p>
    @endforelse

</div>

@endif

</div>

<div id="historyModal" class="modal-overlay" onclick="closeHistoryModal(event)">
    <div class="modal-card">
        <button class="modal-close" onclick="closeHistoryModal()">✕</button>

        <h3 id="modalPetName"></h3>
        <p class="muted" id="modalPetMeta"></p>

        <div class="modal-appt">
            <span id="modalApptMeta"></span>
        </div>

        <div class="section-divider"></div>

        <div id="modalCaseContent"></div>
    </div>
</div>


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