<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Services\AiClinicalService;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\DiagnosticFile;
use App\Models\VetAiCredit;

class VetAiController extends Controller
{
    private function checkCredits(string $feature): VetAiCredit
    {
        $vet = auth('vet')->user();
        $credit = VetAiCredit::getOrCreate($vet->id);
        $cost = VetAiCredit::costFor($feature);

        if (!$credit->hasCredits($cost)) {
            abort(response()->json([
                'error' => "Insufficient AI credits. This feature requires {$cost} credit(s). You have {$credit->balance} remaining.",
                'credits_required' => $cost,
                'credits_balance' => $credit->balance,
                'purchase_url' => route('vet.credits.index'),
            ], 402));
        }

        return $credit;
    }

    private function deductWithUsage(VetAiCredit $credit, string $feature, AiClinicalService $ai, ?int $appointmentId = null): void
    {
        $featureLabels = [
            'refine' => 'Text Refinement',
            'clinical_insights' => 'Clinical Insights',
            'senior_support' => 'Senior Vet Guidance',
            'prescription_support' => 'Prescription Support',
        ];

        $cost = VetAiCredit::costFor($feature);
        $usage = $ai->getLastUsage();

        $credit->deductCredits(
            $cost,
            ($featureLabels[$feature] ?? $feature),
            $feature,
            $appointmentId,
            $usage
        );
    }

    public function refine(Request $request, AiClinicalService $ai)
    {
        $request->validate([
            'field' => 'required|string',
            'text'  => 'required|string',
        ]);

        $credit = $this->checkCredits('refine');

        $refined = $ai->refine(
            $request->field,
            $request->text
        );

        $this->deductWithUsage($credit, 'refine', $ai);

        return response()->json([
            'refined' => $refined,
            'credits_remaining' => $credit->fresh()->balance,
        ]);
    }

