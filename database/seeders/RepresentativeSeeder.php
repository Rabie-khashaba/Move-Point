<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Representative;
use App\Models\User;
use App\Models\Department;
use App\Models\Governorate;
use App\Models\Location;

class RepresentativeSeeder extends Seeder
{
    public function run(): void
    {
        // First, create users for representatives (only with fields that exist in User model)
        $representativeUsers = [
            [
                'phone' => '01028805930',
                'password' => bcrypt('password123'),
                'type' => 'representative',
            ],
            [
                'phone' => '01028805931',
                'password' => bcrypt('password123'),
                'type' => 'representative',
            ],
            [
                'phone' => '01028805932',
                'password' => bcrypt('password123'),
                'type' => 'representative',
            ],
        ];

        $representatives = [
            [
                'name' => 'سعد محمد',
                'phone' => '+966510123456',
                'address' => 'حي النرجس، الرياض',
                'contact' => 'saad.mohamed@company.com',
                'national_id' => '1234567896',
                'salary' => 5000.00,
                'start_date' => '2023-07-01',
                'company_id' => 1,
                'bank_account' => 'SA1234567890123456789012',
                'code' => 'REP001',
                'attachments' => null,
                'inquiry_checkbox' => false,
                'governorate_id' => 1, // الرياض
                'location_id' => 1, // المقر الرئيسي - الرياض
                'is_active' => true,
            ],
            [
                'name' => 'مريم أحمد',
                'phone' => '+966511234567',
                'address' => 'حي التحلية، جدة',
                'contact' => 'maryam.ahmed@company.com',
                'national_id' => '1234567897',
                'salary' => 4800.00,
                'start_date' => '2023-08-15',
                'company_id' => 1,
                'bank_account' => 'SA1234567890123456789013',
                'code' => 'REP002',
                'attachments' => null,
                'inquiry_checkbox' => false,
                'governorate_id' => 2, // جدة
                'location_id' => 2, // فرع جدة
                'is_active' => true,
            ],
            [
                'name' => 'عمر علي',
                'phone' => '+966512345678',
                'address' => 'حي العزيزية، مكة',
                'contact' => 'omar.ali@company.com',
                'national_id' => '1234567898',
                'salary' => 5200.00,
                'start_date' => '2023-09-10',
                'company_id' => 2,
                'bank_account' => 'SA1234567890123456789014',
                'code' => 'REP003',
                'attachments' => null,
                'inquiry_checkbox' => false,
                'governorate_id' => 3, // مكة المكرمة
                'location_id' => 4, // فرع مكة المكرمة
                'is_active' => true,
            ],
        ];

        // Create users first
        foreach ($representativeUsers as $index => $userData) {
            $user = User::create($userData);
            
            // Create representative record
            $representativeData = $representatives[$index];
            $representativeData['user_id'] = $user->id;
            Representative::create($representativeData);
        }
    }
}
