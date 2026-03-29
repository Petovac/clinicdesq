<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lab_test_directory')) {
            Schema::table('lab_test_directory', function (Blueprint $table) {
                if (!Schema::hasColumn('lab_test_directory', 'preferred_sample')) {
                    $table->string('preferred_sample')->nullable()->after('sample_type');
                }
                if (!Schema::hasColumn('lab_test_directory', 'tat')) {
                    $table->string('tat')->nullable()->after('preferred_sample');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('lab_test_directory', function (Blueprint $table) {
            $table->dropColumn(['preferred_sample', 'tat']);
        });
    }
};
