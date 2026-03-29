<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinic_vet', function (Blueprint $table) {
            if (!Schema::hasColumn('clinic_vet', 'is_active')) {
                $table->boolean('is_active')->default(1)->after('vet_id');
            }
            if (!Schema::hasColumn('clinic_vet', 'status')) {
                $table->string('status', 20)->default('active')->after('is_active');
            }
            if (!Schema::hasColumn('clinic_vet', 'can_manage_clinic')) {
                $table->boolean('can_manage_clinic')->default(false)->after('role');
            }
            if (!Schema::hasColumn('clinic_vet', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('can_manage_clinic');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clinic_vet', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'status', 'created_by']);
        });
    }
};
