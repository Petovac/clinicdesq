<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change item_type from ENUM to VARCHAR so visit_fee, procedure, etc. all work
        DB::statement("ALTER TABLE `price_list_items` MODIFY `item_type` VARCHAR(50) NOT NULL DEFAULT 'service'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `price_list_items` MODIFY `item_type` ENUM('service','drug','consumable','surgical','product','vaccine') DEFAULT 'service'");
    }
};
