<?php

namespace App\Exports;

use App\Models\BankAccount;
use Illuminate\Contracts\Support\Arrayable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BankAccountsExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;

    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $request = (object) $this->filters;

        $query = BankAccount::with(['representative.governorate', 'representative.location', 'bank']);

        if (!empty($request->bank_ids) && is_array($request->bank_ids)) {
            $query->whereIn('bank_id', $request->bank_ids);
        }

        if (!empty($request->governorate_id)) {
            $query->whereHas('representative', function ($q) use ($request) {
                $q->where('governorate_id', $request->governorate_id);
            });
        }

        if (!empty($request->location_id)) {
            $query->whereHas('representative', function ($q) use ($request) {
                $q->where('location_id', $request->location_id);
            });
        }

        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        if (!empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('representative', function ($repQuery) use ($search) {
                    $repQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('national_id', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%');
                })
                ->orWhere('account_number', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'اسم المندوب',
            'رقم الهاتف',
            'الرقم القومي',
            'كود المندوب',
            'المحافظة',
            'المنطقة',
            'الحالة',
            'اسم صاحب الحساب',
            'اسم البنك',
            'رقم الحساب',
            'تاريخ الإنشاء',
        ];
    }

    public function map($account): array
    {
        $representative = $account->representative;

        return [
            $representative->name ?? 'غير محدد',
            $representative->phone ?? '-',
            $representative->national_id ?? '-',
            $representative->code ?? 'غير محدد',
            $representative?->governorate?->name ?? 'غير محدد',
            $representative?->location?->name ?? 'غير محدد',
            $account->status,
            $account->account_owner_name,
            $account->bank?->name ?? 'غير محدد',
            $account->account_number,
            optional($account->created_at)->format('Y-m-d H:i'),
        ];
    }
}

