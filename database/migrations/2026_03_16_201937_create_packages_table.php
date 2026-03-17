<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('name', 100);
            $table->integer('price_per_doctor')->default(1499);
            $table->integer('original_price')->default(1999);
            $table->integer('trial_days')->default(30);
            $table->json('features');
            $table->integer('max_clinics')->nullable();
            $table->integer('max_doctors')->nullable();
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('organisations', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn('package_id');
        });
        Schema::dropIfExists('packages');
    }
};
