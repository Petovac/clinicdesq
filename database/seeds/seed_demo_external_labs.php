<?php
/**
 * Seed demo external labs, lab users, and their test offerings.
 * Run: php artisan tinker database/seeds/seed_demo_external_labs.php
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// ─── Demo External Labs ─────────────────────────────────────────
$labs = [
    ['name'=>'Prolife Diagnostics','phone'=>'9876543210','email'=>'info@prolifediag.com','address'=>'HSR Layout, Sector 2','city'=>'Bangalore','state'=>'Karnataka','description'=>'Full-service veterinary diagnostic lab. Hematology, biochemistry, histopathology, microbiology.'],
    ['name'=>'PetScan Veterinary Lab','phone'=>'9887766554','email'=>'lab@petscan.in','address'=>'Indiranagar, 12th Main','city'=>'Bangalore','state'=>'Karnataka','description'=>'Advanced imaging and pathology services for small animals.'],
    ['name'=>'VetPath India','phone'=>'9900112233','email'=>'support@vetpath.in','address'=>'Koramangala, 5th Block','city'=>'Bangalore','state'=>'Karnataka','description'=>'Specializing in cytology, histopathology, and immunohistochemistry.'],
    ['name'=>'Antech Vet Diagnostics','phone'=>'9811223344','email'=>'blr@antech.co.in','address'=>'Whitefield, ITPL Road','city'=>'Bangalore','state'=>'Karnataka','description'=>'Pan-India veterinary reference lab. 200+ tests. Same-day reports for routine panels.'],
    ['name'=>'MicroVet Labs','phone'=>'9944556677','email'=>'info@microvet.in','address'=>'Rajajinagar, 2nd Block','city'=>'Bangalore','state'=>'Karnataka','description'=>'Microbiology, culture sensitivity, PCR testing for infectious diseases.'],
    ['name'=>'LabTail Diagnostics','phone'=>'9955667788','email'=>'hello@labtail.com','address'=>'JP Nagar, 6th Phase','city'=>'Bangalore','state'=>'Karnataka','description'=>'Affordable vet diagnostics. CBC, LFT, KFT, electrolytes, urinalysis.'],
    ['name'=>'PawDiagnostics Chennai','phone'=>'9876001122','email'=>'info@pawdiag.com','address'=>'T Nagar, Usman Road','city'=>'Chennai','state'=>'Tamil Nadu','description'=>'Chennai-based veterinary diagnostic center.'],
    ['name'=>'VetLab Mumbai','phone'=>'9822334455','email'=>'info@vetlabmumbai.com','address'=>'Andheri West','city'=>'Mumbai','state'=>'Maharashtra','description'=>'Mumbai premier veterinary lab.'],
];

$labIdMap = []; // old_name => new_id

foreach ($labs as $lab) {
    $existing = DB::table('external_labs')->where('email', $lab['email'])->first();
    if ($existing) {
        $labIdMap[$lab['name']] = $existing->id;
        echo "  Exists: {$lab['name']} (id: {$existing->id})\n";
    } else {
        $id = DB::table('external_labs')->insertGetId(array_merge($lab, [
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]));
        $labIdMap[$lab['name']] = $id;
        echo "  Created: {$lab['name']} (id: {$id})\n";
    }
}

echo "\n--- Labs done. Total: " . DB::table('external_labs')->count() . " ---\n\n";

// ─── Lab Users (one admin per lab) ──────────────────────────────
$labUsers = [
    ['lab'=>'Prolife Diagnostics', 'email'=>'prolifediagnostics@test.com', 'name'=>'Lab Admin - Prolife Diagnostics'],
    ['lab'=>'PetScan Veterinary Lab', 'email'=>'petscanveterinarylab@test.com', 'name'=>'Lab Admin - PetScan Veterinary Lab'],
    ['lab'=>'VetPath India', 'email'=>'vetpathindia@test.com', 'name'=>'Lab Admin - VetPath India'],
    ['lab'=>'Antech Vet Diagnostics', 'email'=>'antech@test.com', 'name'=>'Lab Admin - Antech'],
    ['lab'=>'MicroVet Labs', 'email'=>'microvet@test.com', 'name'=>'Lab Admin - MicroVet'],
    ['lab'=>'LabTail Diagnostics', 'email'=>'labtail@test.com', 'name'=>'Lab Admin - LabTail'],
];

foreach ($labUsers as $u) {
    $labId = $labIdMap[$u['lab']] ?? null;
    if (!$labId) continue;

    $exists = DB::table('lab_users')->where('email', $u['email'])->exists();
    if ($exists) {
        echo "  Lab user exists: {$u['email']}\n";
        continue;
    }

    DB::table('lab_users')->insert([
        'external_lab_id' => $labId,
        'role' => 'lab_admin',
        'name' => $u['name'],
        'email' => $u['email'],
        'password' => bcrypt('password123'),
        'phone' => '9000000000',
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "  Created lab user: {$u['email']}\n";
}

echo "\n--- Lab users done. Total: " . DB::table('lab_users')->count() . " ---\n\n";

// ─── Test Offerings (external_lab_offerings) ────────────────────
// Common vet tests with realistic pricing per lab
$testOfferings = [
    // test_code => [lab_name => b2b_price, ...]
    'CBC' => ['Prolife Diagnostics'=>329, 'PetScan Veterinary Lab'=>333, 'VetPath India'=>396, 'Antech Vet Diagnostics'=>319, 'MicroVet Labs'=>322, 'LabTail Diagnostics'=>310],
    'LFT' => ['Prolife Diagnostics'=>480, 'VetPath India'=>475, 'Antech Vet Diagnostics'=>485, 'MicroVet Labs'=>475, 'LabTail Diagnostics'=>450],
    'KFT' => ['Prolife Diagnostics'=>473, 'Antech Vet Diagnostics'=>513, 'LabTail Diagnostics'=>437],
    'UA' => ['Prolife Diagnostics'=>285, 'PetScan Veterinary Lab'=>339, 'VetPath India'=>333, 'Antech Vet Diagnostics'=>342, 'MicroVet Labs'=>303, 'LabTail Diagnostics'=>275],
    'CBC' => ['Prolife Diagnostics'=>329, 'PetScan Veterinary Lab'=>333, 'VetPath India'=>396, 'Antech Vet Diagnostics'=>319, 'MicroVet Labs'=>322, 'LabTail Diagnostics'=>310],
    'FNAC' => ['Prolife Diagnostics'=>637, 'Antech Vet Diagnostics'=>623, 'MicroVet Labs'=>637, 'LabTail Diagnostics'=>693],
    'ELEC' => ['Prolife Diagnostics'=>348, 'Antech Vet Diagnostics'=>428, 'MicroVet Labs'=>344],
    'BSE' => ['VetPath India'=>278, 'MicroVet Labs'=>253, 'LabTail Diagnostics'=>240, 'PetScan Veterinary Lab'=>522],
    'CPLI' => ['PetScan Veterinary Lab'=>1116, 'MicroVet Labs'=>1344],
    'LIPID' => ['PetScan Veterinary Lab'=>570, 'VetPath India'=>510, 'Antech Vet Diagnostics'=>500, 'MicroVet Labs'=>520, 'LabTail Diagnostics'=>460],
    'THYR' => ['PetScan Veterinary Lab'=>840, 'VetPath India'=>912, 'Antech Vet Diagnostics'=>904, 'MicroVet Labs'=>792, 'LabTail Diagnostics'=>792],
    'GLU' => ['PetScan Veterinary Lab'=>149, 'VetPath India'=>134, 'MicroVet Labs'=>165, 'LabTail Diagnostics'=>149],
    'COAG' => ['PetScan Veterinary Lab'=>616, 'VetPath India'=>512, 'Antech Vet Diagnostics'=>523, 'MicroVet Labs'=>589],
    'HISTO' => ['PetScan Veterinary Lab'=>1665, 'VetPath India'=>1680, 'Antech Vet Diagnostics'=>1485, 'MicroVet Labs'=>1605, 'LabTail Diagnostics'=>1290],
    'FUNG' => ['PetScan Veterinary Lab'=>522, 'Antech Vet Diagnostics'=>666, 'MicroVet Labs'=>630, 'LabTail Diagnostics'=>558],
    'CDV' => ['PetScan Veterinary Lab'=>616, 'VetPath India'=>763, 'MicroVet Labs'=>735, 'LabTail Diagnostics'=>602],
    'CS' => ['VetPath India'=>696, 'Antech Vet Diagnostics'=>760, 'MicroVet Labs'=>832],
    'LEPT' => ['PetScan Veterinary Lab'=>837, 'VetPath India'=>873, 'Antech Vet Diagnostics'=>792, 'MicroVet Labs'=>846],
    'CPV' => ['PetScan Veterinary Lab'=>630, 'VetPath India'=>654, 'Antech Vet Diagnostics'=>558, 'MicroVet Labs'=>630, 'LabTail Diagnostics'=>612],
    'EHRL' => ['Antech Vet Diagnostics'=>572, 'MicroVet Labs'=>663, 'LabTail Diagnostics'=>657, 'VetPath India'=>650],
    'FELV' => ['Antech Vet Diagnostics'=>723, 'VetPath India'=>969, 'LabTail Diagnostics'=>842],
];

$created = 0;
$existed = 0;
foreach ($testOfferings as $testCode => $labPrices) {
    foreach ($labPrices as $labName => $price) {
        $labId = $labIdMap[$labName] ?? null;
        if (!$labId) continue;

        $exists = DB::table('external_lab_offerings')
            ->where('external_lab_id', $labId)
            ->where('test_code', $testCode)
            ->exists();

        if ($exists) {
            $existed++;
            continue;
        }

        // Get TAT from directory
        $dirTest = DB::table('lab_test_directory')->where('code', $testCode)->first();

        DB::table('external_lab_offerings')->insert([
            'external_lab_id' => $labId,
            'test_code' => $testCode,
            'b2b_price' => $price,
            'estimated_time' => $dirTest->tat ?? '3 Hrs',
            'container_type' => $dirTest->preferred_sample ?? null,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $created++;
    }
}

echo "Offerings: {$created} created, {$existed} existed\n";
echo "Total offerings: " . DB::table('external_lab_offerings')->count() . "\n";
