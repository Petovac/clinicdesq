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
        Schema::table('vets', function (Blueprint $table) {
            $table->string('degree')->nullable()->after('specialization');
            $table->text('skills')->nullable()->after('degree');
            $table->text('certifications')->nullable()->after('skills');
            $table->text('experience')->nullable()->after('certifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vets', function (Blueprint $table) {
            $table->dropColumn([
                'degree',
                'skills',
                'certifications',
                'experience'
            ]);
        });
    }
};
