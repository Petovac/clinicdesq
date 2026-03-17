<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vets', function (Blueprint $table) {
            $table->string('signature_path')->nullable()->after('experience');
            $table->string('license_path')->nullable()->after('signature_path');
            $table->text('certificate_paths')->nullable()->after('license_path');
        });
    }

    public function down(): void
    {
        Schema::table('vets', function (Blueprint $table) {
            $table->dropColumn(['signature_path', 'license_path', 'certificate_paths']);
        });
    }
};
