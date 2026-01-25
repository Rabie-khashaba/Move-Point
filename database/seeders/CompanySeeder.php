<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'شركة التقنية المتقدمة',
                'email' => 'info@tech-advanced.com',
                'phone' => '+966501234567',
                'address' => 'شارع الملك فهد، الرياض',
                'website' => 'https://tech-advanced.com',
                'description' => 'شركة متخصصة في تطوير البرمجيات وحلول تقنية المعلومات',
                'is_active' => true,
            ],
            [
                'name' => 'مؤسسة الخدمات المالية',
                'email' => 'contact@financial-services.com',
                'phone' => '+966502345678',
                'address' => 'شارع التحلية، جدة',
                'website' => 'https://financial-services.com',
                'description' => 'مؤسسة تقدم خدمات مالية واستشارات استثمارية',
                'is_active' => true,
            ],
            [
                'name' => 'شركة التصنيع الحديث',
                'email' => 'info@modern-manufacturing.com',
                'phone' => '+966503456789',
                'address' => 'المنطقة الصناعية، الدمام',
                'website' => 'https://modern-manufacturing.com',
                'description' => 'شركة تصنيع متخصصة في المنتجات الصناعية',
                'is_active' => true,
            ],
            [
                'name' => 'مؤسسة الخدمات الصحية',
                'email' => 'info@health-services.com',
                'phone' => '+966504567890',
                'address' => 'شارع الملك عبدالله، مكة',
                'website' => 'https://health-services.com',
                'description' => 'مؤسسة تقدم خدمات رعاية صحية متكاملة',
                'is_active' => true,
            ],
            [
                'name' => 'شركة التجارة الدولية',
                'email' => 'contact@international-trade.com',
                'phone' => '+966505678901',
                'address' => 'شارع الملك خالد، المدينة المنورة',
                'website' => 'https://international-trade.com',
                'description' => 'شركة تجارة دولية متخصصة في الاستيراد والتصدير',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
