<?php

namespace App\Exports;

use App\Models\Representative;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WalletAccountsExport implements FromCollection, WithHeadings, WithMapping
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

        $query = Representative::with(['governorate', 'location']);

        if (!empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('bank_account', 'like', "%{$search}%");
            });
        }

        if (!empty($request->governorate_id)) {
            $query->where('governorate_id', $request->governorate_id);
        }

        if (!empty($request->location_id)) {
            $query->where('location_id', $request->location_id);
        }

        if (!empty($request->wallet_status)) {
            if ($request->wallet_status === 'with') {
                $query->whereNotNull('bank_account')->where('bank_account', '!=', '');
            } elseif ($request->wallet_status === 'without') {
                $query->where(function ($q) {
                    $q->whereNull('bank_account')->orWhere('bank_account', '');
                });
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'اسم المندوب',
            'رقم الهاتف',
            'كود المندوب',
            'المحافظة',
            'المنطقة',
            'رقم المحفظة',
        ];
    }

    public function map($rep): array
    {
        return [
            $rep->name ?? 'غير محدد',
            $rep->phone ?? '-',
            $rep->code ?? 'غير محدد',
            $rep?->governorate?->name ?? 'غير محدد',
            $rep?->location?->name ?? 'غير محدد',
            $rep->bank_account ?? 'غير محدد',
        ];
    }
}
