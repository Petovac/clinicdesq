<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('permissions')->insert([
            ['name' => 'View IPD',          'slug' => 'ipd.view',        'group' => 'IPD',      'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage IPD',         'slug' => 'ipd.manage',      'group' => 'IPD',      'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Manage Settings',    'slug' => 'settings.manage', 'group' => 'Settings', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('slug', ['ipd.view', 'ipd.manage', 'settings.manage'])->delete();
    }
};
