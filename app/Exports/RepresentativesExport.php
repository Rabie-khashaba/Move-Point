<?php

namespace App\Exports;

use App\Models\Representative;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RepresentativesExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $q = Representative::where('status', 1)
            ->where('is_active', 1)
            ->with(['company', 'employee'])
            ->withCount('deliveryDeposits');

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

        return $q->get()->map(function ($rep) {
            return [
                'اسم المندوب'          => $rep->name,
                'الهاتف'               => $rep->phone,
                'الشركة'               => $rep->company->name ?? 'غير محدد',
                'كود المندوب'          => $rep->code,
                'الموظف'               => $rep->employee->name ?? 'غير محدد',
                'المشرف' => optional($rep->current_supervisor)->name ?? 'غير محدد',
                'عدد الإيصالات'        => $rep->delivery_deposits_count,
                'تاريخ الإنشاء'        => $rep->created_at->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'اسم المندوب',
            'الهاتف',
            'الشركة',
            'كود المندوب',
            'الموظف',
            'المشرف',
            'عدد الإيصالات',
            'تاريخ الإنشاء',
        ];
    }
}
