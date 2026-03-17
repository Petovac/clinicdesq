<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. External labs (lab organisations)
        Schema::create('external_labs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->enum('type', ['in_house', 'external'])->default('external');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Lab user accounts (for lab portal login)
        Schema::create('lab_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_lab_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // 3. Lab test catalog (master list per clinic)
        Schema::create('lab_test_catalog', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->enum('category', [
                'hematology', 'biochemistry', 'urinalysis', 'serology',
                'cytology', 'histopathology', 'microbiology', 'other',
            ])->default('other');
            $table->enum('sample_type', [
                'blood', 'urine', 'swab', 'tissue', 'fluid', 'other',
            ])->default('blood');
            $table->foreignId('price_list_item_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Lab orders
        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('vet_id');
            $table->foreign('vet_id')->references('id')->on('vets')->cascadeOnDelete();
            $table->foreignId('lab_id')->nullable()->constrained('external_labs')->nullOnDelete();
            $table->enum('routing', ['pending', 'in_house', 'external'])->default('pending');
            $table->enum('status', [
                'ordered', 'routed', 'processing', 'results_uploaded',
                'vet_review', 'approved', 'retest_requested',
            ])->default('ordered');
            $table->enum('priority', ['routine', 'urgent'])->default('routine');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('routed_by')->nullable();
            $table->foreign('routed_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('routed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 5. Individual tests within an order
        Schema::create('lab_order_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_test_catalog_id')->nullable()->constrained('lab_test_catalog')->nullOnDelete();
            $table->string('test_name');
            $table->enum('status', ['pending', 'processing', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Lab results (files + values)
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_order_test_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->text('extracted_text')->nullable();
            $table->text('summary')->nullable();
            $table->foreignId('uploaded_by_lab_id')->nullable()->constrained('lab_users')->nullOnDelete();
            $table->boolean('vet_approved')->default(false);
            $table->timestamp('vet_approved_at')->nullable();
            $table->text('vet_notes')->nullable();
            $table->boolean('retest_requested')->default(false);
            $table->text('retest_reason')->nullable();
            $table->boolean('visible_to_client')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_results');
        Schema::dropIfExists('lab_order_tests');
        Schema::dropIfExists('lab_orders');
        Schema::dropIfExists('lab_test_catalog');
        Schema::dropIfExists('lab_users');
        Schema::dropIfExists('external_labs');
    }
};
