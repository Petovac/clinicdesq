<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vet_ai_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('vet_ai_transactions', 'input_tokens')) {
                $table->integer('input_tokens')->nullable()->after('ai_feature');
            }
            if (!Schema::hasColumn('vet_ai_transactions', 'output_tokens')) {
                $table->integer('output_tokens')->nullable()->after('input_tokens');
            }
            if (!Schema::hasColumn('vet_ai_transactions', 'cost_usd')) {
                $table->decimal('cost_usd', 8, 6)->nullable()->after('output_tokens');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vet_ai_transactions', function (Blueprint $table) {
            $table->dropColumn(['input_tokens', 'output_tokens', 'cost_usd']);
        });
    }
};
