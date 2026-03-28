<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vet_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vet_id');
            $table->unsignedBigInteger('clinic_id');
            $table->tinyInteger('day_of_week'); // 0=Sun, 1=Mon ... 6=Sat
            $table->time('start_time')->default('09:00');
            $table->time('end_time')->default('18:00');
            $table->unsignedSmallInteger('slot_duration_minutes')->default(30);
            $table->time('break_start')->nullable(); // e.g., 13:00
            $table->time('break_end')->nullable();   // e.g., 14:00
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('vet_id')->references('id')->on('vets')->onDelete('cascade');
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->unique(['vet_id', 'clinic_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vet_schedules');
    }
};
