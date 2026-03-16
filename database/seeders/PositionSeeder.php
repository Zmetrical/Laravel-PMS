<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        /*
         * positions.department mirrors users.department (varchar, not FK).
         * Structure: DepartmentName => [ position name, ... ]
         */
        $data = [
            'Administration' => [
                'General Manager',
                'Assistant General Manager',
                'Executive Assistant',
                'Administrative Officer',
                'Administrative Clerk',
                'Receptionist',
            ],
            'Human Resources' => [
                'HR Manager',
                'HR Supervisor',
                'HR Officer',
                'HR Assistant',
                'Payroll Officer',
                'Payroll Assistant',
                'Recruitment Officer',
                'Training Officer',
            ],
            'Accounting & Finance' => [
                'Finance Manager',
                'Accounting Supervisor',
                'Senior Accountant',
                'Junior Accountant',
                'Bookkeeper',
                'Billing Officer',
                'Cashier',
                'Accounts Payable Officer',
                'Accounts Receivable Officer',
                'Accounting Clerk',
            ],
            'Information Technology' => [
                'IT Manager',
                'Systems Administrator',
                'Software Developer',
                'Web Developer',
                'Database Administrator',
                'IT Support Specialist',
                'Network Engineer',
            ],
            'Operations' => [
                'Operations Manager',
                'Operations Supervisor',
                'Team Leader',
                'Senior Staff',
                'Staff',
                'Utility Worker',
            ],
            'Sales & Marketing' => [
                'Sales & Marketing Manager',
                'Sales Supervisor',
                'Sales Representative',
                'Account Executive',
                'Marketing Officer',
                'Marketing Assistant',
                'Encoder',
            ],
            'Procurement' => [
                'Procurement Manager',
                'Purchasing Officer',
                'Purchasing Assistant',
                'Inventory Officer',
                'Inventory Clerk',
            ],
            'Logistics & Warehouse' => [
                'Logistics Manager',
                'Warehouse Supervisor',
                'Forklift Operator',
                'Warehouse Staff',
                'Driver',
                'Helper',
            ],
            'Customer Service' => [
                'Customer Service Manager',
                'Customer Service Supervisor',
                'Customer Service Representative',
                'Technical Support Representative',
            ],
            'Security' => [
                'Security Supervisor',
                'Security Guard',
            ],
            'Maintenance' => [
                'Maintenance Supervisor',
                'Technician',
                'Electrician',
                'Janitor / Janitress',
                'Messenger',
            ],
        ];

        $rows = [];

        foreach ($data as $department => $positions) {
            foreach ($positions as $positionName) {
                $rows[] = [
                    'name'        => $positionName,
                    'department'  => $department,
                    'description' => null,
                    'status'      => 'active',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        DB::table('positions')->insertOrIgnore($rows);

        $this->command->info('Positions seeded successfully.');
    }
}