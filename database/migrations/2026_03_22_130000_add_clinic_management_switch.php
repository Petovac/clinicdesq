<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add can_manage_clinic to clinic_vet pivot
        Schema::table('clinic_vet', function (Blueprint $table) {
            $table->boolean('can_manage_clinic')->default(false)->after('role');
        });

        // Link vet to a user account for seamless switching
        Schema::table('vets', function (Blueprint $table) {
            $table->unsignedBigInteger('linked_user_id')->nullable()->after('is_active');
            $table->foreign('linked_user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Reverse link: user to vet
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('linked_vet_id')->nullable()->after('organisation_id');
            $table->foreign('linked_vet_id')->references('id')->on('vets')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['linked_vet_id']);
            $table->dropColumn('linked_vet_id');
        });

        Schema::table('vets', function (Blueprint $table) {
            $table->dropForeign(['linked_user_id']);
            $table->dropColumn('linked_user_id');
        });

        Schema::table('clinic_vet', function (Blueprint $table) {
            $table->dropColumn('can_manage_clinic');
        });
    }
};
