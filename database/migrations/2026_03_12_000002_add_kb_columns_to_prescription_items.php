<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            // drug_generic_id already exists — skip
            // drug_brand_id already exists — skip
            $table->unsignedBigInteger('inventory_item_id')->nullable()->after('drug_generic_id');
            $table->decimal('strength_value', 8, 2)->nullable()->after('inventory_item_id');
            $table->string('strength_unit', 20)->nullable()->after('strength_value');
            $table->string('form', 50)->nullable()->after('strength_unit');

            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn(['inventory_item_id', 'strength_value', 'strength_unit', 'form']);
        });
    }
};
