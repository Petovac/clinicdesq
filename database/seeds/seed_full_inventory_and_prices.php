<?php

use App\Models\InventoryItem;
use App\Models\InventoryBatch;
use App\Models\InventoryMovement;

$orgId = 1;
$userId = 2;

// ─── NEW INVENTORY ITEMS ───
$newItems = [
    // Pet Food
    ['name' => 'Royal Canin Maxi Adult 4kg',      'item_type' => 'product', 'package_type' => 'bag',    'unit_volume_ml' => 4,     'pack_unit' => 'kg',  'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Royal Canin Kitten 2kg',           'item_type' => 'product', 'package_type' => 'bag',    'unit_volume_ml' => 2,     'pack_unit' => 'kg',  'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Hills Science Diet Adult 3kg',     'item_type' => 'product', 'package_type' => 'bag',    'unit_volume_ml' => 3,     'pack_unit' => 'kg',  'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Pedigree Adult Chicken 3kg',       'item_type' => 'product', 'package_type' => 'bag',    'unit_volume_ml' => 3,     'pack_unit' => 'kg',  'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Whiskas Adult Tuna 3kg',           'item_type' => 'product', 'package_type' => 'bag',    'unit_volume_ml' => 3,     'pack_unit' => 'kg',  'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Royal Canin Gastrointestinal 2kg', 'item_type' => 'product', 'package_type' => 'bag',    'unit_volume_ml' => 2,     'pack_unit' => 'kg',  'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Royal Canin Recovery Can 195g',    'item_type' => 'product', 'package_type' => 'canister','unit_volume_ml' => 195,   'pack_unit' => 'gm',  'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Drools Chicken Adult 3kg',         'item_type' => 'product', 'package_type' => 'bag',    'unit_volume_ml' => 3,     'pack_unit' => 'kg',  'strength_value' => null, 'strength_unit' => null],

    // Pet Treats
    ['name' => 'Pedigree Dentastix Medium 7pk',    'item_type' => 'product', 'package_type' => 'packet', 'unit_volume_ml' => 7,     'pack_unit' => 'pcs', 'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Himalaya Healthy Treats Puppy',    'item_type' => 'product', 'package_type' => 'packet', 'unit_volume_ml' => 1,     'pack_unit' => 'pcs', 'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Dogsee Chew Bars',                 'item_type' => 'product', 'package_type' => 'packet', 'unit_volume_ml' => 1,     'pack_unit' => 'pcs', 'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Temptations Cat Treats Chicken',   'item_type' => 'product', 'package_type' => 'packet', 'unit_volume_ml' => 85,    'pack_unit' => 'gm',  'strength_value' => null, 'strength_unit' => null],

    // Pet Toys
    ['name' => 'Kong Classic Dog Toy Medium',      'item_type' => 'product', 'package_type' => 'piece',  'unit_volume_ml' => 1,     'pack_unit' => 'pcs', 'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Tennis Ball 3 Pack',               'item_type' => 'product', 'package_type' => 'packet', 'unit_volume_ml' => 3,     'pack_unit' => 'pcs', 'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Rope Chew Toy',                    'item_type' => 'product', 'package_type' => 'piece',  'unit_volume_ml' => 1,     'pack_unit' => 'pcs', 'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Squeaky Bone Toy',                 'item_type' => 'product', 'package_type' => 'piece',  'unit_volume_ml' => 1,     'pack_unit' => 'pcs', 'strength_value' => null, 'strength_unit' => null],

    // Grooming / Shampoo
    ['name' => 'Himalaya Erina EP Shampoo 200ml',  'item_type' => 'product', 'package_type' => 'bottle', 'unit_volume_ml' => 200,   'pack_unit' => 'ml', 'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Beaphar Anti-Tick Shampoo 250ml',  'item_type' => 'product', 'package_type' => 'bottle', 'unit_volume_ml' => 250,   'pack_unit' => 'ml', 'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Petkin Ear Wipes 30ct',            'item_type' => 'consumable','package_type' => 'packet','unit_volume_ml' => 30,    'pack_unit' => 'pcs','strength_value' => null, 'strength_unit' => null],

    // Consumables
    ['name' => 'Disposable Syringes 5ml (Box 100)','item_type' => 'consumable','package_type' => 'box',  'unit_volume_ml' => 100,   'pack_unit' => 'pcs','strength_value' => null, 'strength_unit' => null],
    ['name' => 'IV Catheter 22G',                  'item_type' => 'consumable','package_type' => 'piece', 'unit_volume_ml' => 1,     'pack_unit' => 'pcs','strength_value' => null, 'strength_unit' => null],
    ['name' => 'Surgical Gloves (Box 100)',        'item_type' => 'surgical',  'package_type' => 'box',  'unit_volume_ml' => 100,   'pack_unit' => 'pcs','strength_value' => null, 'strength_unit' => null],
    ['name' => 'Cotton Roll 500g',                 'item_type' => 'consumable','package_type' => 'roll',  'unit_volume_ml' => 500,   'pack_unit' => 'gm', 'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Micropore Tape 1 inch',            'item_type' => 'consumable','package_type' => 'roll',  'unit_volume_ml' => 1,     'pack_unit' => 'pcs','strength_value' => null, 'strength_unit' => null],
    ['name' => 'Crepe Bandage 6 inch',             'item_type' => 'consumable','package_type' => 'roll',  'unit_volume_ml' => 1,     'pack_unit' => 'pcs','strength_value' => null, 'strength_unit' => null],
    ['name' => 'Elizabethan Collar Medium',        'item_type' => 'consumable','package_type' => 'piece', 'unit_volume_ml' => 1,     'pack_unit' => 'pcs','strength_value' => null, 'strength_unit' => null],

    // Supplements
    ['name' => 'Nutri-Coat Advance 200ml',        'item_type' => 'product', 'package_type' => 'bottle',  'unit_volume_ml' => 200,   'pack_unit' => 'ml', 'strength_value' => null, 'strength_unit' => null],
    ['name' => 'Petvit Multivitamin 60 tabs',     'item_type' => 'product', 'package_type' => 'bottle',  'unit_volume_ml' => 60,    'pack_unit' => 'tabs','strength_value' => null, 'strength_unit' => null],
    ['name' => 'Calcium Bone Supplement 30 tabs',  'item_type' => 'product', 'package_type' => 'bottle',  'unit_volume_ml' => 30,    'pack_unit' => 'tabs','strength_value' => null, 'strength_unit' => null],

    // Anti-parasite
    ['name' => 'Frontline Plus Dog 10-20kg',       'item_type' => 'product', 'package_type' => 'dose',   'unit_volume_ml' => 1,     'pack_unit' => 'dose','strength_value' => null, 'strength_unit' => null],
    ['name' => 'Nexgard Spectra 7.5-15kg',        'item_type' => 'product', 'package_type' => 'tablet',  'unit_volume_ml' => 1,     'pack_unit' => 'tabs','strength_value' => null, 'strength_unit' => null],
    ['name' => 'Simparica Trio 5-10kg',            'item_type' => 'product', 'package_type' => 'tablet',  'unit_volume_ml' => 1,     'pack_unit' => 'tabs','strength_value' => null, 'strength_unit' => null],
];

