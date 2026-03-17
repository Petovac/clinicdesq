<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_sheets', function (Blueprint $table) {
            $table->string('prognosis', 50)->nullable()->after('advice');
            $table->date('followup_date')->nullable()->after('prognosis');
            $table->text('followup_reason')->nullable()->after('followup_date');
        });
    }

    public function down(): void
    {
        Schema::table('case_sheets', function (Blueprint $table) {
            $table->dropColumn(['prognosis', 'followup_date', 'followup_reason']);
        });
    }
};
