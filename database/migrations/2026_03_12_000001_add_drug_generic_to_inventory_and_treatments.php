<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Direct generic link on inventory_items
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->unsignedBigInteger('drug_generic_id')->nullable()->after('drug_brand_id');
            $table->foreign('drug_generic_id')->references('id')->on('drug_generics')->nullOnDelete();
        });

        // 2. inventory_item_id on appointment_treatments
        Schema::table('appointment_treatments', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_item_id')->nullable()->after('drug_brand_id');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropForeign(['drug_generic_id']);
            $table->dropColumn('drug_generic_id');
        });

        Schema::table('appointment_treatments', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn('inventory_item_id');
        });
    }
};
