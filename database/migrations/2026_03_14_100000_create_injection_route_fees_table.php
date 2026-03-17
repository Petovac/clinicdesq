<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('injection_route_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->string('route_code', 20);
            $table->string('route_name', 80);
            $table->decimal('administration_fee', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['organisation_id', 'route_code']);
            $table->foreign('organisation_id')->references('id')->on('organisations')->onDelete('cascade');
        });

        // Seed default routes for org 9
        $routes = [
            ['route_code' => 'IV',  'route_name' => 'Intravenous',   'administration_fee' => 0],
            ['route_code' => 'IM',  'route_name' => 'Intramuscular',  'administration_fee' => 0],
            ['route_code' => 'SC',  'route_name' => 'Subcutaneous',   'administration_fee' => 0],
            ['route_code' => 'ID',  'route_name' => 'Intradermal',    'administration_fee' => 0],
            ['route_code' => 'PO',  'route_name' => 'Oral',           'administration_fee' => 0],
            ['route_code' => 'IO',  'route_name' => 'Intraosseous',   'administration_fee' => 0],
            ['route_code' => 'IT',  'route_name' => 'Intrathecal',    'administration_fee' => 0],
        ];

        foreach ($routes as $route) {
            DB::table('injection_route_fees')->insert(array_merge($route, [
                'organisation_id' => 9,
                'is_active'       => true,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('injection_route_fees');
    }
};
