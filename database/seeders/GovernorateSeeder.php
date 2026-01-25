<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Governorate;

class GovernorateSeeder extends Seeder
{
    public function run(): void
    {
        $governorates = [
            ['name' => 'الرياض'],
            ['name' => 'جدة'],
            ['name' => 'مكة المكرمة'],
            ['name' => 'المدينة المنورة'],
            ['name' => 'الدمام'],
            ['name' => 'الخبر'],
            ['name' => 'الظهران'],
            ['name' => 'القطيف'],
            ['name' => 'الأحساء'],
            ['name' => 'تبوك'],
            ['name' => 'أبها'],
            ['name' => 'جازان'],
            ['name' => 'نجران'],
            ['name' => 'الباحة'],
            ['name' => 'الجوف'],
            ['name' => 'حائل'],
            ['name' => 'القصيم'],
            ['name' => 'عسير'],
            ['name' => 'الحدود الشمالية'],
        ];

        foreach ($governorates as $governorate) {
            Governorate::create($governorate);
        }
    }
}
