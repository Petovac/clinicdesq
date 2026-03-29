<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change package_type from ENUM to VARCHAR to support all drug forms
        // (oral_suspension, syrup, drops, ointment, cream, shampoo, gel, spray, powder, etc.)
        DB::statement("ALTER TABLE `inventory_items` MODIFY `package_type` VARCHAR(50) DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `inventory_items` MODIFY `package_type` ENUM('tablet','capsule','injection','vial','fluid','bottle','strip','packet','tube','piece','sachet','dose') DEFAULT NULL");
    }
};
