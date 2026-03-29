<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Platform-wide master directory of lab tests (admin-managed)
        if (!Schema::hasTable('lab_test_directory')) {
            Schema::create('lab_test_directory', function (Blueprint $table) {
                $table->id();
                $table->string('name');                         // e.g. "Complete Blood Count (CBC)"
                $table->string('code')->unique();               // e.g. "CBC"
                $table->string('category')->nullable();         // e.g. "hematology"
                $table->string('sample_type')->nullable();      // e.g. "blood"
                $table->string('aliases')->nullable();          // e.g. "hemogram,blood count"
                $table->text('default_parameters')->nullable(); // JSON array
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Clinic-level in-house lab test availability
        // Links to lab_test_directory codes, tracks what each clinic can do in-house
        if (!Schema::hasTable('clinic_lab_tests')) {
            Schema::create('clinic_lab_tests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
                $table->string('test_code');                    // references lab_test_directory.code
                $table->text('parameters')->nullable();         // JSON array of parameters
                $table->decimal('price', 10, 2)->default(0);
                $table->boolean('is_available')->default(true);
                $table->timestamps();

                $table->unique(['clinic_id', 'test_code']);
            });
        }

        // External lab test offerings — what external labs offer with their pricing
        // Different from external_lab_tests (which is org-specific)
        if (!Schema::hasTable('external_lab_offerings')) {
            Schema::create('external_lab_offerings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('external_lab_id')->constrained()->cascadeOnDelete();
                $table->string('test_code');                    // references lab_test_directory.code
                $table->string('test_name')->nullable();
                $table->text('parameters')->nullable();         // JSON array
                $table->decimal('b2b_price', 10, 2)->default(0);
                $table->string('estimated_time')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['external_lab_id', 'test_code']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('external_lab_offerings');
        Schema::dropIfExists('clinic_lab_tests');
        Schema::dropIfExists('lab_test_directory');
    }
};
