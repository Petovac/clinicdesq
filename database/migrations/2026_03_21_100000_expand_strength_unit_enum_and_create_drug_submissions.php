<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Expand strength_unit to include 'dose', 'mcg', 'units'
        DB::statement("ALTER TABLE `inventory_items` MODIFY `strength_unit` ENUM('mg/ml','mg','gm','IU','%','dose','mcg','units') DEFAULT NULL");

        // 2. Create drug_submissions table — org-created generics/brands pending admin approval
        Schema::create('drug_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->unsignedBigInteger('submitted_by'); // user_id

            // What they're submitting
            $table->enum('type', ['generic', 'brand'])->default('brand');

            // Generic fields (for new generic submissions)
            $table->string('generic_name')->nullable();
            $table->string('drug_class')->nullable();
            $table->string('default_dose_unit', 20)->nullable();

            // Brand fields (for new brand submissions)
            $table->unsignedBigInteger('drug_generic_id')->nullable(); // existing generic it belongs to
            $table->string('submitted_generic_name')->nullable(); // if new generic submitted alongside
            $table->string('brand_name')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('form', 50)->nullable(); // tablet, injection, etc.
            $table->decimal('strength_value', 10, 2)->nullable();
            $table->string('strength_unit', 20)->nullable();
            $table->decimal('pack_size', 10, 2)->nullable();
            $table->string('pack_unit', 20)->nullable();

            // Approval workflow
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable(); // admin user_id
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            // If approved, link to the created KB records
            $table->unsignedBigInteger('created_generic_id')->nullable(); // links to drug_generics.id
            $table->unsignedBigInteger('created_brand_id')->nullable(); // links to drug_brands.id

            $table->timestamps();

            $table->foreign('organisation_id')->references('id')->on('organisations')->onDelete('cascade');
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('drug_generic_id')->references('id')->on('drug_generics')->onDelete('set null');

            $table->index(['status', 'created_at']);
            $table->index('organisation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drug_submissions');
        DB::statement("ALTER TABLE `inventory_items` MODIFY `strength_unit` ENUM('mg/ml','mg','gm','IU','%') DEFAULT NULL");
    }
};
