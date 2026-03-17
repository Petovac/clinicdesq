<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────
        // 1. External labs — independent lab entities that orgs can tie up with
        // ─────────────────────────────────────────────
        Schema::create('external_labs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // 2. Org ↔ External lab tie-ups (many-to-many)
        //    An org in Bangalore ties up with labs in Bangalore
        // ─────────────────────────────────────────────
        Schema::create('organisation_lab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('external_lab_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['organisation_id', 'external_lab_id']);
        });

        // ─────────────────────────────────────────────
        // 3. Lab users — login for external lab staff AND in-house lab techs
        //    external lab staff → external_lab_id set, organisation_id null
        //    in-house lab tech  → organisation_id set, external_lab_id null
        // ─────────────────────────────────────────────
        Schema::create('lab_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_lab_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('organisation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('clinic_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->enum('role', ['lab_tech', 'lab_admin'])->default('lab_tech');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // 4. Lab test catalog — org-level master list of tests
        //    Org admin creates tests with parameters and pricing
        // ─────────────────────────────────────────────
        Schema::create('lab_test_catalog', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete();
            $table->string('name');                    // e.g. "Complete Blood Count (CBC)"
            $table->string('code')->nullable();        // e.g. "CBC-001"
            $table->enum('category', [
                'hematology', 'biochemistry', 'urinalysis', 'serology',
                'cytology', 'histopathology', 'microbiology', 'immunology',
                'endocrinology', 'parasitology', 'other',
            ])->default('other');
            $table->enum('sample_type', [
                'blood', 'serum', 'plasma', 'urine', 'swab',
                'tissue', 'fluid', 'feces', 'other',
            ])->default('blood');
            $table->text('parameters')->nullable();    // JSON: ["RBC","WBC","Platelets","Hb","PCV","MCV","MCH","MCHC"]
            $table->string('estimated_time')->nullable(); // e.g. "2 hours", "24 hours"
            $table->decimal('price', 10, 2)->default(0); // org's price to client
            $table->decimal('cost_price', 10, 2)->nullable(); // org's cost (for margin calc)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // 5. Lab test availability — lab tech marks which tests are doable
        //    Only tests marked available by lab tech show as "in-house" for doctors
        // ─────────────────────────────────────────────
        Schema::create('lab_test_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_test_catalog_id')->constrained('lab_test_catalog')->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_available')->default(true);
            $table->string('unavailable_reason')->nullable(); // e.g. "Machine under maintenance"
            $table->foreignId('updated_by')->nullable()->constrained('lab_users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['lab_test_catalog_id', 'clinic_id']);
        });

        // ─────────────────────────────────────────────
        // 6. External lab test offerings — tests available from tied-up external labs
        //    with their B2B pricing (what the lab charges the org)
        // ─────────────────────────────────────────────
        Schema::create('external_lab_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_lab_id')->constrained()->cascadeOnDelete();
            $table->string('test_name');
            $table->string('test_code')->nullable();
            $table->string('category')->nullable();
            $table->string('sample_type')->nullable();
            $table->text('parameters')->nullable();        // JSON array of parameters
            $table->string('estimated_time')->nullable();
            $table->decimal('b2b_price', 10, 2)->default(0); // what the lab charges the org
            $table->decimal('org_selling_price', 10, 2)->nullable(); // what org charges client (set by org admin)
            $table->foreignId('organisation_id')->nullable()->constrained()->nullOnDelete(); // org-specific pricing override
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // 7. Lab orders — created when doctor orders tests
        // ─────────────────────────────────────────────
        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('vet_id');
            $table->foreign('vet_id')->references('id')->on('vets')->cascadeOnDelete();

            // Lab assignment — doctor can pre-select or clinic staff routes later
            $table->foreignId('lab_id')->nullable()->constrained('external_labs')->nullOnDelete();
            $table->enum('routing', ['pending', 'in_house', 'external'])->default('pending');

            $table->enum('status', [
                'ordered',              // doctor placed order
                'routed',               // clinic staff assigned to a lab
                'sample_collected',     // sample taken from pet
                'processing',           // lab is running tests
                'results_uploaded',     // lab uploaded results
                'vet_review',           // vet is reviewing
                'approved',             // vet approved → visible to client
                'retest_requested',     // vet wants retest
            ])->default('ordered');

            $table->enum('priority', ['routine', 'urgent', 'stat'])->default('routine');
            $table->text('notes')->nullable();             // doctor's clinical notes for lab
            $table->unsignedBigInteger('routed_by')->nullable();
            $table->foreign('routed_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('routed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Who uploaded result (for direct PDF uploads by clinic staff)
            $table->unsignedBigInteger('result_uploaded_by')->nullable();
            $table->string('result_uploaded_by_type')->nullable(); // 'lab_user' or 'user' (clinic staff)

            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // 8. Individual tests within an order
        // ─────────────────────────────────────────────
        Schema::create('lab_order_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_test_catalog_id')->nullable()->constrained('lab_test_catalog')->nullOnDelete();
            $table->foreignId('external_lab_test_id')->nullable()->constrained('external_lab_tests')->nullOnDelete();
            $table->string('test_name');
            $table->text('parameters')->nullable();       // JSON — copied at order time
            $table->decimal('price', 10, 2)->default(0);  // price at time of order
            $table->enum('status', ['pending', 'processing', 'completed', 'retest'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // 9. Lab results — files and findings
        // ─────────────────────────────────────────────
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_order_test_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->text('extracted_text')->nullable();    // AI extraction
            $table->text('summary')->nullable();
            $table->text('result_data')->nullable();       // JSON structured results

            // Who uploaded
            $table->foreignId('uploaded_by_lab_user_id')->nullable()->constrained('lab_users')->nullOnDelete();
            $table->unsignedBigInteger('uploaded_by_user_id')->nullable(); // clinic staff
            $table->foreign('uploaded_by_user_id')->references('id')->on('users')->nullOnDelete();

            // Vet review
            $table->boolean('vet_approved')->default(false);
            $table->timestamp('vet_approved_at')->nullable();
            $table->text('vet_notes')->nullable();
            $table->boolean('retest_requested')->default(false);
            $table->text('retest_reason')->nullable();
            $table->boolean('visible_to_client')->default(false);
            $table->timestamps();
        });

        // ─────────────────────────────────────────────
        // 10. Lab permissions
        // ─────────────────────────────────────────────
        DB::table('permissions')->insert([
            ['name' => 'View Lab Orders', 'slug' => 'lab_orders.view', 'group' => 'Lab Orders', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Create Lab Orders', 'slug' => 'lab_orders.create', 'group' => 'Lab Orders', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manage Lab Orders', 'slug' => 'lab_orders.manage', 'group' => 'Lab Orders', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Upload Lab Results', 'slug' => 'lab_orders.upload', 'group' => 'Lab Orders', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manage Lab Catalog', 'slug' => 'lab_catalog.manage', 'group' => 'Lab Orders', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manage External Labs', 'slug' => 'labs.manage', 'group' => 'Lab Orders', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_results');
        Schema::dropIfExists('lab_order_tests');
        Schema::dropIfExists('lab_orders');
        Schema::dropIfExists('external_lab_tests');
        Schema::dropIfExists('lab_test_availability');
        Schema::dropIfExists('lab_test_catalog');
        Schema::dropIfExists('lab_users');
        Schema::dropIfExists('organisation_lab');
        Schema::dropIfExists('external_labs');

        DB::table('permissions')->whereIn('slug', [
            'lab_orders.view', 'lab_orders.create', 'lab_orders.manage',
            'lab_orders.upload', 'lab_catalog.manage', 'labs.manage',
        ])->delete();
    }
};
