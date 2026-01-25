<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supervisor;
use App\Models\User;
use App\Models\Department;

class SupervisorSeeder extends Seeder
{
    public function run(): void
    {
        // First, create users for supervisors (only with fields that exist in User model)
        $supervisorUsers = [
            [
                'phone' => '01028805927',
                'password' => bcrypt('password123'),
                'type' => 'supervisor',
            ],
            [
                'phone' => '01028805928',
                'password' => bcrypt('password123'),
                'type' => 'supervisor',
            ],
            [
                'phone' => '01028805929',
                'password' => bcrypt('password123'),
                'type' => 'supervisor',
            ],
        ];

        $supervisors = [
            [
                'name' => 'عبدالرحمن أحمد',
                'phone' => '+966506789012',
                'address' => 'حي النرجس، الرياض',
                'contact' => 'abdulrahman.ahmed@company.com',
                'national_id' => '1234567893',
                'salary' => 12000.00,
                'start_date' => '2022-08-15',
                'department_id' => 4, // المبيعات
                'location_id' => 1, // المقر الرئيسي - الرياض
                'is_active' => true,
            ],
            [
                'name' => 'نورا محمد',
                'phone' => '+966507890123',
                'address' => 'حي التحلية، جدة',
                'contact' => 'nora.mohamed@company.com',
                'national_id' => '1234567894',
                'salary' => 11000.00,
                'start_date' => '2022-05-10',
                'department_id' => 1, // المالية
                'location_id' => 2, // فرع جدة
                'is_active' => true,
            ],
            [
                'name' => 'خالد علي',
                'phone' => '+966508901234',
                'address' => 'حي العليا، الرياض',
                'contact' => 'khalid.ali@company.com',
                'national_id' => '1234567895',
                'salary' => 10500.00,
                'start_date' => '2022-09-20',
                'department_id' => 2, // التسويق
                'location_id' => 1, // المقر الرئيسي - الرياض
                'is_active' => true,
            ],
        ];

        // Create users first
        foreach ($supervisorUsers as $index => $userData) {
            $user = User::create($userData);
            
            // Create supervisor record
            $supervisorData = $supervisors[$index];
            $supervisorData['user_id'] = $user->id;
            Supervisor::create($supervisorData);
        }
    }
}
