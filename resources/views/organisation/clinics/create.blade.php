@extends('organisation.layout')

@section('content')

<div style="max-width:720px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:700;margin:0;color:#1e293b;">Create Clinic</h2>
            <p style="font-size:13px;color:#64748b;margin:4px 0 0;">Add a new clinic location to your organisation.</p>
        </div>
        <a href="{{ route('organisation.clinics.index') }}" style="font-size:13px;color:#64748b;text-decoration:none;display:flex;align-items:center;gap:4px;">&larr; Back to Clinics</a>
    </div>

    @if($errors->any())
    <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#991b1b;">
        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('organisation.clinics.store') }}">
        @csrf

        {{-- Basic Info --}}
        <div class="card" style="padding:24px;margin-bottom:16px;">
            <h3 style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 16px;padding-bottom:10px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:8px;">
                <span style="width:24px;height:24px;background:#eff6ff;color:#2563eb;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">1</span>
                Basic Information
            </h3>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Clinic Name <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Happy Paws Veterinary Clinic"
                    style="width:100%;padding:10px 14px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;transition:all 0.2s;background:#f8fafc;"
                    onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)';this.style.background='#fff'"
                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='';this.style.background='#f8fafc'">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 98765 43210"
                        style="width:100%;padding:10px 14px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;background:#f8fafc;"
                        onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)';this.style.background='#fff'"
                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='';this.style.background='#f8fafc'">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="clinic@example.com"
                        style="width:100%;padding:10px 14px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;background:#f8fafc;"
                        onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)';this.style.background='#fff'"
                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='';this.style.background='#f8fafc'">
                </div>
            </div>
        </div>

        {{-- Location --}}
        <div class="card" style="padding:24px;margin-bottom:16px;">
            <h3 style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 16px;padding-bottom:10px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:8px;">
                <span style="width:24px;height:24px;background:#f0fdf4;color:#16a34a;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">2</span>
                Location
            </h3>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Address</label>
                <textarea name="address" rows="2" placeholder="Street address, building, floor..."
                    style="width:100%;padding:10px 14px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;background:#f8fafc;resize:vertical;min-height:60px;"
                    onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)';this.style.background='#fff'"
                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='';this.style.background='#f8fafc'">{{ old('address') }}</textarea>
            </div>

            @php $locStates = config('locations.states', []); $locCities = config('locations.cities', []); @endphp

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:4px;">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">State</label>
                    <select name="state" id="cc-state"
                        style="width:100%;padding:10px 14px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;background:#f8fafc;cursor:pointer;"
                        onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)'"
                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow=''">
                        <option value="">Select State</option>
                        @foreach($locStates as $s)<option value="{{ $s }}" {{ old('state') === $s ? 'selected' : '' }}>{{ $s }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">City</label>
                    <select name="city" id="cc-city"
                        style="width:100%;padding:10px 14px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;background:#f8fafc;cursor:pointer;"
                        onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)'"
                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow=''">
                        <option value="">Select City</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Pincode</label>
                    <input type="text" name="pincode" value="{{ old('pincode') }}" placeholder="560001"
                        style="width:100%;padding:10px 14px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;background:#f8fafc;"
                        onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)';this.style.background='#fff'"
                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='';this.style.background='#f8fafc'">
                </div>
            </div>

            <script>(function(){var cm=@json($locCities),ss=document.getElementById('cc-state'),cs=document.getElementById('cc-city');ss.addEventListener('change',function(){cs.innerHTML='<option value="">Select City</option>';(cm[this.value]||[]).forEach(function(c){var o=document.createElement('option');o.value=c;o.textContent=c;cs.appendChild(o);});});})();</script>
        </div>

        {{-- Additional Details --}}
        <div class="card" style="padding:24px;margin-bottom:16px;">
            <h3 style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 16px;padding-bottom:10px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:8px;">
                <span style="width:24px;height:24px;background:#fefce8;color:#ca8a04;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">3</span>
                Additional Details
            </h3>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">GST Number</label>
                    <input type="text" name="gst_number" value="{{ old('gst_number') }}" placeholder="22AAAAA0000A1Z5"
                        style="width:100%;padding:10px 14px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;background:#f8fafc;"
                        onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)';this.style.background='#fff'"
                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='';this.style.background='#f8fafc'">
                    <div style="font-size:11px;color:#9ca3af;margin-top:3px;">Optional — for invoicing</div>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Google Reviews URL</label>
                    <input type="url" name="gmb_review_url" value="{{ old('gmb_review_url') }}" placeholder="https://g.page/r/your-clinic/review"
                        style="width:100%;padding:10px 14px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;background:#f8fafc;"
                        onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)';this.style.background='#fff'"
                        onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='';this.style.background='#f8fafc'">
                    <div style="font-size:11px;color:#9ca3af;margin-top:3px;">Clients will be prompted to review</div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <a href="{{ route('organisation.clinics.index') }}"
               style="padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;color:#475569;background:#f1f5f9;text-decoration:none;transition:all 0.2s;">
                Cancel
            </a>
            <button type="submit"
                style="padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;color:#fff;background:#2563eb;border:none;cursor:pointer;transition:all 0.2s;"
                onmouseover="this.style.background='#1d4ed8';this.style.transform='translateY(-1px)'"
                onmouseout="this.style.background='#2563eb';this.style.transform=''">
                Create Clinic
            </button>
        </div>
    </form>
</div>

@endsection
