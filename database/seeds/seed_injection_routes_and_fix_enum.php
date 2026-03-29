<?php

/**
 * Run on live via: php artisan tinker database/seeds/seed_injection_routes_and_fix_enum.php
 *
 * 1. Changes price_list_items.item_type from ENUM to VARCHAR(50)
 * 2. Seeds default injection route fees for the org
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// ── 1. Fix item_type column ──
echo "Fixing price_list_items.item_type to VARCHAR...\n";
DB::statement("ALTER TABLE `price_list_items` MODIFY `item_type` VARCHAR(50) NOT NULL DEFAULT 'service'");
echo "Done.\n\n";

// ── 2. Seed injection route fees ──
$orgId = 1; // live org ID

// Check if table exists
if (!Schema::hasTable('injection_route_fees')) {
    echo "Table injection_route_fees does not exist. Running create...\n";
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

// Check if table procedure_inventory_items exists
if (!Schema::hasTable('procedure_inventory_items')) {
    echo "Table procedure_inventory_items does not exist. Creating...\n";
    Schema::create('procedure_inventory_items', function ($table) {
        $table->id();
        $table->unsignedBigInteger('price_list_item_id');
        $table->unsignedBigInteger('inventory_item_id');
        $table->decimal('quantity_used', 10, 3)->default(1);
        $table->timestamps();
    });
    echo "Table created.\n";
}

$routes = [
    ['route_code' => 'IV',  'route_name' => 'Intravenous (IV)',    'administration_fee' => 0],
    ['route_code' => 'IM',  'route_name' => 'Intramuscular (IM)',  'administration_fee' => 0],
    ['route_code' => 'SC',  'route_name' => 'Subcutaneous (SC)',   'administration_fee' => 0],
    ['route_code' => 'ID',  'route_name' => 'Intradermal (ID)',    'administration_fee' => 0],
    ['route_code' => 'PO',  'route_name' => 'Oral (PO)',           'administration_fee' => 0],
    ['route_code' => 'IO',  'route_name' => 'Intraosseous (IO)',   'administration_fee' => 0],
    ['route_code' => 'IT',  'route_name' => 'Intrathecal (IT)',    'administration_fee' => 0],
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

echo "Seeded {$seeded} injection routes for org {$orgId}.\n";
echo "\nAll done! Visit Fee and Injection Routes should now work.\n";
