@extends('clinic.layout')

@section('content')
<div style="max-width:700px;margin:auto;padding:10px;">

    <a href="{{ route('clinic.ipd.index') }}" style="color:#6b7280;text-decoration:none;font-size:13px;">&larr; Back to IPD List</a>

    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:24px;margin-top:10px;">
        <h2 style="font-size:20px;font-weight:700;color:#111827;margin-bottom:4px;">Admit to IPD</h2>
        <p style="font-size:13px;color:#6b7280;margin-bottom:20px;">Search pet parent by phone number, select pet, then fill admission details</p>

        @if($errors->any())
            <div style="background:#fee2e2;border:1px solid #fecaca;padding:10px 14px;border-radius:8px;font-size:13px;color:#991b1b;margin-bottom:16px;">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        @if(session('error'))
            <div style="background:#fee2e2;border:1px solid #fecaca;padding:10px 14px;border-radius:8px;font-size:13px;color:#991b1b;margin-bottom:16px;">{{ session('error') }}</div>
        @endif

        {{-- Step 1: Search pet parent --}}
        <div id="searchStep">
            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Pet Parent Phone Number *</label>
            <div style="display:flex;gap:8px;">
                <input type="text" id="parentPhone" placeholder="Enter mobile number" style="flex:1;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;" autofocus>
                <button type="button" onclick="searchParent()" style="padding:10px 18px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">Search</button>
            </div>
            <div id="searchResult" style="margin-top:12px;"></div>
        </div>

        {{-- Step 2: Admission form (hidden until pet selected) --}}
        <form method="POST" action="{{ route('clinic.ipd.store') }}" id="admitForm" style="display:none;">
            @csrf

            {{-- Selected pet info card --}}
            <div id="selectedPetCard" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:14px;margin-bottom:20px;"></div>

            <input type="hidden" name="pet_id" id="selectedPetId">
            <input type="hidden" name="pet_parent_id" id="selectedParentId">
            <input type="hidden" name="clinic_id" value="{{ session('active_clinic_id') }}">

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Admission Date & Time</label>
                <input type="datetime-local" name="admission_date" value="{{ now()->format('Y-m-d\TH:i') }}" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Reason for Admission *</label>
                <textarea name="admission_reason" rows="3" required placeholder="Presenting complaint, reason for hospitalisation..." style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;resize:vertical;">{{ old('admission_reason') }}</textarea>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Tentative Diagnosis</label>
                <textarea name="tentative_diagnosis" rows="2" placeholder="Working diagnosis..." style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;resize:vertical;">{{ old('tentative_diagnosis') }}</textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Cage Number</label>
                    <input type="text" name="cage_number" placeholder="e.g. C-12" value="{{ old('cage_number') }}" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">Ward</label>
                    <input type="text" name="ward" placeholder="e.g. ICU, General" value="{{ old('ward') }}" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;">
                </div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" style="padding:10px 20px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">Admit Patient</button>
                <button type="button" onclick="resetForm()" style="padding:10px 20px;background:#e5e7eb;color:#374151;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">Change Pet</button>
            </div>
        </form>
    </div>
</div>

<script>
function searchParent() {
    const phone = document.getElementById('parentPhone').value.trim();
    if (phone.length < 5) { alert('Enter a valid phone number'); return; }

    document.getElementById('searchResult').innerHTML = '<div style="color:#6b7280;font-size:13px;">Searching...</div>';

    fetch('/clinic/ipd/search-parent?phone=' + encodeURIComponent(phone), {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.found) {
            document.getElementById('searchResult').innerHTML = `
                <div style="background:#fef3c7;border:1px solid #fde68a;padding:10px 14px;border-radius:8px;font-size:13px;color:#92400e;">
                    No pet parent found with this number. Please check the number or register them first.
                </div>`;
            return;
        }

        let html = `
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;">
                <div style="font-size:14px;font-weight:600;color:#1e293b;margin-bottom:4px;">${data.parent.name}</div>
                <div style="font-size:12px;color:#64748b;margin-bottom:12px;">${data.parent.phone}${data.parent.email ? ' · ' + data.parent.email : ''}</div>
                <div style="font-size:12px;font-weight:600;color:#374151;margin-bottom:8px;">Select Pet:</div>`;

        if (data.pets.length === 0) {
            html += '<div style="font-size:13px;color:#9ca3af;">No pets registered.</div>';
        } else {
            data.pets.forEach(pet => {
                html += `
                <div onclick="selectPet(${pet.id}, ${data.parent.id}, '${pet.name.replace(/'/g,"\\'")}', '${pet.species || ''}', '${pet.breed || ''}', '${data.parent.name.replace(/'/g,"\\'")}')"
                     style="display:flex;align-items:center;justify-content:space-between;padding:10px;margin-bottom:6px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;cursor:pointer;transition:all 0.15s;"
                     onmouseover="this.style.borderColor='#2563eb';this.style.background='#eff6ff'"
                     onmouseout="this.style.borderColor='#e5e7eb';this.style.background='#fff'">
                    <div>
                        <span style="font-weight:600;font-size:14px;color:#1e293b;">${pet.name}</span>
                        <span style="font-size:12px;color:#64748b;margin-left:8px;">${pet.species || ''}${pet.breed ? ' · ' + pet.breed : ''}</span>
                    </div>
                    <span style="color:#2563eb;font-size:12px;font-weight:600;">Select &rarr;</span>
                </div>`;
            });
        }

        html += '</div>';
        document.getElementById('searchResult').innerHTML = html;
    })
    .catch(() => {
        document.getElementById('searchResult').innerHTML = '<div style="color:#dc2626;font-size:13px;">Search failed. Try again.</div>';
    });
}

function selectPet(petId, parentId, petName, species, breed, parentName) {
    document.getElementById('selectedPetId').value = petId;
    document.getElementById('selectedParentId').value = parentId;
    document.getElementById('selectedPetCard').innerHTML = `
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:40px;height:40px;background:#d1fae5;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;">🐾</div>
            <div>
                <div style="font-weight:700;font-size:15px;color:#166534;">${petName}</div>
                <div style="font-size:12px;color:#166534;">${species}${breed ? ' · ' + breed : ''} · Parent: ${parentName}</div>
            </div>
        </div>`;

    document.getElementById('searchStep').style.display = 'none';
    document.getElementById('admitForm').style.display = 'block';
}

function resetForm() {
    document.getElementById('searchStep').style.display = 'block';
    document.getElementById('admitForm').style.display = 'none';
    document.getElementById('searchResult').innerHTML = '';
    document.getElementById('parentPhone').focus();
}

// Allow Enter key to search
document.getElementById('parentPhone').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); searchParent(); }
});
</script>
@endsection
