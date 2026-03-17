<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'slug' => 'starter',
                'name' => 'Starter',
                'price_per_doctor' => 1499,
                'original_price' => 1999,
                'trial_days' => 30,
                'features' => [
                    'appointments', 'billing', 'prescriptions', 'case_sheets',
                    'pet_parent_portal', 'follow_ups',
                ],
                'max_clinics' => 1,
                'max_doctors' => 3,
                'sort_order' => 1,
            ],
            [
                'slug' => 'professional',
                'name' => 'Professional',
                'price_per_doctor' => 1499,
                'original_price' => 1999,
                'trial_days' => 30,
                'features' => [
                    'appointments', 'billing', 'prescriptions', 'case_sheets',
                    'pet_parent_portal', 'follow_ups',
                    'inventory', 'reports', 'diagnostics', 'ai_support',
                ],
                'max_clinics' => 3,
                'max_doctors' => 10,
                'sort_order' => 2,
            ],
            [
                'slug' => 'enterprise',
                'name' => 'Enterprise',
                'price_per_doctor' => 1499,
                'original_price' => 1999,
                'trial_days' => 30,
                'features' => [
                    'appointments', 'billing', 'prescriptions', 'case_sheets',
                    'pet_parent_portal', 'follow_ups',
                    'inventory', 'reports', 'diagnostics', 'ai_support',
                    'ipd', 'multi_clinic', 'custom_branding', 'api_access', 'priority_support',
                ],
                'max_clinics' => null,
                'max_doctors' => null,
                'sort_order' => 3,
            ],
        ];

        foreach ($packages as $data) {
            Package::updateOrCreate(
                ['slug' => $data['slug']],
                $data,
            );
        }
    }
}
