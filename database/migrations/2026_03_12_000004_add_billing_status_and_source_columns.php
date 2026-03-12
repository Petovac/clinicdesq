<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // bills — add draft/confirmed status
        Schema::table('bills', function (Blueprint $table) {
            $table->enum('status', ['draft', 'confirmed'])->default('draft')->after('payment_status');
            $table->string('notes')->nullable()->after('status');
        });

        // bill_items — add source, workflow status, and prescription link
        Schema::table('bill_items', function (Blueprint $table) {
            $table->enum('source', ['visit_fee', 'injection', 'procedure', 'prescription', 'manual'])
                  ->default('manual')->after('total');
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('approved')->after('source');
            $table->unsignedBigInteger('prescription_item_id')->nullable()->after('status');
            $table->string('description')->nullable()->after('prescription_item_id'); // for items without price_list_item

            $table->foreign('prescription_item_id')->references('id')->on('prescription_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['status', 'notes']);
        });

        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropForeign(['prescription_item_id']);
            $table->dropColumn(['source', 'status', 'prescription_item_id', 'description']);
        });
    }
};
