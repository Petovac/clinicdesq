<?php

$orgId = 1;
$userId = 2;
$items = \App\Models\InventoryItem::where('organisation_id', $orgId)->get();
$count = 0;

foreach ($items as $item) {
    $existing = \App\Models\InventoryBatch::where('inventory_item_id', $item->id)
        ->whereNull('clinic_id')
        ->sum('quantity');

    if ($existing > 0) {
        continue;
    }

    $batch = \App\Models\InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'clinic_id' => null,
        'batch_number' => 'TEST-001',
        'expiry_date' => '2027-12-31',
        'quantity' => 100,
        'purchase_price' => 10.00,
        'created_by' => $userId,
    ]);

    \App\Models\InventoryMovement::create([
        'clinic_id' => 0,
        'inventory_item_id' => $item->id,
        'inventory_batch_id' => $batch->id,
        'quantity' => 100,
        'movement_type' => 'purchase',
        'notes' => 'Test seed - batch TEST-001',
        'created_by' => $userId,
    ]);

    $count++;
}

echo "{$count} items stocked with qty 100, batch TEST-001, expiry 2027-12-31\n";
