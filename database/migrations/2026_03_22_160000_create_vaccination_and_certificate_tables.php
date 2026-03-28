<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pet vaccination records
        Schema::create('pet_vaccinations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pet_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('clinic_id');
            $table->unsignedBigInteger('vet_id');
            $table->string('vaccine_name');          // generic name
            $table->string('brand_name')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('batch_number')->nullable();
            $table->enum('dose_number', ['1st', '2nd', '3rd', '4th', 'booster', 'annual'])->default('1st');
            $table->date('administered_date');
            $table->date('next_due_date')->nullable();
            $table->string('route', 10)->nullable();  // SC, IM
            $table->string('site', 50)->nullable();    // left hind leg, scruff, etc
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('pet_id')->references('id')->on('pets')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->foreign('vet_id')->references('id')->on('vets')->onDelete('cascade');
            $table->index(['pet_id', 'administered_date']);
            $table->index('next_due_date');
        });

        // 2. Certificate templates (system defaults + org custom)
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id')->nullable(); // null = system default
            $table->string('type', 30);  // health, travel, vaccination, fitness, custom
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('content_template'); // field definitions
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('organisation_id')->references('id')->on('organisations')->onDelete('cascade');
            $table->index(['organisation_id', 'type']);
        });

        // 3. Issued certificates
        Schema::create('vet_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pet_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('clinic_id');
            $table->unsignedBigInteger('vet_id');
            $table->unsignedBigInteger('organisation_id');
            $table->unsignedBigInteger('certificate_template_id')->nullable();
            $table->enum('certificate_type', ['health', 'travel', 'vaccination', 'fitness', 'euthanasia', 'custom'])->default('health');
            $table->string('certificate_number', 30)->unique();
            $table->string('title');
            $table->json('content')->nullable(); // filled field values
            $table->date('issued_date');
            $table->date('valid_until')->nullable();
            $table->enum('status', ['draft', 'issued'])->default('draft');
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->foreign('pet_id')->references('id')->on('pets')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->foreign('vet_id')->references('id')->on('vets')->onDelete('cascade');
            $table->foreign('organisation_id')->references('id')->on('organisations')->onDelete('cascade');
            $table->foreign('certificate_template_id')->references('id')->on('certificate_templates')->onDelete('set null');
            $table->index(['pet_id', 'certificate_type']);
        });

        // 4. Seed system default templates
        $this->seedTemplates();
    }

    private function seedTemplates(): void
    {
        $templates = [
            [
                'type' => 'health',
                'name' => 'Health Certificate',
                'description' => 'General health certificate confirming the animal is in good health',
                'content_template' => json_encode([
                    'fields' => [
                        ['key' => 'purpose', 'label' => 'Purpose of Certificate', 'type' => 'text', 'default' => 'General health clearance'],
                        ['key' => 'exam_date', 'label' => 'Date of Examination', 'type' => 'date', 'default' => ''],
                        ['key' => 'general_condition', 'label' => 'General Condition', 'type' => 'select', 'options' => ['Good', 'Fair', 'Poor'], 'default' => 'Good'],
                        ['key' => 'body_weight', 'label' => 'Body Weight (kg)', 'type' => 'text', 'default' => ''],
                        ['key' => 'temperature', 'label' => 'Temperature (°F)', 'type' => 'text', 'default' => ''],
                        ['key' => 'exam_findings', 'label' => 'Examination Findings', 'type' => 'textarea', 'default' => 'On clinical examination, the animal appears bright, alert, and responsive. No abnormalities detected in general physical examination.'],
                        ['key' => 'vaccination_status', 'label' => 'Vaccination Status', 'type' => 'text', 'default' => 'Up to date'],
                        ['key' => 'deworming_status', 'label' => 'Deworming Status', 'type' => 'text', 'default' => ''],
                        ['key' => 'declaration', 'label' => 'Declaration', 'type' => 'textarea', 'default' => 'I hereby certify that the above-mentioned animal has been examined by me and found to be in good health, free from any contagious or infectious disease, and is fit for the stated purpose.'],
                    ],
                ]),
            ],
            [
                'type' => 'travel',
                'name' => 'Travel / Fit-to-Fly Certificate',
                'description' => 'Certificate for domestic or international pet travel',
                'content_template' => json_encode([
                    'fields' => [
                        ['key' => 'destination', 'label' => 'Destination', 'type' => 'text', 'default' => ''],
                        ['key' => 'travel_date', 'label' => 'Date of Travel', 'type' => 'date', 'default' => ''],
                        ['key' => 'mode_of_travel', 'label' => 'Mode of Travel', 'type' => 'select', 'options' => ['Air', 'Road', 'Rail'], 'default' => 'Air'],
                        ['key' => 'carrier_name', 'label' => 'Airline / Carrier', 'type' => 'text', 'default' => ''],
                        ['key' => 'microchip_number', 'label' => 'Microchip Number', 'type' => 'text', 'default' => ''],
                        ['key' => 'exam_findings', 'label' => 'Examination Findings', 'type' => 'textarea', 'default' => 'On clinical examination, the animal is found to be clinically healthy, free from ectoparasites, and fit to travel.'],
                        ['key' => 'rabies_vaccination', 'label' => 'Rabies Vaccination Date', 'type' => 'date', 'default' => ''],
                        ['key' => 'rabies_validity', 'label' => 'Rabies Vaccination Valid Until', 'type' => 'date', 'default' => ''],
                        ['key' => 'other_vaccinations', 'label' => 'Other Vaccinations', 'type' => 'textarea', 'default' => ''],
                        ['key' => 'declaration', 'label' => 'Declaration', 'type' => 'textarea', 'default' => 'I hereby certify that I have examined the above-described animal within 72 hours of the intended travel date and found it to be clinically healthy, free from signs of contagious or infectious disease, and fit to undertake the journey.'],
                    ],
                ]),
            ],
            [
                'type' => 'vaccination',
                'name' => 'Vaccination Certificate',
                'description' => 'Official vaccination record certificate for the pet',
                'content_template' => json_encode([
                    'fields' => [
                        ['key' => 'vaccination_history', 'label' => 'Vaccination History', 'type' => 'auto_vaccinations', 'default' => ''],
                        ['key' => 'next_due', 'label' => 'Next Vaccination Due', 'type' => 'text', 'default' => ''],
                        ['key' => 'remarks', 'label' => 'Remarks', 'type' => 'textarea', 'default' => ''],
                        ['key' => 'declaration', 'label' => 'Declaration', 'type' => 'textarea', 'default' => 'I hereby certify that the above-mentioned animal has been vaccinated as per the schedule indicated above.'],
                    ],
                ]),
            ],
            [
                'type' => 'fitness',
                'name' => 'Fitness Certificate',
                'description' => 'Certificate for fitness for breeding, surgery, or boarding',
                'content_template' => json_encode([
                    'fields' => [
                        ['key' => 'purpose', 'label' => 'Purpose', 'type' => 'select', 'options' => ['Breeding', 'Surgery', 'Boarding', 'Grooming', 'Dog Show', 'Other'], 'default' => 'Breeding'],
                        ['key' => 'body_weight', 'label' => 'Body Weight (kg)', 'type' => 'text', 'default' => ''],
                        ['key' => 'body_condition', 'label' => 'Body Condition Score', 'type' => 'select', 'options' => ['1/9', '2/9', '3/9', '4/9', '5/9', '6/9', '7/9', '8/9', '9/9'], 'default' => '5/9'],
                        ['key' => 'exam_findings', 'label' => 'Clinical Examination', 'type' => 'textarea', 'default' => 'On clinical examination, the animal is found to be in good health and physical condition.'],
                        ['key' => 'specific_tests', 'label' => 'Specific Tests Done', 'type' => 'textarea', 'default' => ''],
                        ['key' => 'declaration', 'label' => 'Declaration', 'type' => 'textarea', 'default' => 'I hereby certify that the above-mentioned animal has been examined and found fit for the stated purpose.'],
                    ],
                ]),
            ],
            [
                'type' => 'custom',
                'name' => 'Custom Certificate',
                'description' => 'Blank certificate with editable title and body',
                'content_template' => json_encode([
                    'fields' => [
                        ['key' => 'body', 'label' => 'Certificate Body', 'type' => 'textarea', 'default' => ''],
                    ],
                ]),
            ],
        ];

        foreach ($templates as $t) {
            DB::table('certificate_templates')->insert(array_merge($t, [
                'organisation_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vet_certificates');
        Schema::dropIfExists('certificate_templates');
        Schema::dropIfExists('pet_vaccinations');
    }
};
