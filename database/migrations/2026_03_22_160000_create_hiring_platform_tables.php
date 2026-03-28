<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->unsignedBigInteger('clinic_id')->nullable(); // specific clinic or null=any
            $table->string('title'); // e.g. "Veterinarian", "Senior Vet Surgeon"
            $table->enum('role_type', ['vet', 'vet_surgeon', 'vet_specialist', 'vet_intern'])->default('vet');
            $table->enum('employment_type', ['full_time', 'part_time', 'locum', 'contract'])->default('full_time');
            $table->text('description')->nullable();
            $table->text('requirements')->nullable(); // degree, experience, skills
            $table->string('specialization_required')->nullable();
            $table->integer('min_experience_years')->nullable();
            $table->decimal('salary_min', 10, 0)->nullable();
            $table->decimal('salary_max', 10, 0)->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->enum('status', ['draft', 'active', 'paused', 'closed', 'filled'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable(); // user who posted
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closes_at')->nullable();
            $table->timestamps();

            $table->foreign('organisation_id')->references('id')->on('organisations')->onDelete('cascade');
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('set null');
            $table->index(['status', 'city']);
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_posting_id');
            $table->unsignedBigInteger('vet_id');
            $table->text('cover_note')->nullable(); // short message from vet
            $table->string('resume_path')->nullable();
            $table->enum('status', ['applied', 'shortlisted', 'interview', 'offered', 'hired', 'rejected', 'withdrawn'])->default('applied');
            $table->text('org_notes')->nullable(); // internal notes by org admin
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('job_posting_id')->references('id')->on('job_postings')->onDelete('cascade');
            $table->foreign('vet_id')->references('id')->on('vets')->onDelete('cascade');
            $table->unique(['job_posting_id', 'vet_id']); // one application per vet per job
            $table->index('vet_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_postings');
    }
};
