<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'clinic_id']);
        });

        // Seed existing users with their current clinic_id
        $users = DB::table('users')->whereNotNull('clinic_id')->get(['id', 'clinic_id']);
        foreach ($users as $user) {
            DB::table('clinic_user')->insert([
                'user_id'    => $user->id,
                'clinic_id'  => $user->clinic_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_user');
    }
};
