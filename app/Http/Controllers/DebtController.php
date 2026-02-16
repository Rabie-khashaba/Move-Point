<?php

namespace App\Http\Controllers;

use App\Imports\DebtSheetImport;
use App\Models\Company;
use App\Models\Debt;
use App\Models\DebtSheet;
use App\Models\Representative;
use Illuminate\Http\Request;

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
        $sheetQuery = DebtSheet::query()->orderByDesc('sheet_date')->orderByDesc('updated_at');

        if ($sheetSearch = $request->input('sheet_search')) {
            $sheetQuery->where('star_id', 'like', "%{$sheetSearch}%");
        }

        if ($sheetMonth = $request->input('sheet_month')) {
            [$year, $month] = array_pad(explode('-', $sheetMonth), 2, null);
            if ($year && $month) {
                $sheetQuery->whereYear('sheet_date', (int) $year)
                    ->whereMonth('sheet_date', (int) $month);
            }
        }

        if ($dateFrom = $request->input('date_from')) {
            $sheetQuery->whereDate('sheet_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $sheetQuery->whereDate('sheet_date', '<=', $dateTo);
        }

        if ($companyId = $request->input('company_id')) {
            $sheetQuery->whereIn('star_id', Representative::query()
                ->where('company_id', $companyId)
                ->whereNotNull('code')
                ->select('code'));
        }

        $companiesQuery = Company::query()->orderBy('name');
        if ($companyId = $request->input('company_id')) {
            $companiesQuery->where('id', $companyId);
        }
        $companies = $companiesQuery->get(['id', 'name']);

        $companyStats = [];
        foreach ($companies as $company) {
            $companyStats[(int) $company->id] = [
                'company_name' => $company->name,
                'debt_total' => 0.0,
                'representatives_count' => 0,
            ];
        }

        $debtTotalsByCode = (clone $sheetQuery)
            ->reorder()
            ->whereNotNull('star_id')
            ->selectRaw('star_id, COALESCE(SUM(shortage + credit_note + advances), 0) as debt_total')
            ->groupBy('star_id')
            ->get();

        $representativesByCode = Representative::query()
            ->with('company:id,name')
            ->whereNotNull('code')
            ->whereNotNull('company_id')
            ->whereIn('company_id', $companies->pluck('id'))
            ->get(['code', 'company_id'])
            ->keyBy('code');

        foreach ($debtTotalsByCode as $debtRow) {
            $rep = $representativesByCode->get($debtRow->star_id);
            if (!$rep) {
                continue;
            }

            $companyId = (int) $rep->company_id;
            if (isset($companyStats[$companyId])) {
                $companyStats[$companyId]['debt_total'] += (float) $debtRow->debt_total;
                $companyStats[$companyId]['representatives_count']++;
            }
        }

        $companyStats = collect($companyStats)
            ->sortByDesc('debt_total')
            ->values();

        $debtSheets = $sheetQuery->paginate(20)->appends($request->query());
        $starIds = $debtSheets->getCollection()
            ->pluck('star_id')
            ->filter()
            ->unique()
            ->values();

        $companyNamesByCode = Representative::query()
            ->with('company:id,name')
            ->whereIn('code', $starIds)
            ->get(['code', 'company_id'])
            ->keyBy('code')
            ->map(fn($rep) => $rep->company->name ?? '-');

        $debtSheets->getCollection()->transform(function ($sheet) use ($companyNamesByCode) {
            $sheet->company_name = $companyNamesByCode[$sheet->star_id] ?? '-';
            return $sheet;
        });

        return view('debts.index2', compact(
            'debtSheets',
            'companies',
            'companyStats'
        ));
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
            'star_id' => 'required|string|max:100',
            'shortage' => 'nullable|numeric',
            'credit_note' => 'nullable|numeric',
            'advances' => 'nullable|numeric',
            'sheet_date' => 'required|date_format:Y-m',
        ]);

        $shortage = (float) ($validated['shortage'] ?? 0);
        $creditNote = (float) ($validated['credit_note'] ?? 0);
        $advances = (float) ($validated['advances'] ?? 0);

        DebtSheet::create([
            'star_id' => trim($validated['star_id']),
            'shortage' => $shortage,
            'credit_note' => $creditNote,
            'advances' => $advances,
            'status' => $this->resolveDebtStatus($shortage, $creditNote, $advances),
            'sheet_date' => $validated['sheet_date'] . '-01',
        ]);

        return redirect()->route('debts.index2')->with('success', 'تم إضافة سجل المديونية الجديد بنجاح.');
    }

    public function updateSheet(Request $request, DebtSheet $debtSheet)
    {
        $validated = $request->validate([
            'star_id' => 'required|string|max:100',
            'shortage' => 'nullable|numeric',
            'credit_note' => 'nullable|numeric',
            'advances' => 'nullable|numeric',
            'sheet_date' => 'required|date_format:Y-m',
        ]);

        $shortage = (float) ($validated['shortage'] ?? 0);
        $creditNote = (float) ($validated['credit_note'] ?? 0);
        $advances = (float) ($validated['advances'] ?? 0);

        $debtSheet->update([
            'star_id' => trim($validated['star_id']),
            'shortage' => $shortage,
            'credit_note' => $creditNote,
            'advances' => $advances,
            'status' => $this->resolveDebtStatus($shortage, $creditNote, $advances),
            'sheet_date' => $validated['sheet_date'] . '-01',
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
            'month' => 'required|date_format:Y-m',
            'status' => 'nullable|in:سدد,لم يسدد',
        ]);

        $import = new DebtSheetImport($request->month, $request->status ?? 'لم يسدد');
        $import->import($request->file('file'));

        $message = 'تم استيراد ملف المديونيات الجديد بنجاح.';
        $message .= " تم تحديث/إضافة {$import->getImportedCount()} صف.";

        return redirect()
            ->route('debts.index2')
            ->with('success', $message)
            ->with('import_failures', $import->getFailures());
    }

    private function resolveDebtStatus(float $shortage, float $creditNote, float $advances): string
    {
        return ($shortage + $creditNote + $advances) <= 0 ? 'سدد' : 'لم يسدد';
    }
}
