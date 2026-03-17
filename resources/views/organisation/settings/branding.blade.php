@extends('organisation.layout')

@section('content')
<div style="max-width:800px;">

    <h2 style="font-size:22px;font-weight:700;color:#111827;margin-bottom:6px;">Branding &amp; Templates</h2>
    <p style="font-size:14px;color:#6b7280;margin-bottom:24px;">
        Upload your organisation logo, set GST info, and choose document templates.
    </p>

    @if(session('success'))
        <div style="background:#d1fae5;color:#065f46;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Logo Section --}}
    <form method="POST" action="{{ route('organisation.settings.branding.logo') }}" enctype="multipart/form-data">
        @csrf
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;margin-bottom:20px;">
            <h3 style="font-size:16px;font-weight:600;color:#111827;margin:0 0 16px;">Organisation Logo</h3>

            @if($org->logo_url)
                <div style="margin-bottom:14px;">
                    <img src="{{ $org->logo_url }}" alt="Current Logo"
                         style="max-height:80px;max-width:200px;border:1px solid #e5e7eb;border-radius:8px;padding:8px;background:#f9fafb;">
                </div>
                <label style="font-size:13px;cursor:pointer;">
                    <input type="checkbox" name="remove_logo" value="1"> Remove current logo
                </label>
            @else
                <p style="font-size:13px;color:#9ca3af;margin-bottom:10px;">No logo uploaded yet.</p>
            @endif

            <div style="margin-top:12px;">
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Upload Logo</label>
                <input type="file" name="logo" accept="image/jpeg,image/png,image/svg+xml" style="font-size:13px;">
                <p style="font-size:12px;color:#9ca3af;margin-top:4px;">JPG, PNG, or SVG. Max 2MB.</p>
                @error('logo')
                    <p style="color:#dc2626;font-size:12px;margin-top:2px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-top:16px;">
                <button type="submit"
                        style="background:#2563eb;color:#fff;padding:8px 20px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                    Save Logo
                </button>
            </div>
        </div>
    </form>

    {{-- GSTIN Section --}}
    <form method="POST" action="{{ route('organisation.settings.branding.gst') }}">
        @csrf
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;margin-bottom:20px;">
            <h3 style="font-size:16px;font-weight:600;color:#111827;margin:0 0 6px;">GST Information</h3>
            <p style="font-size:13px;color:#6b7280;margin-bottom:14px;">
                Organisation-level GSTIN. This will appear on bills. Clinics with their own GST number will use theirs instead.
            </p>
            <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                <div style="flex:1;min-width:200px;max-width:360px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">GSTIN</label>
                    <input type="text" name="gst_number" value="{{ old('gst_number', $org->gst_number) }}"
                           placeholder="e.g. 29ABCDE1234F1Z5"
                           style="width:100%;padding:9px 12px;font-size:14px;border:1px solid #e5e7eb;border-radius:8px;box-sizing:border-box;">
                    @error('gst_number')
                        <p style="color:#dc2626;font-size:12px;margin-top:2px;">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        style="background:#2563eb;color:#fff;padding:9px 20px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;height:40px;">
                    Save GST
                </button>
            </div>
        </div>
    </form>

    {{-- Template Selection --}}
    <form method="POST" action="{{ route('organisation.settings.branding.update') }}">
        @csrf
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;margin-bottom:20px;">
            <h3 style="font-size:16px;font-weight:600;color:#111827;margin:0 0 6px;">Document Templates</h3>
            <p style="font-size:13px;color:#6b7280;margin-bottom:20px;">
                Choose a layout style for each document type. All clinics in your organisation will use these templates.
                Click "Preview" to see a sample document with your branding.
            </p>

            @php
                $templates = [
                    ['field' => 'template_prescription', 'label' => 'Prescription Template', 'value' => $org->template_prescription, 'type' => 'prescription'],
                    ['field' => 'template_casesheet',    'label' => 'Case Sheet Template',   'value' => $org->template_casesheet,    'type' => 'casesheet'],
                    ['field' => 'template_bill',         'label' => 'Bill / Invoice Template', 'value' => $org->template_bill,        'type' => 'bill'],
                ];

                $options = [
                    'classic' => ['name' => 'Classic', 'desc' => 'Traditional bordered layout with clean lines. Professional and familiar.'],
                    'modern'  => ['name' => 'Modern',  'desc' => 'Card-based design with blue accents and rounded corners. Contemporary feel.'],
                    'minimal' => ['name' => 'Minimal', 'desc' => 'Whitespace-heavy with thin lines. Clean and elegant.'],
                ];
            @endphp

            @foreach($templates as $tpl)
                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">
                        {{ $tpl['label'] }}
                    </label>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        @foreach($options as $key => $opt)
                            <label style="
                                flex:1;min-width:180px;
                                display:block;
                                padding:14px 16px;
                                border:2px solid {{ $tpl['value'] === $key ? '#2563eb' : '#e5e7eb' }};
                                border-radius:10px;
                                cursor:pointer;
                                background:{{ $tpl['value'] === $key ? '#eff6ff' : '#fff' }};
                                transition:border-color 0.15s;
                            ">
                                <input type="radio" name="{{ $tpl['field'] }}" value="{{ $key }}"
                                       {{ $tpl['value'] === $key ? 'checked' : '' }}
                                       style="margin-right:6px;"
                                       onchange="this.closest('div').querySelectorAll('label').forEach(l=>{l.style.borderColor='#e5e7eb';l.style.background='#fff'});this.closest('label').style.borderColor='#2563eb';this.closest('label').style.background='#eff6ff';">
                                <strong style="font-size:14px;color:#111827;">{{ $opt['name'] }}</strong>
                                <p style="font-size:12px;color:#6b7280;margin:4px 0 0;">{{ $opt['desc'] }}</p>
                                <a href="{{ route('organisation.settings.branding.preview', ['type' => $tpl['type'], 'template' => $key]) }}"
                                   target="_blank"
                                   onclick="event.stopPropagation();"
                                   style="display:inline-block;margin-top:6px;font-size:11px;color:#2563eb;text-decoration:none;font-weight:500;">
                                    Preview &#8599;
                                </a>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div style="margin-top:8px;">
                <button type="submit"
                        style="background:#2563eb;color:#fff;padding:10px 24px;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                    Save Templates
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
