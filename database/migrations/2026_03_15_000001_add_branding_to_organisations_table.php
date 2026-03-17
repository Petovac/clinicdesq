<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('primary_phone');
            $table->string('template_prescription')->default('classic')->after('logo_path');
            $table->string('template_casesheet')->default('classic')->after('template_prescription');
            $table->string('template_bill')->default('classic')->after('template_casesheet');
        });
    }

    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'template_prescription', 'template_casesheet', 'template_bill']);
        });
    }
};
