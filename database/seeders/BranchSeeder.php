<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $branches = [
            [
                'name'           => 'Meycauayan Main',
                'code'           => 'MCY-MAIN',
                'address'        => 'Malhacan Road, Malhacan',
                'city'           => 'Meycauayan City, Bulacan',
                'contact_number' => null,
                'email'          => null,
                'manager_name'   => null,
                'is_main'        => true,
                'status'         => 'active',
            ],
            [
                'name'           => 'Meycauayan Branch 2',
                'code'           => 'MCY-B2',
                'address'        => null,
                'city'           => 'Meycauayan City, Bulacan',
                'contact_number' => null,
                'email'          => null,
                'manager_name'   => null,
                'is_main'        => false,
                'status'         => 'active',
            ],
            [
                'name'           => 'Valenzuela Branch',
                'code'           => 'VLZ',
                'address'        => null,
                'city'           => 'Valenzuela City, Metro Manila',
                'contact_number' => null,
                'email'          => null,
                'manager_name'   => null,
                'is_main'        => false,
                'status'         => 'active',
            ],
            [
                'name'           => 'Marilao Branch',
                'code'           => 'MRL',
                'address'        => null,
                'city'           => 'Marilao, Bulacan',
                'contact_number' => null,
                'email'          => null,
                'manager_name'   => null,
                'is_main'        => false,
                'status'         => 'active',
            ],
            [
                'name'           => 'Caloocan Branch',
                'code'           => 'CLN',
                'address'        => null,
                'city'           => 'Caloocan City, Metro Manila',
                'contact_number' => null,
                'email'          => null,
                'manager_name'   => null,
                'is_main'        => false,
                'status'         => 'active',
            ],
            [
                'name'           => 'Quezon City Branch',
                'code'           => 'QC',
                'address'        => null,
                'city'           => 'Quezon City, Metro Manila',
                'contact_number' => null,
                'email'          => null,
                'manager_name'   => null,
                'is_main'        => false,
                'status'         => 'active',
            ],
        ];

        foreach ($branches as &$branch) {
            $branch['created_at'] = $now;
            $branch['updated_at'] = $now;
        }

        DB::table('branches')->insertOrIgnore($branches);

        $this->command->info('Branches seeded successfully.');
    }
}