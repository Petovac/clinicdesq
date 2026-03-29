<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('external_lab_offerings', function (Blueprint $table) {
            if (!Schema::hasColumn('external_lab_offerings', 'container_type')) {
                $table->string('container_type', 100)->nullable()->after('parameters');
            }
            if (!Schema::hasColumn('external_lab_offerings', 'sample_volume')) {
                $table->string('sample_volume', 50)->nullable()->after('container_type');
            }
            if (!Schema::hasColumn('external_lab_offerings', 'collection_method')) {
                $table->string('collection_method', 100)->nullable()->after('sample_volume');
            }
        });
    }

    public function down(): void
    {
        Schema::table('external_lab_offerings', function (Blueprint $table) {
            $table->dropColumn(['container_type', 'sample_volume', 'collection_method']);
        });
    }
};
