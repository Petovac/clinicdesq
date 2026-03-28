<?php

/**
 * Run on live: php artisan tinker database/seeds/seed_demo_case.php
 *
 * Creates a realistic pet (Golden Retriever "Max") with 3 past visits
 * and 1 current scheduled appointment with a filled case sheet.
 * Safe to run — only INSERTs, no updates/deletes.
 */

use Illuminate\Support\Carbon;

$vetId = 9; // Dr Amit Sharma
$clinicId = 5;
$petParentId = 1; // Harshit

// ── 1. Create Pet ──
$petId = DB::table('pets')->insertGetId([
    'pet_parent_id' => $petParentId,
    'name' => 'Max',
    'species' => 'Dog',
    'breed' => 'Golden Retriever',
    'gender' => 'Male',
    'date_of_birth' => '2022-06-15',
    'color' => 'Golden',
    'microchip_number' => 'MCH-2024-00981',
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Pet created: Max (ID: $petId)\n";

// ── 2. Past Visit 1 — Gastroenteritis (6 weeks ago) ──
$appt1Id = DB::table('appointments')->insertGetId([
    'clinic_id' => $clinicId,
    'vet_id' => $vetId,
    'pet_parent_id' => $petParentId,
    'pet_id' => $petId,
    'weight' => 28.5,
    'pet_age_at_visit' => '3y 6m',
    'scheduled_at' => Carbon::now()->subWeeks(6),
    'status' => 'completed',
    'checked_in_at' => Carbon::now()->subWeeks(6),
    'consultation_started_at' => Carbon::now()->subWeeks(6)->addMinutes(5),
    'completed_at' => Carbon::now()->subWeeks(6)->addMinutes(30),
    'created_at' => Carbon::now()->subWeeks(6),
    'updated_at' => Carbon::now()->subWeeks(6),
]);

DB::table('case_sheets')->insert([
    'appointment_id' => $appt1Id,
    'presenting_complaint' => 'Vomiting (4-5 episodes since last night), watery diarrhea, refusing food since morning. Owner reports the dog may have eaten garbage from the street yesterday evening.',
    'history' => 'Vaccination up to date. No previous GI episodes. Fed home-cooked food (rice + chicken). Got into garbage bin yesterday. No access to toxins. Last dewormed 3 months ago.',
    'clinical_examination' => 'Mild dehydration (skin turgor slightly delayed). Abdomen tense on palpation, mild discomfort in mid-abdominal region. No foreign body palpable. Borborygmi present. Mucous membranes slightly tacky.',
    'temperature' => '103.2',
    'heart_rate' => '110',
    'respiratory_rate' => '28',
    'capillary_refill_time' => '2.5 sec',
    'mucous_membrane' => 'Pale pink, slightly tacky',
    'hydration_status' => '5-6% dehydrated',
    'lymph_nodes' => 'Normal',
    'body_condition_score' => '5/9',
    'pain_score' => '3/10',
    'differentials' => 'Acute gastroenteritis (dietary indiscretion), Hemorrhagic gastroenteritis, Foreign body obstruction, Pancreatitis',
    'diagnosis' => 'Acute gastroenteritis secondary to dietary indiscretion',
    'treatment_given' => 'IV fluid therapy (Ringer Lactate 500ml over 4 hours). Anti-emetic injection given. Antibiotic started prophylactically.',
    'procedures_done' => 'IV catheterization, fluid therapy',
    'further_plan' => 'Reassess hydration in 24 hours. If vomiting persists, recommend abdominal ultrasound to rule out foreign body or pancreatitis. Bland diet (boiled chicken + rice) for 5 days.',
    'advice' => 'Strict bland diet for 5 days. No treats, no table scraps. Ensure access to clean water. Monitor stool consistency. Return if vomiting recurs or blood in stool.',
    'prognosis' => 'Good — expected full recovery in 3-5 days with supportive care',
    'followup_date' => Carbon::now()->subWeeks(5)->toDateString(),
    'followup_reason' => 'Reassess hydration and GI status',
    'created_at' => Carbon::now()->subWeeks(6),
    'updated_at' => Carbon::now()->subWeeks(6),
]);

// Treatments for visit 1
DB::table('appointment_treatments')->insert([
    ['appointment_id' => $appt1Id, 'drug_generic_id' => 23, 'dose_mg' => 28, 'dose_volume_ml' => 1.4, 'route' => 'SC', 'created_at' => Carbon::now()->subWeeks(6), 'updated_at' => Carbon::now()->subWeeks(6)], // Maropitant (Cerenia)
    ['appointment_id' => $appt1Id, 'drug_generic_id' => 19, 'dose_mg' => 285, 'dose_volume_ml' => null, 'route' => 'IV', 'created_at' => Carbon::now()->subWeeks(6), 'updated_at' => Carbon::now()->subWeeks(6)], // Metronidazole
]);

// Prescription for visit 1
$rx1Id = DB::table('prescriptions')->insertGetId([
    'appointment_id' => $appt1Id,
    'notes' => 'Bland diet for 5 days. Follow up in 1 week.',
    'created_at' => Carbon::now()->subWeeks(6),
    'updated_at' => Carbon::now()->subWeeks(6),
]);

DB::table('prescription_items')->insert([
    ['prescription_id' => $rx1Id, 'medicine' => 'Metronidazole 200mg', 'dosage' => '1.5 tabs (300mg)', 'frequency' => 'BID', 'duration' => '5 days', 'instructions' => 'After food', 'created_at' => Carbon::now()->subWeeks(6), 'updated_at' => Carbon::now()->subWeeks(6)],
    ['prescription_id' => $rx1Id, 'medicine' => 'Ondansetron 4mg', 'dosage' => '1 tab', 'frequency' => 'BID', 'duration' => '3 days', 'instructions' => '30 min before food', 'created_at' => Carbon::now()->subWeeks(6), 'updated_at' => Carbon::now()->subWeeks(6)],
    ['prescription_id' => $rx1Id, 'medicine' => 'Pantoprazole 40mg', 'dosage' => '0.5 tab (20mg)', 'frequency' => 'SID', 'duration' => '5 days', 'instructions' => 'Before food, morning', 'created_at' => Carbon::now()->subWeeks(6), 'updated_at' => Carbon::now()->subWeeks(6)],
]);

echo "Past Visit 1 created (Gastroenteritis, 6 weeks ago) — Appt #$appt1Id\n";


// ── 3. Past Visit 2 — Follow-up + Skin Issue (4 weeks ago) ──
$appt2Id = DB::table('appointments')->insertGetId([
    'clinic_id' => $clinicId,
    'vet_id' => $vetId,
    'pet_parent_id' => $petParentId,
    'pet_id' => $petId,
    'weight' => 29.0,
    'pet_age_at_visit' => '3y 7m',
    'scheduled_at' => Carbon::now()->subWeeks(4),
    'status' => 'completed',
    'checked_in_at' => Carbon::now()->subWeeks(4),
    'consultation_started_at' => Carbon::now()->subWeeks(4)->addMinutes(3),
    'completed_at' => Carbon::now()->subWeeks(4)->addMinutes(25),
    'created_at' => Carbon::now()->subWeeks(4),
    'updated_at' => Carbon::now()->subWeeks(4),
]);

DB::table('case_sheets')->insert([
    'appointment_id' => $appt2Id,
    'presenting_complaint' => 'Follow-up for gastroenteritis (resolved). New complaint: scratching ears and shaking head frequently for 3 days. Owner noticed dark brown discharge in left ear.',
    'history' => 'GI signs resolved completely after previous treatment. Appetite normal. Stool formed. Now presenting with ear scratching — started 3 days ago. Dog was bathed at home last week, water may have entered ears. No history of ear problems before.',
    'clinical_examination' => 'General condition good. Hydration normal. Left ear: erythematous ear canal, dark brown waxy discharge with mild odor. Right ear: mild wax buildup, no inflammation. Otoscopic exam: left tympanic membrane intact, canal swollen. Skin: mild erythema in axillary region bilaterally.',
    'temperature' => '101.8',
    'heart_rate' => '96',
    'respiratory_rate' => '22',
    'capillary_refill_time' => '<2 sec',
    'mucous_membrane' => 'Pink, moist',
    'hydration_status' => 'Normal',
    'lymph_nodes' => 'Submandibular slightly enlarged left side',
    'body_condition_score' => '5/9',
    'pain_score' => '2/10',
    'differentials' => 'Otitis externa (bacterial/yeast), Ear mite infestation, Allergic otitis, Atopic dermatitis with secondary ear involvement',
    'diagnosis' => 'Left otitis externa — likely yeast (Malassezia). Early signs of atopic dermatitis (axillary erythema).',
    'treatment_given' => 'Ear cleaning performed in clinic with chlorhexidine solution. Topical ear drops applied (first dose).',
    'procedures_done' => 'Otoscopic examination, ear cleaning and flushing (left ear)',
    'further_plan' => 'Continue topical ear treatment for 10 days. If axillary erythema worsens or new lesions appear, recommend skin scraping and allergy workup. Recheck ears in 10 days.',
    'advice' => 'Clean ears gently with prescribed solution before applying drops. Do not use cotton buds deep in the ear. Keep ears dry — use cotton ball during bathing. Monitor for increased scratching or new skin lesions.',
    'prognosis' => 'Good for otitis. Guarded if underlying atopy — may need long-term management.',
    'followup_date' => Carbon::now()->subWeeks(2)->toDateString(),
    'followup_reason' => 'Ear recheck + assess skin',
    'created_at' => Carbon::now()->subWeeks(4),
    'updated_at' => Carbon::now()->subWeeks(4),
]);

// Prescription for visit 2
$rx2Id = DB::table('prescriptions')->insertGetId([
    'appointment_id' => $appt2Id,
    'notes' => 'Topical ear treatment. Recheck in 10 days.',
    'created_at' => Carbon::now()->subWeeks(4),
    'updated_at' => Carbon::now()->subWeeks(4),
]);

DB::table('prescription_items')->insert([
    ['prescription_id' => $rx2Id, 'medicine' => 'Otomax Ear Drops', 'dosage' => '5 drops left ear', 'frequency' => 'BID', 'duration' => '10 days', 'instructions' => 'Clean ear before applying. Massage base of ear.', 'created_at' => Carbon::now()->subWeeks(4), 'updated_at' => Carbon::now()->subWeeks(4)],
    ['prescription_id' => $rx2Id, 'medicine' => 'Cetirizine 10mg', 'dosage' => '1 tab', 'frequency' => 'SID', 'duration' => '14 days', 'instructions' => 'For itching. Evening dose.', 'created_at' => Carbon::now()->subWeeks(4), 'updated_at' => Carbon::now()->subWeeks(4)],
]);

echo "Past Visit 2 created (Otitis + early atopy, 4 weeks ago) — Appt #$appt2Id\n";


// ── 4. Past Visit 3 — Ear Recheck + Limping (2 weeks ago) ──
$appt3Id = DB::table('appointments')->insertGetId([
    'clinic_id' => $clinicId,
    'vet_id' => $vetId,
    'pet_parent_id' => $petParentId,
    'pet_id' => $petId,
    'weight' => 29.2,
    'pet_age_at_visit' => '3y 8m',
    'scheduled_at' => Carbon::now()->subWeeks(2),
    'status' => 'completed',
    'checked_in_at' => Carbon::now()->subWeeks(2),
    'consultation_started_at' => Carbon::now()->subWeeks(2)->addMinutes(4),
    'completed_at' => Carbon::now()->subWeeks(2)->addMinutes(35),
    'created_at' => Carbon::now()->subWeeks(2),
    'updated_at' => Carbon::now()->subWeeks(2),
]);

DB::table('case_sheets')->insert([
    'appointment_id' => $appt3Id,
    'presenting_complaint' => 'Ear recheck (left otitis). New complaint: limping on right forelimb since yesterday. Owner says dog was playing in the park and suddenly started limping. No swelling visible.',
    'history' => 'Ear drops completed as prescribed. Ear scratching reduced significantly. Skin itching still present intermittently in axillary and inguinal areas. New: sudden onset right forelimb lameness after vigorous play.',
    'clinical_examination' => 'Left ear: much improved, mild residual wax, no erythema or discharge. Right ear: normal. Right forelimb: pain on flexion of carpus, mild soft tissue swelling over dorsal carpus. No crepitus. Weight-bearing lameness grade 2/5. Axillary skin: persistent mild erythema, no secondary infection.',
    'temperature' => '101.5',
    'heart_rate' => '92',
    'respiratory_rate' => '20',
    'capillary_refill_time' => '<2 sec',
    'mucous_membrane' => 'Pink, moist',
    'hydration_status' => 'Normal',
    'lymph_nodes' => 'Normal',
    'body_condition_score' => '5/9',
    'pain_score' => '4/10',
    'differentials' => 'Soft tissue sprain/strain (carpus), Fracture (unlikely — weight bearing), Ligament injury, Early osteoarthritis (breed predisposition)',
    'diagnosis' => 'Right carpal sprain (grade 1-2). Left otitis externa — resolved. Persistent mild atopic dermatitis.',
    'treatment_given' => 'Meloxicam injection for pain and inflammation. Cold compress applied to right carpus in clinic.',
    'procedures_done' => 'Orthopedic examination of right forelimb. Cold compress therapy.',
    'further_plan' => 'Rest for 7-10 days — no running, jumping, or rough play. If lameness persists beyond 5 days, recommend radiograph of right carpus. Continue monitoring skin — if worsens, schedule allergy panel.',
    'advice' => 'Strict rest — leash walks only for toileting. No stairs if possible. Ice pack on carpus for 10 min twice daily for 3 days. Give pain medication with food. Return immediately if lameness worsens or swelling increases.',
    'prognosis' => 'Good for sprain with rest. Monitor skin long-term.',
    'followup_date' => Carbon::now()->subDays(3)->toDateString(),
    'followup_reason' => 'Lameness recheck',
    'created_at' => Carbon::now()->subWeeks(2),
    'updated_at' => Carbon::now()->subWeeks(2),
]);

// Treatment for visit 3
DB::table('appointment_treatments')->insert([
    ['appointment_id' => $appt3Id, 'drug_generic_id' => 14, 'dose_mg' => 5.8, 'dose_volume_ml' => 0.58, 'route' => 'SC', 'created_at' => Carbon::now()->subWeeks(2), 'updated_at' => Carbon::now()->subWeeks(2)], // Meloxicam
]);

// Prescription for visit 3
$rx3Id = DB::table('prescriptions')->insertGetId([
    'appointment_id' => $appt3Id,
    'notes' => 'Rest and anti-inflammatory. Recheck in 5-7 days.',
    'created_at' => Carbon::now()->subWeeks(2),
    'updated_at' => Carbon::now()->subWeeks(2),
]);

DB::table('prescription_items')->insert([
    ['prescription_id' => $rx3Id, 'medicine' => 'Meloxicam 1.5mg/ml oral suspension', 'dosage' => '0.6ml (3mg)', 'frequency' => 'SID', 'duration' => '5 days', 'instructions' => 'With food. Stop if GI signs appear.', 'created_at' => Carbon::now()->subWeeks(2), 'updated_at' => Carbon::now()->subWeeks(2)],
    ['prescription_id' => $rx3Id, 'medicine' => 'Tramadol 50mg', 'dosage' => '1 tab', 'frequency' => 'BID', 'duration' => '3 days', 'instructions' => 'For pain. May cause mild sedation.', 'created_at' => Carbon::now()->subWeeks(2), 'updated_at' => Carbon::now()->subWeeks(2)],
]);

echo "Past Visit 3 created (Ear recheck + sprain, 2 weeks ago) — Appt #$appt3Id\n";


// ── 5. CURRENT Visit — Recurring GI + skin worsening + lameness recheck ──
$appt4Id = DB::table('appointments')->insertGetId([
    'clinic_id' => $clinicId,
    'vet_id' => $vetId,
    'pet_parent_id' => $petParentId,
    'pet_id' => $petId,
    'weight' => 28.0,
    'pet_age_at_visit' => '3y 9m',
    'scheduled_at' => Carbon::now(),
    'status' => 'in_consultation',
    'checked_in_at' => Carbon::now()->subMinutes(10),
    'consultation_started_at' => Carbon::now()->subMinutes(5),
    'created_at' => now(),
    'updated_at' => now(),
]);

DB::table('case_sheets')->insert([
    'appointment_id' => $appt4Id,
    'presenting_complaint' => 'Vomiting returned (2 episodes today morning). Loose stools for 2 days. Skin itching worsened — scratching constantly at axilla, groin, and paws. Right forelimb lameness resolved but mild stiffness persists after exercise. Weight loss noticed by owner.',
    'history' => 'GI signs recurring — similar to episode 6 weeks ago. Diet has been consistent (home-cooked chicken + rice). No garbage access this time. Skin itching progressively worsening over past month — axillary, inguinal, and interdigital areas. Owner reports foot licking and face rubbing. Lameness mostly resolved but stiff after walks. Lost ~1.2 kg since last visit.',
    'clinical_examination' => 'Mild dehydration. Skin: bilateral axillary erythema with excoriations, inguinal erythema, interdigital erythema and brown staining on all four paws (saliva staining from licking). No secondary pyoderma. Ears: mild bilateral erythema returning. Abdomen: mildly uncomfortable on deep palpation, no masses. Right carpus: no swelling, full ROM, mild pain on extreme flexion only.',
    'temperature' => '102.8',
    'heart_rate' => '104',
    'respiratory_rate' => '26',
    'capillary_refill_time' => '2 sec',
    'mucous_membrane' => 'Pink, slightly tacky',
    'hydration_status' => '4-5% dehydrated',
    'lymph_nodes' => 'Submandibular mildly enlarged bilaterally',
    'body_condition_score' => '4/9',
    'pain_score' => '2/10',
    'differentials' => 'Canine atopic dermatitis with secondary GI involvement (food allergy component?), IBD, Recurring gastroenteritis, Food-responsive enteropathy, Adverse food reaction with cutaneous and GI manifestations',
    'diagnosis' => 'Suspected canine atopic dermatitis with concurrent food-responsive enteropathy. Weight loss concerning — rule out IBD or other chronic GI disease.',
    'treatment_given' => 'IV fluid therapy initiated (Ringer Lactate 250ml). Maropitant injection for vomiting. Prednisolone injection for acute allergic flare.',
    'procedures_done' => 'IV catheterization, skin scraping (negative for Demodex/Sarcoptes), Wood lamp exam (negative)',
    'further_plan' => 'Recommend CBC, serum biochemistry, and specific IgE allergy panel. Start elimination diet trial (hydrolyzed protein) for 8 weeks. If GI signs persist, consider abdominal ultrasound and GI biopsy. Discuss long-term atopy management — Apoquel vs Cytopoint.',
    'advice' => 'Start hydrolyzed protein diet (Royal Canin Hypoallergenic or Hills z/d) exclusively — no treats, no table food, no flavored medications. Keep a food diary. Avoid walking on grass during pollen season. Use gentle hypoallergenic shampoo weekly for skin. Return in 1 week for blood work review.',
    'created_at' => now(),
    'updated_at' => now(),
]);

// Current visit treatments
DB::table('appointment_treatments')->insert([
    ['appointment_id' => $appt4Id, 'drug_generic_id' => 23, 'dose_mg' => 28, 'dose_volume_ml' => 1.4, 'route' => 'SC', 'created_at' => now(), 'updated_at' => now()], // Maropitant
    ['appointment_id' => $appt4Id, 'drug_generic_id' => 17, 'dose_mg' => 14, 'dose_volume_ml' => 0.7, 'route' => 'IV', 'created_at' => now(), 'updated_at' => now()], // Prednisolone
    ['appointment_id' => $appt4Id, 'drug_generic_id' => 19, 'dose_mg' => 280, 'dose_volume_ml' => null, 'route' => 'IV', 'created_at' => now(), 'updated_at' => now()], // Metronidazole
]);

// Current visit prescription
$rx4Id = DB::table('prescriptions')->insertGetId([
    'appointment_id' => $appt4Id,
    'notes' => 'Start elimination diet. Blood work to follow.',
    'created_at' => now(),
    'updated_at' => now(),
]);

DB::table('prescription_items')->insert([
    ['prescription_id' => $rx4Id, 'medicine' => 'Prednisolone 5mg', 'dosage' => '2 tabs (10mg)', 'frequency' => 'SID', 'duration' => '5 days then taper', 'instructions' => 'With food. 2 tabs x 5 days, then 1 tab x 5 days, then stop.', 'created_at' => now(), 'updated_at' => now()],
    ['prescription_id' => $rx4Id, 'medicine' => 'Metronidazole 200mg', 'dosage' => '1.5 tabs (300mg)', 'frequency' => 'BID', 'duration' => '7 days', 'instructions' => 'After food', 'created_at' => now(), 'updated_at' => now()],
    ['prescription_id' => $rx4Id, 'medicine' => 'Ondansetron 4mg', 'dosage' => '1 tab', 'frequency' => 'BID', 'duration' => '3 days', 'instructions' => '30 min before food', 'created_at' => now(), 'updated_at' => now()],
    ['prescription_id' => $rx4Id, 'medicine' => 'Pantoprazole 40mg', 'dosage' => '0.5 tab', 'frequency' => 'SID', 'duration' => '7 days', 'instructions' => 'Before breakfast', 'created_at' => now(), 'updated_at' => now()],
    ['prescription_id' => $rx4Id, 'medicine' => 'Cetirizine 10mg', 'dosage' => '1 tab', 'frequency' => 'SID', 'duration' => '14 days', 'instructions' => 'Evening. For itching.', 'created_at' => now(), 'updated_at' => now()],
]);

echo "Current Visit created (Recurring GI + atopy flare, today) — Appt #$appt4Id\n";
echo "\n✅ Done! Go to this appointment in vet panel to test AI features.\n";
echo "Pet: Max (Golden Retriever, 3y 9m, 28kg)\n";
echo "4 visits total, rich history for Senior Vet Guidance to analyze.\n";
