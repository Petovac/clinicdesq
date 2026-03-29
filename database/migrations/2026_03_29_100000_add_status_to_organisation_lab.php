<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add status workflow to organisation_lab pivot
        if (!Schema::hasColumn('organisation_lab', 'status')) {
            Schema::table('organisation_lab', function (Blueprint $table) {
                $table->enum('status', ['pending', 'accepted', 'rejected'])->default('accepted')->after('external_lab_id');
                $table->timestamp('responded_at')->nullable()->after('is_active');
            });
        }

        // Create clinic_external_lab pivot (clinic-level lab assignments)
        if (!Schema::hasTable('clinic_external_lab')) {
            Schema::create('clinic_external_lab', function (Blueprint $table) {
                $table->id();
                $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
                $table->foreignId('external_lab_id')->constrained()->cascadeOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['clinic_id', 'external_lab_id']);
            });
        }

        // Add external_lab_id to lab_order_tests if missing
        if (!Schema::hasColumn('lab_order_tests', 'external_lab_id')) {
            Schema::table('lab_order_tests', function (Blueprint $table) {
                $table->foreignId('external_lab_id')->nullable()->after('external_lab_test_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_external_lab');

        if (Schema::hasColumn('organisation_lab', 'status')) {
            Schema::table('organisation_lab', function (Blueprint $table) {
                $table->dropColumn(['status', 'responded_at']);
            });
        }

        if (Schema::hasColumn('lab_order_tests', 'external_lab_id')) {
            Schema::table('lab_order_tests', function (Blueprint $table) {
                $table->dropColumn('external_lab_id');
            });
        }
    }
};
