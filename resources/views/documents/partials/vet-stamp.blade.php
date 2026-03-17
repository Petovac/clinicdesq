{{-- Vet Stamp + Signature --}}
<div style="text-align:right; margin-top:24px;">
    @if(!empty($vetSignatureUrl))
        <img src="{{ $vetSignatureUrl }}" alt="Signature"
             style="height:50px; max-width:160px; display:block; margin-left:auto; margin-bottom:4px; object-fit:contain;">
    @endif
    <div style="border:2px solid #333; display:inline-block; padding:8px 16px; text-align:center; font-size:12px; border-radius:4px;">
        <div style="font-weight:bold; font-size:13px;">{{ $vet->name ?? '' }}</div>
        @if(!empty($vet->degree))
            <div style="color:#555;">{{ $vet->degree }}</div>
        @endif
        @if(!empty($vet->registration_number))
            <div style="color:#555;">Reg. No: {{ $vet->registration_number }}</div>
        @endif
    </div>
</div>
