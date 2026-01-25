<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create additional users with different roles
        $users = [
            [
                'name' => 'أحمد محمد',
                'email' => 'ahmed.mohamed@company.com',
                'phone' => '01028805922',
                'password' => Hash::make('password123'),
                'type' => 'employee',
            ],
            [
                'name' => 'فاطمة علي',
                'email' => 'fatima.ali@company.com',
                'phone' => '01028805923',
                'password' => Hash::make('password123'),
                'type' => 'employee',
            ],
            [
                'name' => 'محمد عبدالله',
                'email' => 'mohamed.abdullah@company.com',
                'phone' => '01028805924',
                'password' => Hash::make('password123'),
                'type' => 'supervisor',
            ],
            [
                'name' => 'سارة خالد',
                'email' => 'sara.khalid@company.com',
                'phone' => '01028805925',
                'password' => Hash::make('password123'),
                'type' => 'representative',
            ],
            [
                'name' => 'علي حسن',
                'email' => 'ali.hassan@company.com',
                'phone' => '01028805926',
                'password' => Hash::make('password123'),
                'type' => 'employee',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
            
            // Assign appropriate role based on type
            switch ($userData['type']) {
                case 'employee':
                    $role = Role::firstOrCreate(['name' => 'employee']);
                    break;
                case 'supervisor':
                    $role = Role::firstOrCreate(['name' => 'supervisor']);
                    break;
                case 'representative':
                    $role = Role::firstOrCreate(['name' => 'representative']);
                    break;
                default:
                    $role = Role::firstOrCreate(['name' => 'user']);
            }
            
            $user->assignRole($role);
        }
    }
}
