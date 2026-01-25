<?php

namespace App\Exports;

use App\Models\Representative;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RepresentativesNotExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Representative::where('status', 0)
            ->where('is_active', 1)
            ->with(['company', 'governorate'])
            ->withCount('deliveryDeposits')
            ->get();
    }

    public function headings(): array
    {
        return [
            'الاسم',
            'رقم الهاتف',
            'الشركة',
            'المحافظة',
            'عدد الإيصالات',
            'التدريب',
            'الأوراق الناقصة',
            'جاهز للعمل',
            'تاريخ الإنشاء',
        ];
    }

    public function map($r): array
    {
        // حساب الأوراق الناقصة
        $missing = count($r->missingDocs()) > 0
            ? implode(', ', $r->missingDocs())
            : 'كاملة';

        // جاهز للعمل؟
        $ready = ($r->delivery_deposits_count == 7 && count($r->missingDocs()) == 0 && $r->is_training == 1)
            ? 'نعم'
            : 'لا';

        return [
            $r->name,
            $r->phone ?? 'غير محدد',
            $r->company->name ?? 'غير محدد',
            $r->governorate->name ?? 'غير محدد',
            $r->delivery_deposits_count,
            $r->is_training ? 'تم الحضور' : 'لم يحضر',
            $missing,
            $ready,
            $r->created_at->format('Y-m-d'),
        ];
    }
}
