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
        Schema::create('case_sheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id')->unique();

            $table->text('presenting_complaint')->nullable();
            $table->text('history')->nullable();
            $table->text('clinical_examination')->nullable();
            $table->text('differentials')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment_given')->nullable();
            $table->text('procedures_done')->nullable();
            $table->text('further_plan')->nullable();
            $table->text('advice')->nullable();

            $table->timestamps();

            $table->foreign('appointment_id')
                  ->references('id')
                  ->on('appointments')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_sheets');
    }
};
