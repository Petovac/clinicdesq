<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;


class AiClinicalService
{
    protected function callOpenAi(array $messages, float $temperature = 0.2): string
    {
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4.1', // clinical reasoning model
                'messages' => $messages,
                'temperature' => $temperature,
            ]);

        if (!$response->successful()) {
            return '';
        }

        return $response->json('choices.0.message.content') ?? '';
    }

    /**
     * ✨ Rewrite only (no new info, no inference)
     */
    public function refine(string $field, string $text): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' =>
                    'You are a veterinary clinical documentation assistant.
                     Rewrite the notes into clear, concise, professional veterinary medical language.
                     Do NOT add new findings, diagnoses, drugs, or interpretations.
                     Do NOT remove any information.'
            ],
            [
                'role' => 'user',
                'content' => "Rewrite the following {$field}:\n\n{$text}"
            ]
        ];

        return $this->callOpenAi($messages, 0.3);
    }


    /**
     * 🧠 Senior Vet Clinical Review
     */
    public function clinicalInsights(array $caseData): array
    {
        // ✅ Prepare safe variables FIRST (NO logic inside heredoc)
        $presenting   = $caseData['presenting_complaint']   ?? 'Not provided';
        $history      = $caseData['history']                ?? 'Not provided';
        $exam         = $caseData['clinical_examination']   ?? 'Not provided';
        $differentials= $caseData['differentials']          ?? 'Not provided';
        $diagnosis    = $caseData['diagnosis']              ?? 'Not provided';
        $treatment    = $caseData['treatment_given']        ?? 'Not provided';
        $weight       = $caseData['body_weight']            ?? 'Not provided';
    
        $systemPrompt = <<<PROMPT
    You are a senior veterinary clinician with over 25 years of experience in small animal medicine.
    
    You are reviewing a junior veterinarian’s UNSAVED case sheet and providing clinical guidance only.
    If required clinical data is missing, explicitly state what is missing and do not proceed.
    
    IMPORTANT RULES:
    - This is NOT a final diagnosis or prescription
    - Always validate drug doses against body weight
    - If body weight is missing, explicitly state dose verification is not possible
    - Prefer safer first-line medications
    - Mention alternatives with pros and cons
    - Flag incorrect, unsafe, or suboptimal treatments
    - Recommend diagnostics before aggressive treatment where appropriate
    - Be conservative and evidence-based
    - Assume the user is a licensed veterinarian
    
    Tone: calm, senior consultant, practical, non-judgmental
    PROMPT;

    $species = $caseData['species'] ?? 'Not provided';
    $breed   = $caseData['breed'] ?? 'Not provided';
    $gender  = $caseData['gender'] ?? 'Not provided';
    $age     = $caseData['pet_age'] ?? 'Not provided';
    
        $userPrompt = <<<PROMPT
    CASE DATA (LIVE, UNSAVED — CONTEXT AWARE):

    Pet Information:
    - Species: {$species}
    - Breed: {$breed}
    - Sex: {$gender}
    - Age: {$age}
    - Body Weight: {$weight}
        
    Presenting Complaint:
    {$presenting}
    
    History:
    {$history}
    
    Clinical Examination:
    {$exam}
    
    Differentials:
    {$differentials}
    
    Diagnosis:
    {$diagnosis}
    
    Treatment Given:
    {$treatment}
    
    Body Weight:
    {$weight}
    
    ---
    TASK:
    Analyze this case like a senior consultant and provide:
    
    1. Clinical Summary (1–2 lines)
    2. Assessment of Case Quality (what is adequate / missing)
    3. Likely Differentials (ranked)
    4. Treatment Review (appropriateness & safety)
    5. Drug & Dosage Guidance (mg/kg ranges, route, frequency)
    6. Preferred / Safer Alternatives
    7. Recommended Diagnostics (urgent / recommended)
    8. Next Steps & Monitoring Plan
    9. Red Flags / When to Escalate
    
    Use bullet points.
    Avoid definitive language.
    PROMPT;
    
        $response = $this->callOpenAi([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $userPrompt],
        ]);
    
        return [
            'raw' => $response,
        ];
    }

    
    /**
     * 🧠 Senior Vet Clinical Decision Support (REAL-TIME)
     */
    public function seniorVetGuidance(array $context): string
    {
        $systemPrompt = <<<PROMPT
    You are a senior veterinary clinician with over 25 years of experience
    across small animals, large animals, and exotic species.
    If clinical data is missing, explicitly list what is missing and proceed with cautious, limited guidance based only on available information.
    -Explicitly consider past clinical history, past diagnostics, and prior prescriptions when forming guidance

    Your role is to SUPPORT the clinical reasoning of a practicing veterinarian.

    IMPORTANT:
    - You are NOT writing medical records
    - You are NOT giving final diagnoses
    - You are NOT issuing prescriptions
    - You are NOT speaking to a pet owner

    Assumptions:
    - The user is a licensed veterinarian
    - This is a live clinical case
    - Information may be incomplete

    Tone:
    - Calm
    - Practical
    - Non-judgmental
    - Educational
    PROMPT;

        $userPrompt = <<<PROMPT
    CASE CONTEXT (LIVE CLINICAL DATA):

    PET DETAILS:
    Species: {$context['pet']['species']}
    Breed: {$context['pet']['breed']}
    Sex: {$context['pet']['gender']}
    Age: {$context['pet']['age']}
    Body Weight: {$context['pet']['weight']}

    CURRENT VISIT — CASE SHEET:
    Presenting Complaint:
    {$context['case']['presenting_complaint']}

    History:
    {$context['case']['history']}

    Clinical Examination:
    {$context['case']['clinical_examination']}

    Differentials:
    {$context['case']['differentials']}

    Diagnosis:
    {$context['case']['diagnosis']}

    Treatment Given:
    {$context['case']['treatment_given']}

    DIAGNOSTICS — OBJECTIVE FINDINGS:
    {$context['diagnostics']}

    PAST RELEVANT HISTORY:
    {$context['history']}

    ---
    TASK:
    Review this case as a senior veterinary consultant.

    You MUST:
    - Compare current presentation with past completed visits
    - Identify recurrence, progression, or lack of response to prior treatment
    - Flag if current management mirrors previously ineffective therapy
    - Escalate diagnostics if clinical response is inadequate

    Provide guidance under:
    1. Brief Case Recap
    2. Key Clinical Concerns
    3. Pattern Analysis (comparison with past visits)
    4. Differentials to Keep in Mind
    5. Review of Current & Prior Management
    6. Diagnostic Gaps
    7. Treatment Considerations
    8. Monitoring & Red Flags
    9. Learning Points
    PROMPT;

        return $this->callOpenAi([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ], 0.3);
    }

    public function prescriptionDecisionSupport(array $context): string
    {
        $systemPrompt = <<<PROMPT
    You are a senior veterinary clinician with over 25 years of experience.
    If clinical data is missing, explicitly list what is missing and proceed with cautious, limited guidance based only on available information.
    
    Your role:
    - Support a practicing veterinarian in deciding an appropriate PRESCRIPTION PLAN
    - You do NOT write prescriptions
    - You do NOT instruct owners
    - You do NOT document medical records
    
    STRICT RULES:
    - Tablets and oral syrups ONLY
    - Do NOT suggest injections (they belong to treatment, not prescription)
    - Always use mg/kg ranges
    - Always cross-check body weight
    - If weight is missing or unreliable, explicitly say so
    - Prefer safer first-line drugs
    - Mention 1–2 alternatives only when relevant
    - Never invent diagnoses
    - Never ignore laboratory findings
    
    Tone:
    - Senior consultant
    - Practical
    - Conservative
    - Educational
    PROMPT;
    
        $userPrompt = <<<PROMPT
    LIVE CLINICAL CONTEXT:
    
    PET DETAILS:
    Species: {$context['pet']['species']}
    Breed: {$context['pet']['breed']}
    Sex: {$context['pet']['gender']}
    Age: {$context['pet']['age']}
    Body Weight: {$context['pet']['weight']} kg
    
    CASE SUMMARY:
    Presenting Complaint:
    {$context['case']['presenting_complaint']}
    
    History:
    {$context['case']['history']}
    
    Clinical Examination:
    {$context['case']['clinical_examination']}
    
    Differentials:
    {$context['case']['differentials']}
    
    Working Diagnosis:
    {$context['case']['diagnosis']}
    
    Treatment Already Given (in-clinic):
    {$context['case']['treatment_given']}
    
    OBJECTIVE DIAGNOSTICS (LABS / IMAGING):
    {$context['diagnostics']}
    
    ---
    TASK:
    Act as a senior vet reviewing this case BEFORE prescription is written.
    
    Provide output in the following structure:
    
    1. Brief Case Understanding (2–3 lines)
    2. Key Diagnostic Takeaways (explicitly reference labs if present)
    3. Therapeutic Goals (what the prescription should achieve)
    4. Oral Medication Options (tablets / syrups only)
        For EACH drug, use EXACTLY this format:

        Drug Name
        • Indication:
        • Dose (mg/kg):
        • Route:
        • Frequency:
        • Duration:
        • Important cautions:
    5. Safer Alternatives (if any)
    6. What NOT to prescribe / avoid (if relevant)
    7. Monitoring Advice for the Vet
    
    Rules:
    - Do NOT format as a prescription
    - Do NOT give brand names unless unavoidable
    - Do NOT include injections
    - Do NOT address the pet owner
    - Support clinical judgment, do not replace it
    PROMPT;
    
        return $this->callOpenAi([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content'   => $userPrompt],
        ], 0.25);
    }

