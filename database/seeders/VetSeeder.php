<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vet;

class VetSeeder extends Seeder
{
    public function run(): void
    {
        Vet::updateOrCreate(
            ['phone' => '9000000001'],
            [
                'name' => 'Dr. Rahul Sharma',
                'email' => 'rahul@clinicdesq.com',
                'registration_number' => 'KA12345',
                'specialization' => 'Small Animal Medicine',
                'is_active' => true,
            ]
        );

        Vet::updateOrCreate(
            ['phone' => '9000000002'],
            [
                'name' => 'Dr. Neha Verma',
                'email' => 'neha@clinicdesq.com',
                'registration_number' => 'KA67890',
                'specialization' => 'Surgery',
                'is_active' => true,
            ]
        );
    }
}