$itemCount = 0;
foreach ($newItems as $data) {
    $exists = InventoryItem::where('organisation_id', $orgId)
        ->where('name', $data['name'])
        ->exists();
    if ($exists) continue;

    InventoryItem::create(array_merge($data, [
        'organisation_id' => $orgId,
        'track_inventory' => 1,
        'is_multi_use' => 0,
    ]));
    $itemCount++;
}
echo "Created {$itemCount} new inventory items.\n";

// ─── STOCK ALL ITEMS ───
$allItems = InventoryItem::where('organisation_id', $orgId)->get();
$stockCount = 0;

foreach ($allItems as $item) {
    $existing = InventoryBatch::where('inventory_item_id', $item->id)
        ->whereNull('clinic_id')
        ->sum('quantity');
    if ($existing > 0) continue;

    $batch = InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'clinic_id' => null,
        'batch_number' => 'TEST-001',
        'expiry_date' => '2027-12-31',
        'quantity' => 100,
        'purchase_price' => 10.00,
        'created_by' => $userId,
    ]);

    InventoryMovement::create([
        'clinic_id' => 0,
        'inventory_item_id' => $item->id,
        'inventory_batch_id' => $batch->id,
        'quantity' => 100,
        'movement_type' => 'purchase',
        'notes' => 'Test seed - batch TEST-001',
        'created_by' => $userId,
    ]);
    $stockCount++;
}
echo "Stocked {$stockCount} items with qty 100.\n";

