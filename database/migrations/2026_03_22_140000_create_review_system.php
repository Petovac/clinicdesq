<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add GMB review URL to clinics
        Schema::table('clinics', function (Blueprint $table) {
            $table->string('gmb_review_url', 500)->nullable()->after('gst_number');
        });

        // Internal reviews from pet parents
        Schema::create('clinic_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('pet_parent_id')->nullable();
            $table->unsignedBigInteger('vet_id')->nullable();
            $table->string('token', 64)->unique(); // for anonymous review link
            $table->tinyInteger('overall_rating')->nullable(); // 1-5
            $table->tinyInteger('staff_rating')->nullable(); // 1-5
            $table->tinyInteger('cleanliness_rating')->nullable(); // 1-5
            $table->tinyInteger('wait_time_rating')->nullable(); // 1-5
            $table->tinyInteger('doctor_rating')->nullable(); // 1-5
            $table->text('feedback')->nullable();
            $table->boolean('would_recommend')->nullable();
            $table->enum('status', ['pending', 'submitted', 'flagged'])->default('pending');
            $table->boolean('gmb_link_sent')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('pet_parent_id')->references('id')->on('pet_parents')->onDelete('set null');
            $table->foreign('vet_id')->references('id')->on('vets')->onDelete('set null');
            $table->index(['clinic_id', 'status']);
            $table->index('token');
        });

        // Add reviews.view and reviews.manage permissions
        $perms = [
            ['name' => 'View Reviews', 'slug' => 'reviews.view', 'group' => 'reviews'],
            ['name' => 'Manage Reviews', 'slug' => 'reviews.manage', 'group' => 'reviews'],
        ];
        foreach ($perms as $p) {
            DB::table('permissions')->insertOrIgnore(array_merge($p, ['created_at' => now(), 'updated_at' => now()]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_reviews');
        Schema::table('clinics', function (Blueprint $table) {
            $table->dropColumn('gmb_review_url');
        });
        DB::table('permissions')->whereIn('slug', ['reviews.view', 'reviews.manage'])->delete();
    }
};
