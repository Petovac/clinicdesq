<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipd_treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ipd_admission_id')->constrained()->onDelete('cascade');

            $table->string('treated_by_type');    // 'user' or 'vet'
            $table->unsignedBigInteger('treated_by_id');

            $table->foreignId('price_list_item_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('drug_generic_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('drug_brand_id')->nullable()->constrained('drug_brands')->onDelete('set null');
            $table->unsignedBigInteger('inventory_item_id')->nullable();

            $table->decimal('dose_mg', 10, 3)->nullable();
            $table->decimal('dose_volume_ml', 10, 3)->nullable();
            $table->string('route')->nullable();
            $table->decimal('billing_quantity', 10, 3)->nullable();

            $table->enum('treatment_type', ['injection', 'procedure', 'medication', 'fluid'])->default('medication');
            $table->text('notes')->nullable();
            $table->dateTime('administered_at');

            $table->timestamps();
            $table->index('ipd_admission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_treatments');
    }
};
