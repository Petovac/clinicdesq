<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Webhook endpoints registered by each org
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->string('url'); // https://their-app.com/api/clinicdesq/webhook
            $table->string('secret', 64); // HMAC signing secret
            $table->string('label')->nullable(); // e.g. "My Brand App", "Website CRM"
            $table->json('events'); // ['case_sheet.saved', 'prescription.created', ...]
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->integer('failure_count')->default(0);
            $table->timestamps();

            $table->foreign('organisation_id')->references('id')->on('organisations')->onDelete('cascade');
            $table->index('organisation_id');
        });

        // Webhook delivery log
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('webhook_endpoint_id');
            $table->string('event', 100);
            $table->json('payload')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->integer('http_status')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('attempt')->default(1);
            $table->timestamps();

            $table->foreign('webhook_endpoint_id')->references('id')->on('webhook_endpoints')->onDelete('cascade');
            $table->index(['webhook_endpoint_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('webhook_endpoints');
    }
};
