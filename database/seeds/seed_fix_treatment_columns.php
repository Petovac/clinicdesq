<?php

/**
 * Run on live via: php artisan tinker database/seeds/seed_fix_treatment_columns.php
 *
 * Adds missing columns to appointment_treatments table if not present.
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$table = 'appointment_treatments';

$columns = [
    'drug_generic_id' => "ALTER TABLE `{$table}` ADD COLUMN `drug_generic_id` BIGINT UNSIGNED NULL AFTER `drug_brand_id`",
    'inventory_item_id' => "ALTER TABLE `{$table}` ADD COLUMN `inventory_item_id` BIGINT UNSIGNED NULL AFTER `drug_brand_id`",
    'billing_quantity' => "ALTER TABLE `{$table}` ADD COLUMN `billing_quantity` DECIMAL(10,2) DEFAULT 1 AFTER `route`",
];

foreach ($columns as $col => $sql) {
    if (!Schema::hasColumn($table, $col)) {
        echo "Adding '{$col}' column...\n";
        DB::statement($sql);
        echo "Added.\n";
    } else {
        echo "Column '{$col}' already exists.\n";
    }
}

echo "Done.\n";
