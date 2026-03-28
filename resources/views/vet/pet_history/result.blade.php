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

            {{-- OPD Visits --}}
            @forelse($pet->appointments as $appointment)
                <div class="appt {{ $appointment->status }}">
                    <p style="font-size:14px;margin:0;">
                        <span style="font-size:11px;background:#dbeafe;color:#1d4ed8;padding:1px 6px;border-radius:4px;font-weight:600;">OPD</span>
                        <strong>Date:</strong>
                        {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}
                        &middot; <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                        @if($appointment->pet_age_at_visit) &middot; <strong>Age:</strong> {{ $appointment->pet_age_at_visit }} @endif
                        @if($appointment->weight) &middot; <strong>Wt:</strong> {{ $appointment->weight }} kg @endif
                        @if($appointment->treatments->count()) &middot; {{ $appointment->treatments->count() }} treatment(s) @endif
                    </p>

                    <button class="view-btn" onclick="openHistoryModal(this)"
                        data-pet-name="{{ $pet->name }}"
                        data-species="{{ ucfirst($pet->species) }}"
                        data-breed="{{ $pet->breed ?? '-' }}"
                        data-gender="{{ ucfirst($pet->gender ?? '-') }}"
                        data-date="{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y') }}"
                        data-age="{{ $appointment->pet_age_at_visit ?? '-' }}"
                        data-weight="{{ $appointment->weight ?? '-' }}">
                        View Details
                    </button>

                    <div class="case-template" style="display:none;">
                        @include('vet.appointments.partials.history_case', ['appointment' => $appointment])
                    </div>
                </div>
            @empty
                <p style="color:var(--text-muted);font-size:13px;margin-left:14px;">No OPD appointments found.</p>
            @endforelse

            {{-- IPD Admissions --}}
            @if($pet->ipdAdmissions->count())
                @foreach($pet->ipdAdmissions as $ipd)
                <div class="appt" style="border-color:#f59e0b;">
                    <p style="font-size:14px;margin:0;">
                        <span style="font-size:11px;background:#fef3c7;color:#92400e;padding:1px 6px;border-radius:4px;font-weight:600;">IPD</span>
                        <strong>Admitted:</strong> {{ \Carbon\Carbon::parse($ipd->admission_date)->format('d M Y') }}
                        &middot; <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $ipd->status)) }}
                        @if($ipd->discharged_at) &middot; <strong>Discharged:</strong> {{ \Carbon\Carbon::parse($ipd->discharged_at)->format('d M Y') }} @endif
                    </p>
                    @if($ipd->tentative_diagnosis)
                        <p style="font-size:13px;margin:4px 0 0;"><strong>Diagnosis:</strong> {{ $ipd->tentative_diagnosis }}</p>
                    @endif
                    @if($ipd->admission_reason)
                        <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0;">Reason: {{ Str::limit($ipd->admission_reason, 80) }}</p>
                    @endif
                    <p style="font-size:12px;color:var(--text-muted);margin:4px 0 0;">
                        {{ $ipd->treatments->count() }} treatment(s) · {{ $ipd->notes->count() }} note(s)
                    </p>

                    <button class="view-btn" onclick="openHistoryModal(this)"
                        data-pet-name="{{ $pet->name }}"
                        data-species="{{ ucfirst($pet->species) }}"
                        data-breed="{{ $pet->breed ?? '-' }}"
                        data-gender="{{ ucfirst($pet->gender ?? '-') }}"
                        data-date="IPD: {{ \Carbon\Carbon::parse($ipd->admission_date)->format('d M Y') }}"
                        data-age="-"
                        data-weight="{{ $pet->weight ?? '-' }}">
                        View Details
                    </button>

                    <div class="case-template" style="display:none;">
                        <div style="padding:8px 0;">
                            <h4 style="margin:0 0 8px;color:#92400e;">IPD Admission</h4>
                            <p><strong>Admitted:</strong> {{ \Carbon\Carbon::parse($ipd->admission_date)->format('d M Y, h:i A') }}</p>
                            @if($ipd->discharged_at)<p><strong>Discharged:</strong> {{ \Carbon\Carbon::parse($ipd->discharged_at)->format('d M Y, h:i A') }}</p>@endif
                            @if($ipd->admission_reason)<p><strong>Reason:</strong> {{ $ipd->admission_reason }}</p>@endif
                            @if($ipd->tentative_diagnosis)<p><strong>Tentative Diagnosis:</strong> {{ $ipd->tentative_diagnosis }}</p>@endif
                            @if($ipd->cage_number)<p><strong>Cage:</strong> {{ $ipd->cage_number }} · Ward: {{ $ipd->ward ?? '-' }}</p>@endif

                            @if($ipd->treatments->count())
                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:10px 0;">
                            <h4 style="margin:0 0 6px;">Treatments ({{ $ipd->treatments->count() }})</h4>
                            <ul style="margin:0;padding-left:18px;font-size:13px;">
                                @foreach($ipd->treatments as $tx)
                                <li style="margin-bottom:4px;">
                                    <strong>{{ ucfirst($tx->treatment_type) }}</strong>
                                    @if($tx->drug_name) — {{ $tx->drug_name }} @endif
                                    @if($tx->dose_mg) {{ $tx->dose_mg }}mg @endif
                                    @if($tx->dose_volume_ml) ({{ $tx->dose_volume_ml }}ml) @endif
                                    @if($tx->route) · {{ $tx->route }} @endif
                                    <span style="color:#9ca3af;font-size:11px;">{{ $tx->administered_at ? \Carbon\Carbon::parse($tx->administered_at)->format('d/m H:i') : '' }}</span>
                                </li>
                                @endforeach
                            </ul>
                            @endif

                            @if($ipd->notes->count())
                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:10px 0;">
                            <h4 style="margin:0 0 6px;">Clinical Notes ({{ $ipd->notes->count() }})</h4>
                            @foreach($ipd->notes as $note)
                            <div style="background:#f9fafb;border-radius:6px;padding:8px 10px;margin-bottom:6px;font-size:13px;">
                                <strong>{{ ucfirst($note->note_type ?? 'Note') }}</strong>: {{ $note->content }}
                                <div style="font-size:11px;color:#9ca3af;">{{ $note->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                            @endforeach
                            @endif

                            @if($ipd->discharge_summary)
                            <hr style="border:none;border-top:1px solid #f3f4f6;margin:10px 0;">
                            <h4 style="margin:0 0 6px;">Discharge Summary</h4>
                            <p style="font-size:13px;">{{ $ipd->discharge_summary }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            @endif

            {{-- Vaccination Record --}}
            @if($pet->vaccinations->count())
            <div style="margin-top:16px;padding-top:14px;border-top:2px solid #e2e8f0;">
                <h4 style="font-size:15px;font-weight:700;color:#0d9488;margin:0 0 12px;">
                    💉 Vaccination Record ({{ $pet->vaccinations->count() }})
                </h4>
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:#f0fdfa;">
                            <th style="text-align:left;padding:8px 10px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb;">Vaccine</th>
                            <th style="text-align:left;padding:8px 10px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb;">Brand</th>
                            <th style="text-align:left;padding:8px 10px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb;">Dose</th>
                            <th style="text-align:left;padding:8px 10px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb;">Date</th>
                            <th style="text-align:left;padding:8px 10px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb;">Next Due</th>
                            <th style="text-align:left;padding:8px 10px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pet->vaccinations as $vax)
                        <tr>
                            <td style="padding:8px 10px;border-bottom:1px solid #f3f4f6;font-weight:600;">{{ $vax->vaccine_name }}</td>
                            <td style="padding:8px 10px;border-bottom:1px solid #f3f4f6;color:#6b7280;">{{ $vax->brand_name ?? '-' }}</td>
                            <td style="padding:8px 10px;border-bottom:1px solid #f3f4f6;">
                                <span style="background:#dbeafe;color:#1d4ed8;padding:1px 6px;border-radius:4px;font-size:11px;font-weight:600;">{{ $vax->dose_number }}</span>
                            </td>
                            <td style="padding:8px 10px;border-bottom:1px solid #f3f4f6;">{{ $vax->administered_date->format('d M Y') }}</td>
                            <td style="padding:8px 10px;border-bottom:1px solid #f3f4f6;">
                                {{ $vax->next_due_date ? $vax->next_due_date->format('d M Y') : '-' }}
                            </td>
                            <td style="padding:8px 10px;border-bottom:1px solid #f3f4f6;">
                                @if($vax->isOverdue())
                                    <span style="background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;">Overdue</span>
                                @elseif($vax->isDueSoon())
                                    <span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;">Due Soon</span>
                                @elseif($vax->next_due_date && $vax->next_due_date->isFuture())
                                    <span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;">Up to Date</span>
                                @else
                                    <span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600;">Done</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
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
