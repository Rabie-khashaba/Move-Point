<?php

namespace App\Exports;

use App\Models\Representative;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RepresentativesResigneExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $q = \App\Models\Representative::where('is_active', 0)->with(['company', 'user', 'supervisors', 'location', 'governorate'])->withCount('deliveryDeposits');
        // نفس الفلاتر
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $q->where(function ($x) use ($search) {
                $x->where('name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('national_id', 'like', "%$search%");
            });
        }

        if ($this->request->filled('date_from')) {
            $q->whereDate('created_at', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $q->whereDate('created_at', '<=', $this->request->date_to);
        }

        if ($this->request->filled('company_id')) {
            $q->where('company_id', $this->request->company_id);
        }

        if ($this->request->filled('employee_id')) {
            $q->where('employee_id', $this->request->employee_id);
        }

        return $q->get()->map(function ($r) {
            return [
                $r->id,
                $r->name,
                $r->phone,
                $r->national_id ?? 'غير محدد',
                $r->company->name ?? 'غير محدد',
                optional($r->current_supervisor)->name ?? 'غير محدد',
                $r->is_training ? 'حضر التدريب' : 'لم يحضر',
                $r->delivery_deposits_count,
                $r->created_at->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'اسم المندوب',
            'رقم الهاتف',
            'الرقم القومي',
            'الشركة',
            'المشرف الحالي',
            'حالة التدريب',
            'عدد الإيداعات',
            'تاريخ الإضافة',
        ];
    }
}
