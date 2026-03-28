<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'vaccine' to inventory_items.item_type
        DB::statement("ALTER TABLE `inventory_items` MODIFY `item_type` ENUM('drug','consumable','surgical','product','vaccine') DEFAULT 'drug'");

        // Add 'vaccine' to price_list_items.item_type
        DB::statement("ALTER TABLE `price_list_items` MODIFY `item_type` ENUM('service','drug','consumable','surgical','product','vaccine') DEFAULT 'service'");

        // Add 'per_dose' to billing_type for vaccines
        DB::statement("ALTER TABLE `price_list_items` MODIFY `billing_type` ENUM('fixed','per_ml','per_vial','per_tablet','per_unit','per_strip','per_piece','per_sachet','per_tube','per_dose') DEFAULT 'fixed'");

        // Add 'dose' to package_type for vaccines in inventory
        DB::statement("ALTER TABLE `inventory_items` MODIFY `package_type` ENUM('tablet','capsule','injection','vial','fluid','bottle','strip','packet','tube','piece','sachet','dose') DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `inventory_items` MODIFY `item_type` ENUM('drug','consumable','surgical','product') DEFAULT 'drug'");
        DB::statement("ALTER TABLE `price_list_items` MODIFY `item_type` ENUM('service','drug','consumable','surgical','product') DEFAULT 'service'");
        DB::statement("ALTER TABLE `price_list_items` MODIFY `billing_type` ENUM('fixed','per_ml','per_vial','per_tablet','per_unit','per_strip','per_piece','per_sachet','per_tube') DEFAULT 'fixed'");
        DB::statement("ALTER TABLE `inventory_items` MODIFY `package_type` ENUM('tablet','capsule','injection','vial','fluid','bottle','strip','packet','tube','piece','sachet') DEFAULT NULL");
    }
};
