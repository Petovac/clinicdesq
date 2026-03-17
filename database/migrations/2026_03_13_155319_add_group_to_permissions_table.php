<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add group column
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('group', 50)->nullable()->after('slug');
        });

        // Delete legacy duplicate permission (ID=1 manage_users, replaced by ID=7 users.manage)
        DB::table('role_permissions')->where('permission_id', 1)->delete();
        DB::table('permissions')->where('id', 1)->delete();

        // Delete appointments.metrics (ID=15) — replaced by dashboard.metrics
        DB::table('role_permissions')->where('permission_id', 15)->delete();
        DB::table('permissions')->where('id', 15)->delete();

        // Insert new dashboard.metrics permission
        DB::table('permissions')->insert([
            'id'         => 36,
            'name'       => 'View Dashboard Metrics',
            'slug'       => 'dashboard.metrics',
            'group'      => 'Dashboard',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign groups to all existing permissions
        $groupMap = [
            3  => 'Dashboard',
            4  => 'Clinics',
            5  => 'Clinics',
            6  => 'Users & Roles',
            7  => 'Users & Roles',
            8  => 'Users & Roles',
            9  => 'Users & Roles',
            10 => 'Vets',
            11 => 'Vets',
            12 => 'Appointments',
            13 => 'Appointments',
            14 => 'Appointments',
            16 => 'Clinical Records',
            17 => 'Clinical Records',
            18 => 'Clinical Records',
            19 => 'Diagnostics & Reports',
            20 => 'Diagnostics & Reports',
            21 => 'Billing',
            22 => 'Billing',
            23 => 'Billing',
            24 => 'Inventory',
            25 => 'Inventory',
            26 => 'Inventory',
            27 => 'Inventory',
            28 => 'Inventory',
            29 => 'Inventory',
            30 => 'Inventory',
            31 => 'Pricing',
            32 => 'Pricing',
            33 => 'Followups & Performance',
            34 => 'Followups & Performance',
            35 => 'Followups & Performance',
        ];

        foreach ($groupMap as $id => $group) {
            DB::table('permissions')->where('id', $id)->update(['group' => $group]);
        }
    }

    public function down(): void
    {
        DB::table('permissions')->insert([
            ['id' => 1, 'name' => 'Manage Users', 'slug' => 'manage_users', 'group' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'name' => 'View Appointment Metrics', 'slug' => 'appointments.metrics', 'group' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('role_permissions')->where('permission_id', 36)->delete();
        DB::table('permissions')->where('id', 36)->delete();

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('group');
        });
    }
};
