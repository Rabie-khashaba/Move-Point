<?php

namespace App\Http\Controllers;

use App\Imports\DebtSheetImport;
use App\Models\Company;
use App\Models\Debt;
use App\Models\DebtSheet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $query = Debt::with(['employee', 'representative', 'supervisor'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', function ($employeeQuery) use ($search) {
                    $employeeQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('representative', function ($representativeQuery) use ($search) {
                    $representativeQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('supervisor', function ($supervisorQuery) use ($search) {
                    $supervisorQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
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

    public function index2(Request $request)
    {
        $sheetQuery = DebtSheet::query()->latest();
        if ($sheetSearch = $request->input('sheet_search')) {
            $sheetQuery->where('star_id', 'like', "%{$sheetSearch}%");
        }

        $debtSheets = $sheetQuery->paginate(20)->appends($request->query());

        return view('debts.index2', compact('debtSheets'));
    }

    public function toggleStatus(Debt $debt)
    {
        $debt->update([
            'status' => $debt->status === 'سدد' ? 'لم يسدد' : 'سدد',
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة المديونية بنجاح.');
    }

    public function storeSheet(Request $request)
    {
        $validated = $request->validate([
            'star_id' => 'required|string|max:100|unique:debt_sheets,star_id',
            'shortage' => 'nullable|numeric',
            'credit_note' => 'nullable|numeric',
            'advances' => 'nullable|numeric',
        ]);

        DebtSheet::create([
            'star_id' => trim($validated['star_id']),
            'shortage' => $validated['shortage'] ?? 0,
            'credit_note' => $validated['credit_note'] ?? 0,
            'advances' => $validated['advances'] ?? 0,
        ]);

        return redirect()->route('debts.index2')->with('success', 'تم إضافة سجل المديونية الجديد بنجاح.');
    }

    public function updateSheet(Request $request, DebtSheet $debtSheet)
    {
        $validated = $request->validate([
            'star_id' => [
                'required',
                'string',
                'max:100',
                Rule::unique('debt_sheets', 'star_id')->ignore($debtSheet->id),
            ],
            'shortage' => 'nullable|numeric',
            'credit_note' => 'nullable|numeric',
            'advances' => 'nullable|numeric',
        ]);

        $debtSheet->update([
            'star_id' => trim($validated['star_id']),
            'shortage' => $validated['shortage'] ?? 0,
            'credit_note' => $validated['credit_note'] ?? 0,
            'advances' => $validated['advances'] ?? 0,
        ]);

        return redirect()->route('debts.index2')->with('success', 'تم تعديل سجل المديونية الجديد بنجاح.');
    }

    public function destroySheet(DebtSheet $debtSheet)
    {
        $debtSheet->delete();

        return redirect()->route('debts.index2')->with('success', 'تم حذف سجل المديونية الجديد بنجاح.');
    }

    public function importSheet(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new DebtSheetImport();
        $import->import($request->file('file'));

        $message = 'تم استيراد ملف المديونيات الجديد بنجاح.';
        $message .= " تم تحديث/إضافة {$import->getImportedCount()} صف.";

        return redirect()
            ->route('debts.index2')
            ->with('success', $message)
            ->with('import_failures', $import->getFailures());
    }
}
