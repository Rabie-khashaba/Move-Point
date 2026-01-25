<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Company;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    /**
     * Display a listing of the debts.
     */
    public function index(Request $request)
    {
        $query = Debt::with(['employee', 'representative', 'supervisor'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', function ($employeeQuery) use ($search) {
                    $employeeQuery->where('name', 'like', "%{$search}%")->orwhere('phone', 'like', "%{$search}%");
                })->orWhereHas('representative', function ($representativeQuery) use ($search) {
                    $representativeQuery->where('name', 'like', "%{$search}%")->orwhere('phone', 'like', "%{$search}%");
                })->orWhereHas('supervisor', function ($supervisorQuery) use ($search) {
                    $supervisorQuery->where('name', 'like', "%{$search}%")->orwhere('phone', 'like', "%{$search}%");
                });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($companyId = $request->input('company_id')) {
            $query->where(function ($q) use ($companyId) {
                $q->whereHas('representative.company', fn($c) => $c->where('id', $companyId));
            });
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $debts = $query->paginate(20)->appends($request->query());

        $companies = Company::all();

        return view('debts.index', compact('debts', 'companies'));
    }

    /**
     * Toggle the debt status between paid/unpaid.
     */
    public function toggleStatus(Debt $debt)
    {
        $debt->update([
            'status' => $debt->status === 'سدد' ? 'لم يسدد' : 'سدد',
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة المديونية بنجاح.');
    }
}

