<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Run seeders in order to maintain referential integrity
        $this->call([
            AdminUserSeeder::class,
            CompanySeeder::class,
            DepartmentSeeder::class,
            GovernorateSeeder::class,
            LocationSeeder::class,
            SourceSeeder::class,
            SupervisorSeeder::class,
            RepresentativeSeeder::class,
            EmployeeSeeder::class,
            LeadSeeder::class,
        ]);
    }
}