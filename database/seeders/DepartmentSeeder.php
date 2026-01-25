<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            ['name' => 'المالية', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'التسويق', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الموارد البشرية', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'المبيعات', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
