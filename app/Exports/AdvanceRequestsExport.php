<?php

namespace App\Exports;

use App\Models\AdvanceRequest;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class AdvanceRequestsExport implements FromView
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $request = (object) $this->filters;

        $query = AdvanceRequest::with(['representative.governorate', 'representative.location', 'employee', 'supervisor']);

        // 🔍 البحث بالاسم
        if (!empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('requester_name', 'like', "%{$search}%")
                    ->orWhereHas('representative', fn($r) => $r->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('employee', fn($e) => $e->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('supervisor', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        // ⚙️ الحالة
        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        // 📅 التاريخ
        if (!empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if (!empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 👥 نوع الموظف
        if (!empty($request->role)) {
            $role = $request->role;
            $query->whereNotNull("{$role}_id");
        }

        $advances = $query->get();

        return view('exports.advance_requests', compact('advances'));
    }
}
