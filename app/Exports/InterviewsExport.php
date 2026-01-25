<?php

namespace App\Exports;

use App\Models\Interview;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class InterviewsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $request = $this->request;

        $query = Interview::with(['lead.source','lead.governorate', 'message.government', 'message.location'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->whereHas('lead', function ($q) use ($search) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%");
                    });
                });
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('date_interview', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('date_interview', '<=', $request->date_to);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('employee_id'), function ($query) use ($request) {
                $query->where('assigned_to', $request->employee_id);
            })
            ->when($request->filled('governorate_id'), function ($query) use ($request) {
                $query->whereHas('message.government', function ($q) use ($request) {
                    $q->where('government_id', $request->governorate_id);
                });
            });

        return $query->get();
    }

    public function map($interview): array
    {
        return [
            $interview->id,
            $interview->lead->name ?? 'غير محدد',
            $interview->lead->phone ?? 'غير محدد',
            $interview->message->government->name ?? 'غير محدد',
            $interview->message->location->name ?? 'غير محدد',
            $interview->date_interview ? $interview->date_interview->format('Y-m-d H:i') : '-',
            $interview->status ?? '-',
            optional($interview->notes->first())->note ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'اسم العميل',
            'رقم الهاتف',
            'المحافظة',
            'المنطقة',
            'تاريخ المقابلة',
            'الحالة',
            'آخر ملاحظة',
        ];
    }
}
