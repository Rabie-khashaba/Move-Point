<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'governorate_id' => 1, // الرياض
                'name' => 'المقر الرئيسي - الرياض',
                'location' => 'حي النرجس',
                'address' => 'شارع الملك فهد، حي النرجس، الرياض',
                'phone' => '+966501234567',
                'email' => 'info@company.com',
                'description' => 'المقر الرئيسي للشركة في الرياض',
                'is_active' => true,
            ],
            [
                'governorate_id' => 2, // جدة
                'name' => 'فرع جدة',
                'location' => 'حي الكورنيش',
                'address' => 'شارع التحلية، حي الكورنيش، جدة',
                'phone' => '+966502345678',
                'email' => 'jeddah@company.com',
                'description' => 'فرع الشركة في جدة',
                'is_active' => true,
            ],
            [
                'governorate_id' => 5, // الدمام
                'name' => 'فرع الدمام',
                'location' => 'المنطقة الصناعية',
                'address' => 'المنطقة الصناعية، الدمام',
                'phone' => '+966503456789',
                'email' => 'dammam@company.com',
                'description' => 'فرع الشركة في الدمام',
                'is_active' => true,
            ],
            [
                'governorate_id' => 3, // مكة المكرمة
                'name' => 'فرع مكة المكرمة',
                'location' => 'حي العزيزية',
                'address' => 'شارع الملك عبدالله، حي العزيزية، مكة',
                'phone' => '+966504567890',
                'email' => 'makkah@company.com',
                'description' => 'فرع الشركة في مكة المكرمة',
                'is_active' => true,
            ],
            [
                'governorate_id' => 4, // المدينة المنورة
                'name' => 'فرع المدينة المنورة',
                'location' => 'حي قباء',
                'address' => 'شارع الملك خالد، حي قباء، المدينة المنورة',
                'phone' => '+966505678901',
                'email' => 'madinah@company.com',
                'description' => 'فرع الشركة في المدينة المنورة',
                'is_active' => true,
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