public function extractRawText(string $filePath): string
{
    try {
        if (!file_exists($filePath)) {
            return 'PDF file not found.';
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        return trim($text) ?: 'PDF parsed but text empty.';
    } catch (\Throwable $e) {
        // 🔍 LOG THE REAL ERROR
        Log::error('PDF extraction failed', [
            'file' => $filePath,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return 'PDF parsing exception occurred.';
    }
}

public function extractClinicalTextFromFile(string $filePath): string
{
    $rawText = $this->extractRawText($filePath);

    $prompt = <<<PROMPT
You are a senior veterinary clinician's assistant.

Task:
Extract ONLY objective laboratory and diagnostic findings from the report below.

Rules:
- Do NOT interpret or diagnose
- Do NOT suggest treatment
- Do NOT mention reference ranges unless abnormal
- Highlight abnormalities clearly
- Use concise bullet points
- Use clinical language suitable for a medical record

Lab Report:
{$rawText}

Output format:
• Finding 1
• Finding 2
• Finding 3
PROMPT;

return $this->callOpenAi([
    [
        'role' => 'user',
        'content' => $prompt
    ]
]);
}

public function prescriptionSuggestions(array $context): array
{
    $systemPrompt = <<<PROMPT
You are a veterinary prescription assistant.

Generate a draft prescription for a licensed veterinarian.

Rules:
- Use ONLY provided data
- No explanations
- No new diagnoses
- No disclaimers
- Output JSON only
PROMPT;

    $userPrompt = <<<PROMPT
PATIENT:
Species: {$context['pet']['species']}
Breed: {$context['pet']['breed']}
Age: {$context['pet']['age']}
Sex: {$context['pet']['gender']}
Body Weight: {$context['pet']['weight']} kg

DIAGNOSIS:
{$context['case']['diagnosis']}

CLINICAL NOTES:
{$context['case']['clinical_examination']}

DIAGNOSTICS:
{$context['diagnostics']}

OUTPUT JSON FORMAT:
{
  "medicines": [
    {
      "medicine": "",
      "dosage": "",
      "frequency": "",
      "duration": "",
      "instructions": ""
    }
  ]
}
PROMPT;

    $raw = $this->callOpenAi([
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user', 'content' => $userPrompt],
    ], 0.1);

    return json_decode($raw, true) ?? ['medicines' => []];
}

}