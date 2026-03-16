<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $departments = [
            ['name' => 'Administration',         'code' => 'ADMIN',  'description' => 'Handles overall company administration and executive support.'],
            ['name' => 'Human Resources',        'code' => 'HR',     'description' => 'Manages recruitment, payroll, employee relations, and compliance.'],
            ['name' => 'Accounting & Finance',   'code' => 'ACCT',   'description' => 'Oversees financial reporting, bookkeeping, billing, and cash management.'],
            ['name' => 'Information Technology', 'code' => 'IT',     'description' => 'Manages systems, networks, and software development.'],
            ['name' => 'Operations',             'code' => 'OPS',    'description' => 'Oversees day-to-day operational activities across all branches.'],
            ['name' => 'Sales & Marketing',      'code' => 'SALES',  'description' => 'Drives revenue through sales efforts and marketing campaigns.'],
            ['name' => 'Procurement',            'code' => 'PROC',   'description' => 'Handles purchasing, supplier management, and inventory control.'],
            ['name' => 'Logistics & Warehouse',  'code' => 'LOG',    'description' => 'Manages delivery, warehousing, and supply chain operations.'],
            ['name' => 'Customer Service',       'code' => 'CS',     'description' => 'Handles client concerns, support tickets, and after-sales service.'],
            ['name' => 'Security',               'code' => 'SEC',    'description' => 'Ensures premises and personnel safety across all branches.'],
            ['name' => 'Maintenance',            'code' => 'MAINT',  'description' => 'Handles facility upkeep, repairs, and utilities management.'],
        ];

        foreach ($departments as &$dept) {
            $dept['branch']            = null;
            $dept['head_employee_ids'] = null;
            $dept['status']            = 'active';
            $dept['created_at']        = $now;
            $dept['updated_at']        = $now;
        }

        DB::table('departments')->insertOrIgnore($departments);

        $this->command->info('Departments seeded successfully.');
    }
}