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
echo "\nChecking injection route fees...\n";

// Create table if it doesn't exist
if (!Schema::hasTable('injection_route_fees')) {
    echo "Creating injection_route_fees table...\n";
    Schema::create('injection_route_fees', function ($table) {
        $table->id();
        $table->unsignedBigInteger('organisation_id');
        $table->string('route_code', 20);
        $table->string('route_name', 80);
        $table->decimal('administration_fee', 10, 2)->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        $table->unique(['organisation_id', 'route_code']);
    });
    echo "Table created.\n";
}

// Create procedure_inventory_items table if needed
if (!Schema::hasTable('procedure_inventory_items')) {
    echo "Creating procedure_inventory_items table...\n";
    Schema::create('procedure_inventory_items', function ($table) {
        $table->id();
        $table->unsignedBigInteger('price_list_item_id');
        $table->unsignedBigInteger('inventory_item_id');
        $table->decimal('quantity_used', 10, 3)->default(1);
        $table->timestamps();
    });
    echo "Table created.\n";
}

$orgId = DB::table('organisations')->value('id');
if ($orgId) {
    $routes = [
        ['route_code' => 'IV',  'route_name' => 'Intravenous (IV)',    'administration_fee' => 300],
        ['route_code' => 'IM',  'route_name' => 'Intramuscular (IM)',  'administration_fee' => 250],
        ['route_code' => 'SC',  'route_name' => 'Subcutaneous (SC)',   'administration_fee' => 200],
        ['route_code' => 'ID',  'route_name' => 'Intradermal (ID)',    'administration_fee' => 200],
        ['route_code' => 'PO',  'route_name' => 'Oral (PO)',           'administration_fee' => 150],
        ['route_code' => 'IO',  'route_name' => 'Intraosseous (IO)',   'administration_fee' => 1000],
        ['route_code' => 'IT',  'route_name' => 'Intrathecal (IT)',    'administration_fee' => 600],
    ];
    $seeded = 0;
    foreach ($routes as $route) {
        $exists = DB::table('injection_route_fees')
            ->where('organisation_id', $orgId)
            ->where('route_code', $route['route_code'])
            ->exists();
        if (!$exists) {
            DB::table('injection_route_fees')->insert(array_merge($route, [
                'organisation_id' => $orgId,
                'is_active'       => true,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]));
            $seeded++;
        }
    }
    echo "Seeded {$seeded} injection route fees.\n";
} else {
    echo "No organisation found, skipping injection routes.\n";
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
