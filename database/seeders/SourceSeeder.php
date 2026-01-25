<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Source;

class SourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            ['name' => 'موقع إلكتروني'],
            ['name' => 'وسائل التواصل الاجتماعي'],
            ['name' => 'إعلانات جوجل'],
            ['name' => 'إعلانات فيسبوك'],
            ['name' => 'إحالة من عميل'],
            ['name' => 'معارض تجارية'],
            ['name' => 'مكالمة هاتفية'],
            ['name' => 'بريد إلكتروني'],
            ['name' => 'زيارة مكتبية'],
            ['name' => 'شركاء أعمال'],
            ['name' => 'موظفين سابقين'],
            ['name' => 'أخرى'],
        ];

        foreach ($sources as $source) {
            Source::create($source);
        }
    }
}
