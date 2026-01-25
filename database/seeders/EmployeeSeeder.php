<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // First, create users for employees (only with fields that exist in User model)
        $employeeUsers = [
            [
                'phone' => '01028805922',
                'password' => bcrypt('password123'),
                'type' => 'employee',
            ],
            [
                'phone' => '01028805923',
                'password' => bcrypt('password123'),
                'type' => 'employee',
            ],
            [
                'phone' => '01028805924',
                'password' => bcrypt('password123'),
                'type' => 'employee',
            ],
        ];

        $employees = [
            [
                'name' => 'أحمد محمد',
                'phone' => '+966501234567',
                'address' => 'شارع الملك فهد، الرياض',
                'contact' => 'ahmed.mohamed@company.com',
                'national_id' => '1234567890',
                'salary' => 8000.00,
                'start_date' => '2023-01-15',
                'department_id' => 4, // المبيعات
                'attachments' => null,
                'is_active' => true,
            ],
            [
                'name' => 'فاطمة علي',
                'phone' => '+966502345678',
                'address' => 'شارع التحلية، جدة',
                'contact' => 'fatima.ali@company.com',
                'national_id' => '1234567891',
                'salary' => 6000.00,
                'start_date' => '2023-03-20',
                'department_id' => 1, // المالية
                'attachments' => null,
                'is_active' => true,
            ],
            [
                'name' => 'محمد عبدالله',
                'phone' => '+966503456789',
                'address' => 'حي العليا، الرياض',
                'contact' => 'mohamed.abdullah@company.com',
                'national_id' => '1234567892',
                'salary' => 9000.00,
                'start_date' => '2023-06-10',
                'department_id' => 2, // التسويق
                'attachments' => null,
                'is_active' => true,
            ],
        ];

        // Create users first
        foreach ($employeeUsers as $index => $userData) {
            $user = User::create($userData);
            
            // Create employee record
            $employeeData = $employees[$index];
            $employeeData['user_id'] = $user->id;
            Employee::create($employeeData);
        }
    }
}
