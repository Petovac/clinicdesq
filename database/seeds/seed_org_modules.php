<?php

/**
 * Run on live via: php artisan tinker database/seeds/seed_org_modules.php
 *
 * Adds 'modules' column to organisations table if not present,
 * and sets default modules for all orgs.
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Add column if not exists
if (!Schema::hasColumn('organisations', 'modules')) {
    echo "Adding 'modules' column to organisations table...\n";
    DB::statement("ALTER TABLE `organisations` ADD COLUMN `modules` JSON NULL");
    echo "Column added.\n";
} else {
    echo "Column 'modules' already exists.\n";
}

// Set defaults for all orgs that don't have modules set
$updated = DB::table('organisations')
    ->whereNull('modules')
    ->update(['modules' => json_encode(['inventory' => true, 'billing' => true, 'lab' => true])]);

echo "Updated {$updated} organisations with default modules.\n";
echo "Done.\n";
