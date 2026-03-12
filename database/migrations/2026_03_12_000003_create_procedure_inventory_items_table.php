<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedure_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('price_list_item_id'); // must be item_type = 'procedure'
            $table->unsignedBigInteger('inventory_item_id');
            $table->decimal('quantity_used', 8, 3)->default(1); // e.g. 1 cannula, 5ml saline

            $table->foreign('price_list_item_id')->references('id')->on('price_list_items')->cascadeOnDelete();
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedure_inventory_items');
    }
};
