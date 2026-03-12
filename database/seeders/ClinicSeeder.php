<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clinic;

class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        Clinic::updateOrCreate(
            ['name' => 'ClinicDesq Main Clinic'],
            [
                'phone'      => '9876543210',
                'email'      => 'clinic@clinicdesq.com',
                'address'    => 'MG Road',
                'city'       => 'Bangalore',
                'state'      => 'Karnataka',
                'pincode'    => '560001',
                'gst_number' => '29ABCDE1234F1Z5',
            ]
        );
    }
}
