<?php

/**
 * Run on live via: php artisan tinker database/seeds/seed_fix_billing_enums.php
 *
 * Fixes enum columns that block billing:
 * 1. inventory_movements.movement_type — change to VARCHAR
 * 2. bill_items.source — change to VARCHAR
 * 3. bills.status — change to VARCHAR
 * 4. bill_items.status — change to VARCHAR
 * 5. bill_items.price_list_item_id — make nullable
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$fixes = [
    ['inventory_movements', 'movement_type', "VARCHAR(50) NOT NULL DEFAULT 'stock_in'"],
    ['bill_items', 'source', "VARCHAR(50) NOT NULL DEFAULT 'manual'"],
    ['bills', 'status', "VARCHAR(30) NOT NULL DEFAULT 'draft'"],
    ['bills', 'payment_status', "VARCHAR(30) NOT NULL DEFAULT 'unpaid'"],
    ['bill_items', 'status', "VARCHAR(30) NOT NULL DEFAULT 'approved'"],
];

foreach ($fixes as [$table, $col, $def]) {
    if (Schema::hasColumn($table, $col)) {
        echo "Fixing {$table}.{$col} to VARCHAR...\n";
        DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `{$col}` {$def}");
        echo "Done.\n";
    } else {
        echo "Column {$table}.{$col} does not exist, skipping.\n";
    }
}

// Make bill_items.price_list_item_id nullable
if (Schema::hasColumn('bill_items', 'price_list_item_id')) {
    echo "Making bill_items.price_list_item_id nullable...\n";
    DB::statement("ALTER TABLE `bill_items` MODIFY COLUMN `price_list_item_id` BIGINT UNSIGNED NULL");
    echo "Done.\n";
}

echo "\nAll billing columns fixed.\n";
