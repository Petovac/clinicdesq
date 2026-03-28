<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove pre-scheduled break from vet_schedules
        Schema::table('vet_schedules', function (Blueprint $table) {
            $table->dropColumn(['break_start', 'break_end']);
        });

        // On-demand breaks — doctor manually goes on/off break
        Schema::create('vet_breaks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vet_id');
            $table->unsignedBigInteger('clinic_id');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable(); // null = currently on break
            $table->string('reason', 100)->nullable(); // lunch, personal, etc.
            $table->timestamps();

            $table->foreign('vet_id')->references('id')->on('vets')->onDelete('cascade');
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->index(['vet_id', 'clinic_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vet_breaks');

        Schema::table('vet_schedules', function (Blueprint $table) {
            $table->time('break_start')->nullable()->after('slot_duration_minutes');
            $table->time('break_end')->nullable()->after('break_start');
        });
    }
};
