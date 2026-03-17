<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipd_vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ipd_admission_id')->constrained()->onDelete('cascade');

            $table->string('recorded_by_type');   // 'user' or 'vet'
            $table->unsignedBigInteger('recorded_by_id');
            $table->dateTime('recorded_at');

            $table->decimal('temperature', 5, 2)->nullable();
            $table->unsignedSmallInteger('heart_rate')->nullable();
            $table->unsignedSmallInteger('respiratory_rate')->nullable();
            $table->decimal('weight', 8, 3)->nullable();
            $table->unsignedSmallInteger('spo2')->nullable();
            $table->unsignedSmallInteger('blood_pressure_systolic')->nullable();
            $table->unsignedSmallInteger('blood_pressure_diastolic')->nullable();
            $table->string('mucous_membrane')->nullable();
            $table->decimal('crt', 3, 1)->nullable();
            $table->unsignedTinyInteger('pain_score')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->index('ipd_admission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_vitals');
    }
};
