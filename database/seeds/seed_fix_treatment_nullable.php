<?php

/**
 * Run on live via: php artisan tinker database/seeds/seed_fix_treatment_nullable.php
 *
 * Makes price_list_item_id nullable on appointment_treatments (doctors should be
 * able to add treatments even if no price list item exists for the drug).
 * Also syncs other columns that may differ between local and live.
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$table = 'appointment_treatments';

// 1. Make price_list_item_id nullable
echo "Making price_list_item_id nullable...\n";
DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `price_list_item_id` BIGINT UNSIGNED NULL");
echo "Done.\n";

// 2. Make drug_brand_id nullable if not already
echo "Making drug_brand_id nullable...\n";
DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `drug_brand_id` BIGINT UNSIGNED NULL");
echo "Done.\n";

// 3. Make drug_generic_id nullable if not already
if (Schema::hasColumn($table, 'drug_generic_id')) {
    echo "Making drug_generic_id nullable...\n";
    DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `drug_generic_id` BIGINT UNSIGNED NULL");
    echo "Done.\n";
}

// 4. Make inventory_item_id nullable if not already
if (Schema::hasColumn($table, 'inventory_item_id')) {
    echo "Making inventory_item_id nullable...\n";
    DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `inventory_item_id` BIGINT UNSIGNED NULL");
    echo "Done.\n";
}

// 5. Check for billing_quantity default
if (Schema::hasColumn($table, 'billing_quantity')) {
    echo "Setting billing_quantity default to 1...\n";
    DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `billing_quantity` DECIMAL(10,2) DEFAULT 1");
    echo "Done.\n";
}

echo "\nAll appointment_treatments columns fixed.\n";

// --- Quick schema comparison ---
echo "\n=== Schema comparison: appointment_treatments ===\n";
$columns = DB::select("SHOW COLUMNS FROM `{$table}`");
foreach ($columns as $col) {
    echo sprintf("  %-25s %-20s %s %s\n", $col->Field, $col->Type, $col->Null === 'YES' ? 'NULL' : 'NOT NULL', $col->Default !== null ? "default={$col->Default}" : '');
}
