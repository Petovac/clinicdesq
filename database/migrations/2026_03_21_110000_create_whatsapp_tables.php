<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // WhatsApp config per organisation
        Schema::create('whatsapp_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id')->unique();
            $table->enum('provider', ['msg91'])->default('msg91');
            $table->text('api_key')->nullable(); // encrypted
            $table->string('integrated_number_id')->nullable(); // MSG91 integrated number ID
            $table->string('whatsapp_number', 20)->nullable(); // display number
            $table->boolean('is_active')->default(false);
            $table->boolean('send_case_sheet')->default(true);
            $table->boolean('send_prescription')->default(true);
            $table->boolean('send_bill')->default(true);
            $table->boolean('send_lab_report')->default(true);
            $table->timestamps();

            $table->foreign('organisation_id')->references('id')->on('organisations')->onDelete('cascade');
        });

        // WhatsApp message log
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->unsignedBigInteger('clinic_id')->nullable();
            $table->string('recipient_phone', 20);
            $table->string('recipient_name')->nullable();
            $table->string('template_name', 100); // MSG91 template slug
            $table->enum('message_type', ['case_sheet', 'prescription', 'bill', 'lab_report', 'appointment_reminder', 'custom']);
            $table->string('reference_type')->nullable(); // morphable: App\Models\Appointment, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('file_path')->nullable(); // stored PDF path
            $table->string('file_url')->nullable(); // public URL sent to MSG91
            $table->enum('status', ['queued', 'sent', 'delivered', 'read', 'failed'])->default('queued');
            $table->string('provider_request_id')->nullable(); // MSG91 request ID
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable(); // user_id
            $table->timestamps();

            $table->foreign('organisation_id')->references('id')->on('organisations')->onDelete('cascade');
            $table->index(['organisation_id', 'message_type']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_configs');
    }
};
