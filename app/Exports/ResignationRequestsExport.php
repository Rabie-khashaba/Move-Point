<?php

namespace App\Exports;

use App\Models\ResignationRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResignationRequestsExport implements FromCollection, WithHeadings
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $resignations = ResignationRequest::with([
                'employee.department',
                'employee.company',
                'employee.debits',
                'representative.company',
                'representative.debits',
                'supervisor.company',
                'supervisor.debits',
                'approver',
            ])
            ->when($this->request->search, function ($query, $search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('representative', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('supervisor', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($this->request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($this->request->source, function ($query, $source) {
                if ($source === 'app') {
                    $query->whereNull('source');
                } else {
                    $query->where('source', $source);
                }
            })
            ->when($this->request->company_id, function ($query, $companyId) {
                $query->where(function ($q) use ($companyId) {
                    $q->whereHas('employee', function ($emp) use ($companyId) {
                        $emp->where('company_id', $companyId);
                    })
                    ->orWhereHas('representative', function ($rep) use ($companyId) {
                        $rep->where('company_id', $companyId);
                    })
                    ->orWhereHas('supervisor', function ($sup) use ($companyId) {
                        $sup->where('company_id', $companyId);
                    });
                });
            })
            ->when($this->request->date_from, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($this->request->date_to, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->get();

        return $resignations->map(function ($resignation) {
            $name = $resignation->employee?->name
                ?? $resignation->representative?->name
                ?? $resignation->supervisor?->name
                ?? 'غير محدد';

            $code = $resignation->employee?->code
                ?? $resignation->representative?->code
                ?? $resignation->supervisor?->code
                ?? 'غير محدد';

            $phone = $resignation->employee?->phone
                ?? $resignation->representative?->phone
                ?? $resignation->supervisor?->phone
                ?? 'غير محدد';

            $company = $resignation->employee?->company?->name
                ?? $resignation->representative?->company?->name
                ?? $resignation->supervisor?->company?->name
                ?? 'غير محدد';

            $department = $resignation->employee?->department?->name ?? 'غير محدد';
            $resignationDate = $resignation->resignation_date ? $resignation->resignation_date->format('Y-m-d') : '-';
            $lastDay = $resignation->last_working_day ? $resignation->last_working_day->format('Y-m-d') : '-';
            $approvedAt = $resignation->approved_at ? $resignation->approved_at->format('Y-m-d') : 'لم تتم المعالجة';
            $requestDate = $resignation->created_at ? $resignation->created_at->format('Y-m-d') : '-';

            $source = match ($resignation->source) {
                'training_session' => 'محاضرات التدريب',
                'work_start' => 'بدء العمل',
                default => 'APP',
            };

            $debit = $resignation->employee?->debits?->where('status', 'لم يسدد')->first()
                ?? $resignation->representative?->debits?->where('status', 'لم يسدد')->first()
                ?? $resignation->supervisor?->debits?->where('status', 'لم يسدد')->first();
            $debtValue = $debit?->loan_amount ?? 'لا توجد مديونية';

            return [
                $name,
                $code,
                $phone,
                $company,
                $department,
                $resignationDate,
                $lastDay,
                $resignation->reason,
                $source,
                $debtValue,
                $resignation->status_text ?? $resignation->status,
                $requestDate,
                $resignation->approver?->name ?? 'غير محدد',
                $approvedAt,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'الاسم',
            'الكود',
            'رقم الهاتف',
            'الشركة',
            'القسم',
            'تاريخ الاستقالة',
            'آخر يوم عمل',
            'السبب',
            'المصدر',
            'المديونية',
            'الحالة',
            'تاريخ الطلب',
            'تمت الموافقة بواسطة',
            'تاريخ الموافقة',
        ];
    }
}
