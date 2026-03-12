<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clinic;
use App\Models\Vet;

class ClinicVetSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = Clinic::where('name', 'ClinicDesq Main Clinic')->first();

        if (! $clinic) {
            return;
        }

        $rahul = Vet::where('phone', '9000000001')->first();
        $neha  = Vet::where('phone', '9000000002')->first();

        $attach = [];

        if ($rahul) {
            $attach[$rahul->id] = ['role' => 'owner'];
        }

        if ($neha) {
            $attach[$neha->id] = ['role' => 'vet'];
        }

        if (! empty($attach)) {
            $clinic->vets()->syncWithoutDetaching($attach);
        }
    }
}
