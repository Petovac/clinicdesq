<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Expand inventory_items.item_type: add 'surgical' and 'product'
        DB::statement("ALTER TABLE `inventory_items` MODIFY `item_type` ENUM('drug','consumable','surgical','product') DEFAULT 'drug'");

        // 2. The column may have been changed to VARCHAR or have non-enum values.
        //    First make it VARCHAR to safely clean up, then convert back to ENUM.
        DB::statement("ALTER TABLE `price_list_items` MODIFY `item_type` VARCHAR(50) DEFAULT 'service'");

        // 3. Normalize non-standard values
        DB::statement("UPDATE `price_list_items` SET `item_type` = 'service' WHERE `item_type` IN ('visit_fee','procedure','')");
        DB::statement("UPDATE `price_list_items` SET `item_type` = 'drug' WHERE `item_type` = 'treatment'");

        // 4. Now safely set the final enum
        DB::statement("ALTER TABLE `price_list_items` MODIFY `item_type` ENUM('service','drug','consumable','surgical','product') DEFAULT 'service'");

        // 5. Expand billing_type
        DB::statement("ALTER TABLE `price_list_items` MODIFY `billing_type` ENUM('fixed','per_ml','per_vial','per_tablet','per_unit','per_strip','per_piece','per_sachet','per_tube') DEFAULT 'fixed'");
    }

    public function down(): void
    {
        // Add 'treatment' back temporarily
        DB::statement("ALTER TABLE `price_list_items` MODIFY `item_type` ENUM('service','treatment','drug','consumable','surgical','product') DEFAULT 'service'");
        DB::statement("UPDATE `price_list_items` SET `item_type` = 'treatment' WHERE `item_type` = 'drug'");
        DB::statement("ALTER TABLE `price_list_items` MODIFY `item_type` ENUM('service','treatment','product') DEFAULT 'service'");

        DB::statement("ALTER TABLE `inventory_items` MODIFY `item_type` ENUM('drug','consumable') DEFAULT 'drug'");
        DB::statement("ALTER TABLE `price_list_items` MODIFY `billing_type` ENUM('fixed','per_ml','per_vial','per_tablet','per_unit') DEFAULT 'fixed'");
    }
};
