<?php

/**
 * Run on live via: php artisan tinker database/seeds/seed_procedures.php
 *
 * Seeds common vet procedures/services into the active price list.
 */

use Illuminate\Support\Facades\DB;

$orgId = 1;

// Find active price list
$activeList = DB::table('price_lists')
    ->where('organisation_id', $orgId)
    ->where('is_active', 1)
    ->first();

if (!$activeList) {
    echo "No active price list found for org {$orgId}.\n";
    return;
}

$procedures = [
    // Surgeries
    ['name' => 'Spay (Female)',             'price' => 8000],
    ['name' => 'Neuter (Male)',             'price' => 5000],
    ['name' => 'Caesarean Section',         'price' => 15000],
    ['name' => 'Wound Suturing',            'price' => 2000],
    ['name' => 'Abscess Drainage',          'price' => 1500],
    ['name' => 'Tumor / Lump Removal',      'price' => 8000],
    ['name' => 'Fracture Repair',           'price' => 12000],
    ['name' => 'Ear Hematoma Surgery',      'price' => 4000],
    ['name' => 'Eye Enucleation',           'price' => 6000],
    ['name' => 'Hernia Repair',             'price' => 7000],
    ['name' => 'Amputation',               'price' => 10000],
    ['name' => 'Exploratory Laparotomy',    'price' => 12000],

    // Diagnostics
    ['name' => 'X-Ray',                    'price' => 800],
    ['name' => 'Ultrasound',               'price' => 1200],
    ['name' => 'ECG',                      'price' => 600],
    ['name' => 'Blood Test (CBC)',          'price' => 500],
    ['name' => 'Blood Test (Biochemistry)', 'price' => 800],
    ['name' => 'Urinalysis',               'price' => 400],
    ['name' => 'Skin Scraping',            'price' => 300],
    ['name' => 'Ear Swab Cytology',        'price' => 350],
    ['name' => 'Fine Needle Aspirate (FNA)', 'price' => 500],

    // Dental
    ['name' => 'Dental Cleaning / Scaling', 'price' => 3000],
    ['name' => 'Tooth Extraction',          'price' => 1500],
    ['name' => 'Dental X-Ray',             'price' => 600],

    // Grooming & Care
    ['name' => 'Ear Cleaning',             'price' => 300],
    ['name' => 'Nail Trimming',            'price' => 200],
    ['name' => 'Anal Gland Expression',    'price' => 400],
    ['name' => 'Wound Dressing',           'price' => 500],
    ['name' => 'Bandage Change',           'price' => 300],

    // Other Services
    ['name' => 'Fluid Therapy (IV Drip)',   'price' => 1500],
    ['name' => 'Blood Transfusion',        'price' => 5000],
    ['name' => 'Catheter Placement',       'price' => 800],
    ['name' => 'Microchip Implantation',   'price' => 1500],
    ['name' => 'Euthanasia',               'price' => 2000],
    ['name' => 'Hospitalization (per day)', 'price' => 1500],
    ['name' => 'ICU Care (per day)',        'price' => 3000],
    ['name' => 'Boarding (per day)',        'price' => 800],
];

$added = 0;
foreach ($procedures as $proc) {
    // Check if already exists
    $exists = DB::table('price_list_items')
        ->where('price_list_id', $activeList->id)
        ->where('name', $proc['name'])
        ->exists();

    if ($exists) continue;

    DB::table('price_list_items')->insert([
        'price_list_id' => $activeList->id,
        'name'          => $proc['name'],
        'item_type'     => 'service',
        'billing_type'  => 'fixed',
        'price'         => $proc['price'],
        'procedure_price' => 0,
        'is_active'     => 1,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);
    $added++;
}

echo "Added {$added} procedures/services to price list '{$activeList->name}'.\n";
echo "These will now appear in Fee Configuration > Procedure & Service Fees.\n";
