<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vet_id')->nullable()->constrained('vets')->nullOnDelete();
            $table->foreignId('pet_parent_id')->nullable();
            $table->foreignId('pet_id')->nullable();
            $table->dateTime('scheduled_at');
            $table->string('status')->default('pending'); // pending, assigned, completed
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
