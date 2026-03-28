<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vet_ai_credits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vet_id');
            $table->integer('balance')->default(0);
            $table->integer('total_purchased')->default(0);
            $table->integer('total_used')->default(0);
            $table->timestamps();

            $table->foreign('vet_id')->references('id')->on('vets')->onDelete('cascade');
            $table->unique('vet_id');
        });

        Schema::create('vet_ai_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vet_id');
            $table->enum('type', ['purchase', 'deduction', 'bonus', 'refund']);
            $table->integer('credits');
            $table->integer('balance_after');
            $table->string('description');
            $table->string('reference')->nullable(); // e.g. appointment_id, pack name
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->string('ai_feature')->nullable(); // clinical_insights, senior_support, prescription_support, refine
            $table->timestamps();

            $table->foreign('vet_id')->references('id')->on('vets')->onDelete('cascade');
            $table->index(['vet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vet_ai_transactions');
        Schema::dropIfExists('vet_ai_credits');
    }
};
