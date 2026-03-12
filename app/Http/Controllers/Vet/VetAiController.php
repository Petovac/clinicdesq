<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Services\AiClinicalService;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\DiagnosticFile;

class VetAiController extends Controller
{
    public function refine(Request $request, AiClinicalService $ai)
    {
        $request->validate([
            'field' => 'required|string',
            'text'  => 'required|string',
        ]);

        $refined = $ai->refine(
            $request->field,
            $request->text
        );

        return response()->json([
            'refined' => $refined
        ]);
    }

    public function clinicalInsights(Request $request, AiClinicalService $ai)
    {
        $caseData = $request->validate([
            'presenting_complaint' => 'nullable|string',
            'history'              => 'nullable|string',
            'clinical_examination' => 'nullable|string',
            'diagnosis'            => 'nullable|string',

            // ✅ Pet context
            'species'              => 'nullable|string',
            'breed'                => 'nullable|string',
            'gender'               => 'nullable|string',
            'pet_age'              => 'nullable|string',
            'body_weight'          => 'nullable|string',
        ]);

        $insights = $ai->clinicalInsights($caseData);

        return response()->json($insights);
    }

    

    public function seniorVetSupport(Appointment $appointment, AiClinicalService $ai)
    {

        $hasVerifiedDiagnostics = DiagnosticFile::hasVerifiedFindings($appointment->id);

        $diagnosticText = $hasVerifiedDiagnostics
        ? DiagnosticFile::verifiedSummariesForAppointment($appointment->id)
        : 'No human-verified diagnostic findings available. Guidance based on clinical signs only.';

        $clinicId = session('active_clinic_id');
        $vetId    = auth('vet')->id();

        abort_if(!$clinicId, 403);
        abort_if($appointment->clinic_id !== $clinicId, 403);
        abort_if($appointment->vet_id !== $vetId, 403);

        $appointment->load([
            'pet',
            'caseSheet',
            'diagnosticReports.files',
        ]);

        // 🕓 Collect brief relevant history (no AI)
        $historyText = '';
        $pastAppointments = Appointment::with([
            'caseSheet',
            'prescription.items',
            'diagnosticReports.files'
        ])
        ->where('pet_id', $appointment->pet_id)
        ->where('id', '!=', $appointment->id)
        ->orderBy('scheduled_at', 'desc')
        ->limit(3)
        ->get();

            foreach ($pastAppointments as $past) {

                $historyText .= "Visit: " . $past->scheduled_at->format('d M Y') . "\n";
            
                // 🩺 Case sheet
                if ($past->caseSheet) {
                    if ($past->caseSheet->presenting_complaint) {
                        $historyText .= "Complaint: {$past->caseSheet->presenting_complaint}\n";
                    }
            
                    if ($past->caseSheet->diagnosis) {
                        $historyText .= "Diagnosis: {$past->caseSheet->diagnosis}\n";
                    }
            
                    if ($past->caseSheet->treatment_given) {
                        $historyText .= "Treatment: {$past->caseSheet->treatment_given}\n";
                    }
                }
            
                // 💊 Prescription
                if ($past->prescription && $past->prescription->items->isNotEmpty()) {
                    $historyText .= "Prescription:\n";
                    foreach ($past->prescription->items as $item) {
                        $historyText .=
                            "- {$item->medicine}" .
                            ($item->dosage ? " ({$item->dosage})" : '') .
                            ($item->frequency ? ", {$item->frequency}" : '') .
                            ($item->duration ? ", {$item->duration}" : '') .
                            "\n";
                    }
                }
            
                // 🧪 PAST VERIFIED DIAGNOSTICS (🔥 THIS IS THE KEY ADDITION)
                $verifiedFindings = [];
            
                foreach ($past->diagnosticReports as $report) {
                    foreach ($report->files as $file) {
                        if ($file->status === 'human_verified' && $file->ai_summary) {
                            $verifiedFindings[] = $file->ai_summary;
                        }
                    }
                }
            
                if (!empty($verifiedFindings)) {
                    $historyText .= "Verified Diagnostics:\n";
                    foreach ($verifiedFindings as $finding) {
                        $historyText .= "- " . trim($finding) . "\n";
                    }
                }
            
                $historyText .= "— — —\n";
            }

        $context = [
            'pet' => [
                'species' => $appointment->pet->species ?? '-',
                'breed'   => $appointment->pet->breed ?? '-',
                'gender'  => $appointment->pet->gender ?? '-',
                'age'     => $appointment->calculated_age_at_visit ?? '-',
                'weight'  => $appointment->weight ?? '-',
            ],
            'case' => [
                'presenting_complaint' => $appointment->caseSheet->presenting_complaint ?? '-',
                'history'              => $appointment->caseSheet->history ?? '-',
                'clinical_examination' => $appointment->caseSheet->clinical_examination ?? '-',
                'differentials'        => $appointment->caseSheet->differentials ?? '-',
                'diagnosis'            => $appointment->caseSheet->diagnosis ?? '-',
                'treatment_given'      => $appointment->caseSheet->treatment_given ?? '-',

                // ✅ ADD THIS
                'prescription' => $appointment->prescription
                    ? $appointment->prescription->items
                        ->map(function ($item) {
                            return
                                "{$item->medicine}" .
                                ($item->dosage ? " | Dose: {$item->dosage}" : '') .
                                ($item->frequency ? " | Freq: {$item->frequency}" : '') .
                                ($item->duration ? " | Duration: {$item->duration}" : '');
                        })
                        ->implode("\n")
                    : 'No prescription recorded.',
            ],
            'diagnostics' => $diagnosticText ?: 'No diagnostic findings available.',
            'history'     => $historyText ?: 'No significant past history recorded.',
        ];

        $guidance = trim($ai->seniorVetGuidance($context));

        if ($guidance === '') {
            $guidance =
                "⚠️ Senior Vet Guidance (Limited)\n\n" .
                "The AI could not generate a full response with the current data.\n\n" .
                "Based on available information:\n" .
                "- Review the case sheet for completeness\n" .
                "- Add diagnostics if clinical response is inadequate\n" .
                "- Reassess patient status and monitor closely\n\n" .
                "This guidance is intentionally conservative.";
        }

        return response()->json([
            'guidance' => $guidance,
        ]);
    }