    public function clinicalInsights(Request $request, AiClinicalService $ai)
    {
        $caseData = $request->validate([
            'presenting_complaint' => 'nullable|string',
            'history'              => 'nullable|string',
            'clinical_examination' => 'nullable|string',
            'differentials'        => 'nullable|string',
            'diagnosis'            => 'nullable|string',
            'treatment_given'      => 'nullable|string',
            'procedures_done'      => 'nullable|string',
            'further_plan'         => 'nullable|string',
            'advice'               => 'nullable|string',

            // ✅ Vitals
            'temperature'          => 'nullable|string',
            'heart_rate'           => 'nullable|string',
            'respiratory_rate'     => 'nullable|string',
            'capillary_refill_time'=> 'nullable|string',
            'mucous_membrane'      => 'nullable|string',
            'hydration_status'     => 'nullable|string',
            'lymph_nodes'          => 'nullable|string',
            'body_condition_score' => 'nullable|string',
            'pain_score'           => 'nullable|string',

            // ✅ Pet context
            'species'              => 'nullable|string',
            'breed'                => 'nullable|string',
            'gender'               => 'nullable|string',
            'pet_age'              => 'nullable|string',
            'body_weight'          => 'nullable|string',

            // ✅ Structured treatments from live form
            'treatments'           => 'nullable|string',
            'procedures'           => 'nullable|string',
        ]);

        $credit = $this->checkCredits('clinical_insights');

        $insights = $ai->clinicalInsights($caseData);

        $this->deductWithUsage($credit, 'clinical_insights', $ai);

        $insights['credits_remaining'] = $credit->fresh()->balance;
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

        $credit = $this->checkCredits('senior_support');

        $appointment->load([
            'pet',
            'caseSheet',
            'diagnosticReports.files',
            'treatments.drugGeneric',
            'treatments.priceItem',
            'prescription.items',
        ]);

        // 🕓 Collect brief relevant history (no AI)
        $historyText = '';
        $pastAppointments = Appointment::with([
            'caseSheet',
            'prescription.items',
            'diagnosticReports.files',
            'treatments.drugGeneric',
            'treatments.priceItem',
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
            
                // 💉 In-clinic Treatments (drugs & procedures)
                if ($past->treatments->isNotEmpty()) {
                    $drugTx = $past->treatments->whereNotNull('drug_generic_id');
                    $procTx = $past->treatments->whereNull('drug_generic_id');

                    if ($drugTx->isNotEmpty()) {
                        $historyText .= "In-clinic Drug Treatments:\n";
                        foreach ($drugTx as $tx) {
                            $historyText .= "- " . (optional($tx->drugGeneric)->name ?? 'Unknown drug');
                            if ($tx->dose_mg) $historyText .= " | Dose: {$tx->dose_mg} mg";
                            if ($tx->dose_volume_ml) $historyText .= " | Vol: {$tx->dose_volume_ml} ml";
                            if ($tx->route) $historyText .= " | Route: {$tx->route}";
                            $historyText .= "\n";
                        }
                    }
                    if ($procTx->isNotEmpty()) {
                        $historyText .= "Procedures Performed:\n";
                        foreach ($procTx as $tx) {
                            $historyText .= "- " . (optional($tx->priceItem)->name ?? 'Unknown procedure') . "\n";
                        }
                    }
                }

                // 💊 Prescription (take-home medication)
                if ($past->prescription && $past->prescription->items->isNotEmpty()) {
                    $historyText .= "Prescription (Take-home):\n";
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

        // Build structured treatment text for current appointment
        $treatmentText = '';
        if ($appointment->treatments->isNotEmpty()) {
            $drugTx = $appointment->treatments->whereNotNull('drug_generic_id');
            $procTx = $appointment->treatments->whereNull('drug_generic_id');

            if ($drugTx->isNotEmpty()) {
                $treatmentText .= "Injectable/In-clinic Drug Treatments:\n";
                foreach ($drugTx as $tx) {
                    $treatmentText .= "- " . (optional($tx->drugGeneric)->name ?? 'Unknown');
                    if ($tx->dose_mg) $treatmentText .= " | Dose: {$tx->dose_mg} mg";
                    if ($tx->dose_volume_ml) $treatmentText .= " | Volume: {$tx->dose_volume_ml} ml";
                    if ($tx->route) $treatmentText .= " | Route: {$tx->route}";
                    $treatmentText .= "\n";
                }
            }
            if ($procTx->isNotEmpty()) {
                $treatmentText .= "Procedures Performed:\n";
                foreach ($procTx as $tx) {
                    $treatmentText .= "- " . (optional($tx->priceItem)->name ?? 'Unknown procedure') . "\n";
                }
            }
        }

        // Build vitals string
        $vitals = $this->buildVitalsString($appointment->caseSheet);

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
                'vitals'               => $vitals,
                'differentials'        => $appointment->caseSheet->differentials ?? '-',
                'diagnosis'            => $appointment->caseSheet->diagnosis ?? '-',
                'treatment_notes'      => $appointment->caseSheet->treatment_given ?? '-',
                'procedures_done'      => $appointment->caseSheet->procedures_done ?? '-',
                'further_plan'         => $appointment->caseSheet->further_plan ?? '-',
                'advice'               => $appointment->caseSheet->advice ?? '-',
                'treatments'           => $treatmentText ?: 'No in-clinic treatments administered.',
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

        $this->deductWithUsage($credit, 'senior_support', $ai, $appointment->id);

        if ($guidance === '') {
            $guidance =
                "Senior Vet Guidance (Limited)\n\n" .
                "The AI could not generate a full response with the current data.\n\n" .
                "Based on available information:\n" .
                "- Review the case sheet for completeness\n" .
                "- Add diagnostics if clinical response is inadequate\n" .
                "- Reassess patient status and monitor closely\n\n" .
                "This guidance is intentionally conservative.";
        }

        return response()->json([
            'guidance' => $guidance,
            'credits_remaining' => $credit->fresh()->balance,
        ]);
    }

    private function buildVitalsString($caseSheet): string
    {
        if (!$caseSheet) return 'Not recorded';

        $parts = array_filter([
            $caseSheet->temperature ? "Temp: {$caseSheet->temperature}°F" : null,
            $caseSheet->heart_rate ? "HR: {$caseSheet->heart_rate} bpm" : null,
            $caseSheet->respiratory_rate ? "RR: {$caseSheet->respiratory_rate} bpm" : null,
            $caseSheet->capillary_refill_time ? "CRT: {$caseSheet->capillary_refill_time}" : null,
            $caseSheet->mucous_membrane ? "MM: {$caseSheet->mucous_membrane}" : null,
            $caseSheet->hydration_status ? "Hydration: {$caseSheet->hydration_status}" : null,
            $caseSheet->lymph_nodes ? "PLN: {$caseSheet->lymph_nodes}" : null,
            $caseSheet->body_condition_score ? "BCS: {$caseSheet->body_condition_score}" : null,
            $caseSheet->pain_score ? "Pain: {$caseSheet->pain_score}" : null,
        ]);

        return !empty($parts) ? implode(' | ', $parts) : 'Not recorded';
    }

    public function prescriptionSupport(Appointment $appointment, AiClinicalService $ai)
    {

        $clinicId = session('active_clinic_id');
        $vetId    = auth('vet')->id();
    
        abort_if(!$clinicId, 403);
        abort_if($appointment->clinic_id !== $clinicId, 403);
        abort_if($appointment->vet_id !== $vetId, 403);

        $credit = $this->checkCredits('prescription_support');

        // Load all relevant clinical data
        $appointment->load([
            'pet',
            'caseSheet',
            'diagnosticReports.files',
            'treatments.drugGeneric',
            'treatments.priceItem',
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
        // Build structured treatment text
        $treatmentText = '';
        if ($appointment->treatments->isNotEmpty()) {
            $drugTx = $appointment->treatments->whereNotNull('drug_generic_id');
            $procTx = $appointment->treatments->whereNull('drug_generic_id');

            if ($drugTx->isNotEmpty()) {
                $treatmentText .= "Injectable/In-clinic Drugs Already Given:\n";
                foreach ($drugTx as $tx) {
                    $treatmentText .= "- " . (optional($tx->drugGeneric)->name ?? 'Unknown');
                    if ($tx->dose_mg) $treatmentText .= " | Dose: {$tx->dose_mg} mg";
                    if ($tx->dose_volume_ml) $treatmentText .= " | Volume: {$tx->dose_volume_ml} ml";
                    if ($tx->route) $treatmentText .= " | Route: {$tx->route}";
                    $treatmentText .= "\n";
                }
            }
            if ($procTx->isNotEmpty()) {
                $treatmentText .= "Procedures Already Performed:\n";
                foreach ($procTx as $tx) {
                    $treatmentText .= "- " . (optional($tx->priceItem)->name ?? 'Unknown procedure') . "\n";
                }
            }
        }

        // Build vitals string
        $rxVitals = $this->buildVitalsString($appointment->caseSheet);

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
                'vitals'               => $rxVitals,
                'differentials'        => $appointment->caseSheet->differentials ?? '-',
                'diagnosis'            => $appointment->caseSheet->diagnosis ?? '-',
                'treatment_notes'      => $appointment->caseSheet->treatment_given ?? '-',
                'procedures_done'      => $appointment->caseSheet->procedures_done ?? '-',
                'treatments'           => $treatmentText ?: 'No in-clinic treatments administered yet.',
            ],
            'diagnostics' => $diagnosticFindings,
        ];

        /**
         * -----------------------------------
         * Senior Vet Prescription Guidance
         * -----------------------------------
         */
        $guidance = $ai->prescriptionDecisionSupport($context);

        $this->deductWithUsage($credit, 'prescription_support', $ai, $appointment->id);

        return response()->json([
            'guidance' => $guidance,
            'credits_remaining' => $credit->fresh()->balance,
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