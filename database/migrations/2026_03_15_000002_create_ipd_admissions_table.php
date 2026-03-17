<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipd_admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->foreignId('pet_parent_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');

            $table->string('admitted_by_type');   // 'user' or 'vet'
            $table->unsignedBigInteger('admitted_by_id');

            $table->dateTime('admission_date');
            $table->text('admission_reason');
            $table->text('tentative_diagnosis')->nullable();
            $table->string('cage_number')->nullable();
            $table->string('ward')->nullable();

            $table->enum('status', ['admitted', 'discharged', 'transferred', 'deceased'])->default('admitted');

            $table->dateTime('discharged_at')->nullable();
            $table->text('discharge_notes')->nullable();
            $table->text('discharge_summary')->nullable();
            $table->string('discharged_by_type')->nullable();
            $table->unsignedBigInteger('discharged_by_id')->nullable();

            $table->timestamps();

            $table->index(['clinic_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_admissions');
    }
};
