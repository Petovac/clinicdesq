<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE organisations MODIFY COLUMN `type` ENUM('single_clinic', 'hospital', 'multi_clinic', 'corporate') NOT NULL DEFAULT 'single_clinic'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE organisations MODIFY COLUMN `type` ENUM('single_clinic', 'corporate') NOT NULL DEFAULT 'single_clinic'");
    }
};