// ─── PRICE LIST ───
$priceList = DB::table('price_lists')->where('organisation_id', $orgId)->first();
if (!$priceList) {
    $priceListId = DB::table('price_lists')->insertGetId([
        'organisation_id' => $orgId,
        'name' => 'Standard Price List',
        'is_active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created price list 'Standard Price List'.\n";
} else {
    $priceListId = $priceList->id;
    echo "Using existing price list '{$priceList->name}'.\n";
}

// Define selling prices per item type
$prices = [
    // Drugs — margin ~3-5x on purchase
    'drug' => function($item) {
        $base = match(true) {
            str_contains(strtolower($item->name), 'apoquel') => 85,
            str_contains(strtolower($item->name), 'cerenia') => 120,
            str_contains(strtolower($item->name), 'tramadol') => 15,
            str_contains(strtolower($item->name), 'intacef') => 45,
            str_contains(strtolower($item->name), 'envas') => 8,
            str_contains(strtolower($item->name), 'dexona') => 5,
            str_contains(strtolower($item->name), 'avil') => 10,
            str_contains(strtolower($item->name), 'bayrocin') => 12,
            str_contains(strtolower($item->name), 'enrovet') => 15,
            str_contains(strtolower($item->name), 'emeset') => 12,
            str_contains(strtolower($item->name), 'melonex') => 8,
            str_contains(strtolower($item->name), 'pantocid') => 18,
            str_contains(strtolower($item->name), 'metrogyl') => 25,
            str_contains(strtolower($item->name), 'nac') => 30,
            str_contains(strtolower($item->name), 'buprenorphine') => 150,
            str_contains(strtolower($item->name), 'prednisolone') || str_contains(strtolower($item->name), 'prednisolo') => 35,
            str_contains(strtolower($item->name), 'enroflox') => 20,
            default => 25,
        };
        return $base;
    },
    // Products
    'product' => function($item) {
        return match(true) {
            str_contains(strtolower($item->name), 'royal canin maxi') => 1850,
            str_contains(strtolower($item->name), 'royal canin kitten') => 1200,
            str_contains(strtolower($item->name), 'royal canin gastro') => 1650,
            str_contains(strtolower($item->name), 'royal canin recovery') => 350,
            str_contains(strtolower($item->name), 'hills') => 1600,
            str_contains(strtolower($item->name), 'pedigree adult') => 650,
            str_contains(strtolower($item->name), 'whiskas') => 750,
            str_contains(strtolower($item->name), 'drools') => 550,
            str_contains(strtolower($item->name), 'dentastix') => 180,
            str_contains(strtolower($item->name), 'himalaya healthy') => 120,
            str_contains(strtolower($item->name), 'dogsee') => 250,
            str_contains(strtolower($item->name), 'temptations') => 150,
            str_contains(strtolower($item->name), 'kong') => 650,
            str_contains(strtolower($item->name), 'tennis ball') => 180,
            str_contains(strtolower($item->name), 'rope chew') => 150,
            str_contains(strtolower($item->name), 'squeaky') => 120,
            str_contains(strtolower($item->name), 'erina') => 250,
            str_contains(strtolower($item->name), 'beaphar') => 450,
            str_contains(strtolower($item->name), 'nutri-coat') => 350,
            str_contains(strtolower($item->name), 'petvit') => 280,
            str_contains(strtolower($item->name), 'calcium bone') => 220,
            str_contains(strtolower($item->name), 'frontline') => 650,
            str_contains(strtolower($item->name), 'nexgard') => 850,
            str_contains(strtolower($item->name), 'simparica') => 750,
            default => 200,
        };
    },
    // Consumables
    'consumable' => function($item) {
        return match(true) {
            str_contains(strtolower($item->name), '5d') => 45,
            str_contains(strtolower($item->name), 'dns') => 50,
            str_contains(strtolower($item->name), 'normal saline') => 40,
            str_contains(strtolower($item->name), 'ringers') => 55,
            str_contains(strtolower($item->name), 'syringe') => 350,
            str_contains(strtolower($item->name), 'catheter') => 45,
            str_contains(strtolower($item->name), 'cotton') => 85,
            str_contains(strtolower($item->name), 'micropore') => 35,
            str_contains(strtolower($item->name), 'crepe') => 30,
            str_contains(strtolower($item->name), 'elizabethan') => 180,
            str_contains(strtolower($item->name), 'petkin') => 350,
            default => 50,
        };
    },
    'surgical' => function($item) {
        return match(true) {
            str_contains(strtolower($item->name), 'gloves') => 450,
            default => 100,
        };
    },
    'vaccine' => function($item) { return 500; },
];

// Check if price_list_items table has inventory_item_id column
$hasInvCol = Schema::hasColumn('price_list_items', 'inventory_item_id');

$priceCount = 0;
foreach ($allItems as $item) {
    // Check if already in price list
    $exists = DB::table('price_list_items')
        ->where('price_list_id', $priceListId)
        ->where('item_name', $item->name)
        ->exists();
    if ($exists) continue;

    $priceFn = $prices[$item->item_type] ?? $prices['product'];
    $sellPrice = $priceFn($item);

    $row = [
        'price_list_id' => $priceListId,
        'item_name' => $item->name,
        'item_type' => $item->item_type === 'drug' ? 'product' : 'product',
        'selling_price' => $sellPrice,
        'created_at' => now(),
        'updated_at' => now(),
    ];

    if ($hasInvCol) {
        $row['inventory_item_id'] = $item->id;
    }

    DB::table('price_list_items')->insert($row);
    $priceCount++;
}
echo "Added {$priceCount} items to price list with selling prices.\n";

echo "\nDone! Summary:\n";
echo "- {$itemCount} new items (food, treats, toys, consumables, supplements, anti-parasite)\n";
echo "- {$stockCount} items freshly stocked with qty 100\n";
echo "- {$priceCount} items added to price list\n";
