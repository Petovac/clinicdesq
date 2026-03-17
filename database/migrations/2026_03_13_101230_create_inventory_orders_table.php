<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id');
            $table->unsignedBigInteger('organisation_id');
            $table->string('order_number', 30)->unique();
            $table->enum('order_type', ['vendor', 'organisation'])->default('vendor');
            $table->string('vendor_name', 255)->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'fulfilled', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('clinics');
            $table->foreign('organisation_id')->references('id')->on('organisations');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });

        Schema::create('inventory_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_order_id');
            $table->unsignedBigInteger('inventory_item_id');
            $table->decimal('quantity_requested', 10, 3);
            $table->decimal('quantity_fulfilled', 10, 3)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('inventory_order_id')->references('id')->on('inventory_orders')->onDelete('cascade');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_order_items');
        Schema::dropIfExists('inventory_orders');
    }
};
