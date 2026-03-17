<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipd_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ipd_admission_id')->constrained()->onDelete('cascade');

            $table->string('noted_by_type');     // 'user' or 'vet'
            $table->unsignedBigInteger('noted_by_id');
            $table->enum('note_type', ['progress', 'handover', 'observation', 'plan'])->default('progress');
            $table->text('content');

            $table->timestamps();
            $table->index('ipd_admission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_notes');
    }
};
