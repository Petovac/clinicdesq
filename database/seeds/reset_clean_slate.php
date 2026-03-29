<?php
/**
 * RESET: Clean slate — keep admin, external labs, and knowledge base.
 * Deletes all orgs, clinics, users (except superadmin), vets, pets, appointments, etc.
 *
 * Run ON LIVE: php artisan tinker database/seeds/reset_clean_slate.php
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "╔══════════════════════════════════════╗\n";
echo "║   CLEAN SLATE RESET                 ║\n";
echo "║   Keeping: admin, labs, KB           ║\n";
echo "╚══════════════════════════════════════╝\n\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// ─── TABLES TO TRUNCATE ───

$truncateTables = [
    // Appointments & clinical
    'appointments', 'appointment_treatments', 'case_sheets',
    'prescriptions', 'prescription_items', 'pet_vaccinations',
    'diagnostic_reports', 'diagnostic_files', 'certificate_templates',

    // Billing
    'bills', 'bill_items',

    // IPD
    'ipd_admissions', 'ipd_notes', 'ipd_treatments', 'ipd_vitals',

    // Lab orders (not lab directory/offerings)
    'lab_orders', 'lab_order_tests', 'lab_results',

    // Org-specific lab config
    'lab_test_catalog', 'lab_test_availability', 'external_lab_tests',
    'clinic_lab_tests', 'clinic_external_lab', 'organisation_lab', 'org_lab_test_pricing',

    // Inventory
    'inventory_items', 'inventory_batches', 'inventory_movements',
    'inventory_orders', 'inventory_order_items', 'inventory_usage_logs',
    'clinic_inventory', 'procedure_inventory_items',

    // Pricing
    'price_lists', 'price_list_items', 'packages', 'injection_route_fees',

    // Pets & parents
    'pets', 'pet_parents', 'pet_parent_access_otps', 'pet_parent_clinic_access',

    // Vets
    'vets', 'vet_schedules', 'vet_breaks', 'vet_certificates',
    'vet_ai_credits', 'vet_ai_transactions', 'clinic_vet',

    // Orgs & clinics
    'clinics', 'clinic_user', 'clinic_user_assignments', 'clinic_brands',
    'clinic_reviews', 'organisations', 'organisation_roles', 'organisation_user_roles',

    // Users (will re-add admin after)
    'users',

    // Jobs & misc
    'job_postings', 'job_applications',
    'webhook_endpoints', 'webhook_deliveries',
    'whatsapp_configs', 'whatsapp_messages', 'drug_submissions',

    // Sessions & cache
    'sessions', 'cache', 'cache_locks', 'password_reset_tokens',

    // Permissions
    'role_permissions', 'permissions',
];

$truncated = 0;
$skipped = 0;
foreach ($truncateTables as $table) {
    try {
        if (Schema::hasTable($table)) {
            $count = DB::table($table)->count();
            DB::table($table)->truncate();
            echo "  ✓ {$table} ({$count} rows cleared)\n";
            $truncated++;
        } else {
            $skipped++;
        }
    } catch (\Exception $e) {
        echo "  ✗ {$table}: " . $e->getMessage() . "\n";
        $skipped++;
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "\n--- Truncated {$truncated} tables, skipped {$skipped} ---\n\n";

// ─── Re-create superadmin user ──────────────────────────────────
$adminId = DB::table('users')->insertGetId([
    'name' => 'Super Admin',
    'email' => 'admin@clinicdesq.com',
    'password' => bcrypt('admin123'),
    'role' => 'superadmin',
    'is_active' => 1,
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "✓ Superadmin created: admin@clinicdesq.com / admin123 (id: {$adminId})\n";

// ─── Summary of what's preserved ────────────────────────────────
echo "\n═══ PRESERVED DATA ═══\n";
echo "  External Labs: " . DB::table('external_labs')->count() . "\n";
echo "  Lab Users: " . DB::table('lab_users')->count() . "\n";
echo "  Lab Offerings: " . DB::table('external_lab_offerings')->count() . "\n";
echo "  Lab Test Directory: " . DB::table('lab_test_directory')->count() . "\n";
echo "  Drug Generics: " . DB::table('drug_generics')->count() . "\n";
echo "  Drug Brands: " . DB::table('drug_brands')->count() . "\n";
echo "  Drug Dosages: " . DB::table('drug_dosages')->count() . "\n";
echo "\n✅ Clean slate ready! Register a fresh org → clinics → vets → start testing.\n";
