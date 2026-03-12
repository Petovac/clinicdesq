<div>

    {{-- HEADER ROW --}}
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <p>
            <strong>Date:</strong>
            {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y, h:i A') }}
        </p>

        <p style="font-size:14px;color:#374151;">
            @if($appointment->pet_age_at_visit)
                <strong>Age:</strong> {{ $appointment->pet_age_at_visit }}
            @endif

            @if($appointment->weight)
                &nbsp; | &nbsp;
                <strong>Wt:</strong> {{ $appointment->weight }} kg
            @endif
        </p>
    </div>

    <hr>

    {{-- ================= --}}
    {{-- CASE SHEET --}}
    {{-- ================= --}}
    @if($appointment->caseSheet)
        <h4>📄 Case Sheet</h4>

        @foreach([
            'Presenting Complaint' => $appointment->caseSheet->presenting_complaint,
            'History' => $appointment->caseSheet->history,
            'Clinical Examination' => $appointment->caseSheet->clinical_examination,
            'Differentials' => $appointment->caseSheet->differentials,
            'Diagnosis' => $appointment->caseSheet->diagnosis,
            'Treatment Given' => $appointment->caseSheet->treatment_given,
            'Procedures Done' => $appointment->caseSheet->procedures_done,
            'Further Plan' => $appointment->caseSheet->further_plan,
            'Advice' => $appointment->caseSheet->advice,
        ] as $label => $value)
            @if($value)
                <p><strong>{{ $label }}:</strong> {{ $value }}</p>
            @endif
        @endforeach

        <hr>
    @endif

    {{-- ================= --}}
    {{-- PRESCRIPTION --}}
    {{-- ================= --}}
    @if($appointment->prescription)
        <h4>💊 Prescription</h4>

        @if($appointment->prescription->notes)
            <p><strong>Notes:</strong> {{ $appointment->prescription->notes }}</p>
        @endif

        <ul style="padding-left:18px;">
            @foreach($appointment->prescription->items as $item)
                <li>
                    {{ $item->medicine }}
                    @if($item->dosage) — {{ $item->dosage }} @endif
                    @if($item->frequency), {{ $item->frequency }} @endif
                    @if($item->duration) ({{ $item->duration }}) @endif
                </li>
            @endforeach
        </ul>

        <hr>
    @endif

    {{-- ===================== --}}
{{-- DIAGNOSTICS --}}
{{-- ===================== --}}

@if($appointment->diagnosticReports->isNotEmpty())
    <h3 style="margin-top:24px;">🧪 Diagnostics</h3>

    @foreach($appointment->diagnosticReports as $report)
        <div style="
            margin-top:12px;
            padding:14px;
            background:#f9fafb;
            border:1px solid #e5e7eb;
            border-radius:8px;
        ">

            <strong>
                {{ strtoupper($report->type) }}
                @if($report->title) — {{ $report->title }} @endif
            </strong>

            @if($report->report_date)
                <div style="font-size:13px;color:#6b7280;margin-top:4px;">
                    Date: {{ $report->report_date->format('d M Y') }}
                </div>
            @endif

            {{-- FILES --}}
            @if($report->files->isNotEmpty())
                <ul style="margin-top:10px;padding-left:18px;">
                    @foreach($report->files as $file)
                        <li style="margin-bottom:12px;">

                            {{-- FILE NAME + ACTIONS --}}
                            <div style="font-size:13px;">
                            {{ $file->display_name ?: $file->original_filename }}

                                <a href="{{ route('vet.diagnostics.files.view', $file->id) }}"
                                   target="_blank"
                                   style="margin-left:8px;color:#2563eb;">
                                    👁 View
                                </a>

                                <a href="{{ route('vet.diagnostics.download', $file->id) }}"
                                   style="margin-left:6px;color:#2563eb;">
                                    ⬇ Download
                                </a>
                            </div>

                            {{-- AI / EXTRACTED SUMMARY --}}
                            @if($file->ai_summary)
                                <div style="
                                    margin-top:6px;
                                    margin-left:12px;
                                    font-size:13px;
                                    color:#374151;
                                ">
                                    <strong>Findings:</strong>
                                    <ul style="margin-top:4px;padding-left:18px;">
                                        @foreach(preg_split('/\r\n|\r|\n|•/', $file->ai_summary) as $line)
                                            @if(trim($line))
                                                <li>{{ trim($line) }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        </li>
                    @endforeach
                </ul>
            @endif

        </div>
    @endforeach
@endif

</div>