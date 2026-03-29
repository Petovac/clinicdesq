<?php

/**
 * Run on live via: php artisan tinker database/seeds/seed_fix_generic_links.php
 *
 * Backfills drug_generic_id on inventory_items that have a drug_brand_id
 * but are missing the generic link. Also tries to match by generic_name.
 */

use Illuminate\Support\Facades\DB;

$fixed = 0;

// 1. Fix items that have drug_brand_id but no drug_generic_id
$items = DB::table('inventory_items')
    ->where('item_type', 'drug')
    ->whereNull('drug_generic_id')
    ->whereNotNull('drug_brand_id')
    ->get();

foreach ($items as $item) {
    $brand = DB::table('drug_brands')->find($item->drug_brand_id);
    if ($brand && $brand->generic_id) {
        DB::table('inventory_items')
            ->where('id', $item->id)
            ->update(['drug_generic_id' => $brand->generic_id]);
        $fixed++;
        echo "Fixed via brand: {$item->name} -> generic_id {$brand->generic_id}\n";
    }
}

// 2. Fix items that have generic_name but no drug_generic_id
$items2 = DB::table('inventory_items')
    ->where('item_type', 'drug')
    ->whereNull('drug_generic_id')
    ->whereNotNull('generic_name')
    ->where('generic_name', '!=', '')
    ->get();

foreach ($items2 as $item) {
    $generic = DB::table('drug_generics')
        ->where('name', $item->generic_name)
        ->first();
    if ($generic) {
        DB::table('inventory_items')
            ->where('id', $item->id)
            ->update(['drug_generic_id' => $generic->id]);
        $fixed++;
        echo "Fixed via name: {$item->name} (generic: {$item->generic_name}) -> generic_id {$generic->id}\n";
    }
}

echo "\nFixed {$fixed} inventory items with missing drug_generic_id.\n";
echo "Done.\n";
