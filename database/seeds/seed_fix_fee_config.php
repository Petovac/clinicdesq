<?php

/**
 * Run on live via: php artisan tinker database/seeds/seed_fix_fee_config.php
 *
 * 1. Changes price_list_items.item_type from ENUM to VARCHAR(50) so visit_fee works
 * 2. Seeds default injection routes if none exist
 * 3. Seeds common vet procedures into the active price list
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// ──────────────────────────────────────
// 1. Fix item_type enum → VARCHAR
// ──────────────────────────────────────
echo "Checking price_list_items.item_type column type...\n";
$col = DB::select("SHOW COLUMNS FROM price_list_items WHERE Field = 'item_type'");
if ($col && str_contains($col[0]->Type, 'enum')) {
    echo "Converting item_type from ENUM to VARCHAR(50)...\n";
    DB::statement("ALTER TABLE `price_list_items` MODIFY `item_type` VARCHAR(50) NOT NULL DEFAULT 'service'");
    echo "Done.\n";
} else {
    echo "Already VARCHAR, skipping.\n";
}

// ──────────────────────────────────────
// 2. Seed injection routes
// ──────────────────────────────────────
echo "\nChecking injection routes...\n";
$hasRoutes = DB::table('injection_routes')->exists();
if (!$hasRoutes) {
    echo "Seeding default injection routes...\n";
    $routes = [
        ['code' => 'IV',  'name' => 'Intravenous (IV)',    'admin_fee' => 300, 'is_active' => 1],
        ['code' => 'IM',  'name' => 'Intramuscular (IM)',  'admin_fee' => 250, 'is_active' => 1],
        ['code' => 'SC',  'name' => 'Subcutaneous (SC)',   'admin_fee' => 200, 'is_active' => 1],
        ['code' => 'ID',  'name' => 'Intradermal (ID)',    'admin_fee' => 200, 'is_active' => 1],
        ['code' => 'PO',  'name' => 'Oral (PO)',           'admin_fee' => 150, 'is_active' => 1],
        ['code' => 'IO',  'name' => 'Intraosseous (IO)',   'admin_fee' => 1000, 'is_active' => 1],
        ['code' => 'IT',  'name' => 'Intrathecal (IT)',    'admin_fee' => 600, 'is_active' => 1],
    ];
    foreach ($routes as $route) {
        // Check for org_id column
        if (Schema::hasColumn('injection_routes', 'organisation_id')) {
            $orgs = DB::table('organisations')->pluck('id');
            foreach ($orgs as $orgId) {
                DB::table('injection_routes')->updateOrInsert(
                    ['code' => $route['code'], 'organisation_id' => $orgId],
                    array_merge($route, ['organisation_id' => $orgId, 'created_at' => now(), 'updated_at' => now()])
                );
            }
        } else {
            DB::table('injection_routes')->updateOrInsert(
                ['code' => $route['code']],
                array_merge($route, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
    echo "Injection routes seeded.\n";
} else {
    echo "Injection routes already exist, skipping.\n";
}

// ──────────────────────────────────────
// 3. Seed common vet procedures
// ──────────────────────────────────────
echo "\nSeeding procedures into active price list...\n";
$org = DB::table('organisations')->first();
if (!$org) {
    echo "No organisation found. Skipping procedures.\n";
    return;
}

$priceList = DB::table('price_lists')
    ->where('organisation_id', $org->id)
    ->where('is_active', 1)
    ->first();

if (!$priceList) {
    echo "No active price list found. Skipping procedures.\n";
    return;
}

$procedures = [
    ['name' => 'Abscess Drainage',              'price' => 1500],
    ['name' => 'Amputation',                     'price' => 10000],
    ['name' => 'Anal Gland Expression',          'price' => 400],
    ['name' => 'Bandage Change',                 'price' => 300],
    ['name' => 'Blood Test (Biochemistry)',       'price' => 1200],
    ['name' => 'Blood Test (CBC)',               'price' => 600],
    ['name' => 'Blood Transfusion',              'price' => 5000],
    ['name' => 'Boarding (per day)',             'price' => 800],
    ['name' => 'Caesarean Section',              'price' => 15000],
    ['name' => 'Catheter Placement',             'price' => 500],
    ['name' => 'Dental Cleaning / Scaling',      'price' => 3000],
    ['name' => 'Dental X-Ray',                   'price' => 600],
    ['name' => 'Ear Cleaning',                   'price' => 300],
    ['name' => 'Ear Flush (under sedation)',     'price' => 2000],
    ['name' => 'Eye Exam (Ophthalmoscopy)',      'price' => 500],
    ['name' => 'Fluid Therapy (per session)',    'price' => 800],
    ['name' => 'Fracture Repair / Splint',       'price' => 8000],
    ['name' => 'Grooming (Full)',                'price' => 1500],
    ['name' => 'Microchip Implantation',         'price' => 1000],
    ['name' => 'Nail Clipping',                  'price' => 200],
    ['name' => 'Nebulisation (per session)',     'price' => 500],
    ['name' => 'Neutering (Male)',               'price' => 5000],
    ['name' => 'Skin Scraping / Cytology',       'price' => 500],
    ['name' => 'Spay (Female)',                  'price' => 8000],
    ['name' => 'Suture Removal',                 'price' => 300],
    ['name' => 'Ultrasound',                     'price' => 1500],
    ['name' => 'Urinalysis',                     'price' => 400],
    ['name' => 'Wound Dressing',                 'price' => 500],
    ['name' => 'X-Ray',                          'price' => 800],
];

$added = 0;
foreach ($procedures as $proc) {
    $exists = DB::table('price_list_items')
        ->where('price_list_id', $priceList->id)
        ->where('name', $proc['name'])
        ->exists();

    if (!$exists) {
        DB::table('price_list_items')->insert([
            'price_list_id' => $priceList->id,
            'name'          => $proc['name'],
            'item_type'     => 'service',
            'billing_type'  => 'fixed',
            'price'         => $proc['price'],
            'is_active'     => 1,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        $added++;
    }
}

echo "Added {$added} procedures to price list.\n";
echo "Done.\n";