    public function prescriptionSupport(Appointment $appointment, AiClinicalService $ai)
    {

        $clinicId = session('active_clinic_id');
        $vetId    = auth('vet')->id();
    
        abort_if(!$clinicId, 403);
        abort_if($appointment->clinic_id !== $clinicId, 403);
        abort_if($appointment->vet_id !== $vetId, 403);
    
        // Load all relevant clinical data
        $appointment->load([
            'pet',
            'caseSheet',
            'diagnosticReports.files',
        ]);
    
        // ❌ HARD STOP — prescription guidance without case sheet is unsafe
        if (!$appointment->caseSheet) {
            return response()->json([
                'error' => 'Case sheet not available. Complete case sheet before requesting prescription guidance.'
            ], 422);
        }

        if (!DiagnosticFile::hasVerifiedFindings($appointment->id)) {
            return response()->json([
                'error' =>
                    'Prescription guidance blocked. ' .
                    'Human-verified diagnostic findings are required before prescribing.'
            ], 422);
        }

        if (!$appointment->weight || $appointment->weight <= 0) {
            return response()->json([
                'error' =>
                    'Prescription guidance blocked. ' .
                    'Accurate body weight is required for dose safety.'
            ], 422);
        }
    
        /**
         * -----------------------------------
         * Diagnostic Interpretation Context
         * -----------------------------------
         */
        $diagnosticFindings = '';
    
        $diagnosticFindings =
        DiagnosticFile::verifiedSummariesForAppointment($appointment->id)
        ?: 'No human-verified diagnostic findings available.';
        
            if (!$diagnosticFindings) {
                $diagnosticFindings = 'No diagnostic findings available.';
            }
    
        /**
         * -----------------------------------
         * Build Context for Senior Vet AI
         * -----------------------------------
         */
        $context = [
            'pet' => [
                'species' => $appointment->pet->species ?? '-',
                'breed'   => $appointment->pet->breed ?? '-',
                'gender'  => $appointment->pet->gender ?? '-',
                'age'     => $appointment->calculated_age_at_visit ?? '-',
                'weight'  => $appointment->weight ?? '-',
            ],
            'case' => [
                'presenting_complaint' => $appointment->caseSheet->presenting_complaint ?? '-',
                'history'              => $appointment->caseSheet->history ?? '-',
                'clinical_examination' => $appointment->caseSheet->clinical_examination ?? '-',
                'differentials'        => $appointment->caseSheet->differentials ?? '-',
                'diagnosis'            => $appointment->caseSheet->diagnosis ?? '-',
                'treatment_given'      => $appointment->caseSheet->treatment_given ?? '-',
            ],
            'diagnostics' => $diagnosticFindings,
        ];
    
        /**
         * -----------------------------------
         * Senior Vet Prescription Guidance
         * -----------------------------------
         */
        $guidance = $ai->prescriptionDecisionSupport($context);
    
        return response()->json([
            'guidance' => $guidance
        ]);
    }

    public function prescriptionAI(Appointment $appointment, AiClinicalService $ai)
{

    return response()->json([
        'error' =>
            'This endpoint is deprecated.\n' .
            'Use prescription-support with full clinical validation.'
    ], 410);

    $clinicId = session('active_clinic_id');
    $vetId    = auth('vet')->id();

    abort_if(!$clinicId, 403);
    abort_if($appointment->clinic_id !== $clinicId, 403);
    abort_if($appointment->vet_id !== $vetId, 403);

    $appointment->load([
        'pet',
        'caseSheet',
        'diagnosticReports.files',
    ]);

    $diagnostics =
    DiagnosticFile::verifiedSummariesForAppointment($appointment->id)
    ?: 'No human-verified diagnostic findings available.';

    $context = [
        'pet' => [
            'species' => $appointment->pet->species,
            'breed'   => $appointment->pet->breed,
            'gender'  => $appointment->pet->gender,
            'age'     => $appointment->calculated_age_at_visit,
            'weight'  => $appointment->weight,
        ],
        'case' => [
            'diagnosis'            => $appointment->caseSheet->diagnosis,
            'clinical_examination' => $appointment->caseSheet->clinical_examination,
        ],
        'diagnostics' => $diagnostics ?: 'No diagnostic findings.',
    ];

    return response()->json(
        $ai->prescriptionSuggestions($context)
    );
}

}