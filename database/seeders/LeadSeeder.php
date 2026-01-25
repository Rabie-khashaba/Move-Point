<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\Source;
use App\Models\Governorate;
use App\Models\User;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $leads = [
            [
                'name' => 'محمد علي',
                'phone' => '+966514567890',
                'governorate_id' => 1, // الرياض
                'source_id' => 1, // موقع إلكتروني
                'status' => 'new',
                'notes' => 'مهتم بخدمات التطوير البرمجي',
                'assigned_to' => null,
                'next_follow_up' => '2024-02-15',
                'is_active' => true,
            ],
            [
                'name' => 'فاطمة أحمد',
                'phone' => '+966515678901',
                'governorate_id' => 2, // جدة
                'source_id' => 2, // وسائل التواصل الاجتماعي
                'status' => 'contacted',
                'notes' => 'تريد نظام إدارة تعليمي',
                'assigned_to' => null,
                'next_follow_up' => '2024-03-01',
                'is_active' => true,
            ],
            [
                'name' => 'علي محمد',
                'phone' => '+966516789012',
                'governorate_id' => 5, // الدمام
                'source_id' => 3, // إعلانات جوجل
                'status' => 'qualified',
                'notes' => 'مهتم بحلول إدارة المخزون',
                'assigned_to' => null,
                'next_follow_up' => '2024-01-30',
                'is_active' => true,
            ],
            [
                'name' => 'سارة عبدالله',
                'phone' => '+966517890123',
                'governorate_id' => 3, // مكة المكرمة
                'source_id' => 4, // إعلانات فيسبوك
                'status' => 'new',
                'notes' => 'تريد نظام إدارة المرضى',
                'assigned_to' => null,
                'next_follow_up' => '2024-02-28',
                'is_active' => true,
            ],
            [
                'name' => 'أحمد خالد',
                'phone' => '+966518901234',
                'governorate_id' => 1, // الرياض
                'source_id' => 5, // إحالة من عميل
                'status' => 'qualified',
                'notes' => 'مهتم بمنصة تجارة إلكترونية',
                'assigned_to' => null,
                'next_follow_up' => '2024-01-15',
                'is_active' => true,
            ],
        ];

        foreach ($leads as $lead) {
            Lead::create($lead);
        }
    }
}
