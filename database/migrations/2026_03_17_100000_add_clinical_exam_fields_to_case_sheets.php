<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_sheets', function (Blueprint $table) {
            $table->decimal('temperature', 5, 2)->nullable()->after('clinical_examination');
            $table->unsignedSmallInteger('heart_rate')->nullable()->after('temperature');
            $table->unsignedSmallInteger('respiratory_rate')->nullable()->after('heart_rate');
            $table->string('capillary_refill_time', 20)->nullable()->after('respiratory_rate');
            $table->string('mucous_membrane', 50)->nullable()->after('capillary_refill_time');
            $table->string('hydration_status', 50)->nullable()->after('mucous_membrane');
            $table->string('lymph_nodes', 100)->nullable()->after('hydration_status');
            $table->string('body_condition_score', 10)->nullable()->after('lymph_nodes');
            $table->string('pain_score', 10)->nullable()->after('body_condition_score');
        });
    }

    public function down(): void
    {
        Schema::table('case_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'temperature',
                'heart_rate',
                'respiratory_rate',
                'capillary_refill_time',
                'mucous_membrane',
                'hydration_status',
                'lymph_nodes',
                'body_condition_score',
                'pain_score',
            ]);
        });
    }
};
