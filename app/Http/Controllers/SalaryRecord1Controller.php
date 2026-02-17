<?php

namespace App\Http\Controllers;

use App\Imports\SalaryImport;
use App\Models\Debt;
use App\Models\Representative;
use App\Models\salary_records1;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class SalaryRecord1Controller extends Controller
{
    public function index(Request $request)
    {
        $allowedPerPage = [25, 50, 100];
        $perPage = (int) $request->input('per_page', 50);
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 50;
        }

        $query = salary_records1::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qr) use ($q) {
                $qr->where('name', 'like', "%{$q}%")
                    ->orWhere('star_id', 'like', "%{$q}%")
                    ->orWhere('zone', 'like', "%{$q}%");
            });
        }

        if ($month = request('month')) {
            $query->whereMonth('salary_date', date('m', strtotime($month)))
                ->whereYear('salary_date', date('Y', strtotime($month)));
        }

        $records = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return view('salary-records1.index', compact('records'));
    }

    public function showImportForm()
    {
        return view('salary-records1.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'mode' => 'required|in:append,replace',
            'month' => 'required|date_format:Y-m',
        ]);

        $file = $request->file('file');
        $mode = $request->mode;
        $month = $request->month;

        try {
            if ($mode === 'replace') {
                DB::transaction(function () use ($file, $month) {
                    DB::table('salary_records1s')->truncate();
                    Excel::import(new SalaryImport($month), $file);
                });

                $this->syncDebtsFromSalary($month);
                return redirect()->route('salary-record1.index')->with('success', 'تم استبدال البيانات واستيراد الملف بنجاح.');
            }

            Excel::import(new SalaryImport($month), $file);
            $this->syncDebtsFromSalary($month);
            return redirect()->route('salary-record1.index')->with('success', 'تم إضافة بيانات الملف إلى قاعدة البيانات.');
        } catch (Throwable $e) {
            $message = (string) $e->getMessage();
            if (!preg_match('/^\[[A-Z0-9\-]+\]/', $message)) {
                $message = '[SAL-IMP-000] ' . $message;
            }

            return back()->withInput()->with('error', $message);
        }
    }

    public function importFromServer(Request $request)
    {
        $request->validate(['mode' => 'required|in:append,replace']);
        $mode = $request->mode;
        $path = '/mnt/data/قبض مناديب نهائي 7-10 شهر 9-2025 (2).xlsx';

        if (!file_exists($path)) {
            return back()->with('error', 'الملف غير موجود في /mnt/data');
        }

        try {
            if ($mode === 'replace') {
                DB::transaction(function () use ($path) {
                    DB::table('salary_records1s')->truncate();
                    Excel::import(new SalaryImport, $path);
                });

                return redirect()->route('salary-record1.index')->with('success', 'تم استبدال البيانات واستيراد الملف من السيرفر.');
            }

            Excel::import(new SalaryImport, $path);
            return redirect()->route('salary-record1.index')->with('success', 'تم إضافة بيانات الملف من السيرفر.');
        } catch (Throwable $e) {
            $message = (string) $e->getMessage();
            if (!preg_match('/^\[[A-Z0-9\-]+\]/', $message)) {
                $message = '[SAL-IMP-000] ' . $message;
            }

            return back()->withInput()->with('error', $message);
        }
    }

    public function destroy(salary_records1 $record)
    {
        $record->delete();
        return back()->with('success', 'تم حذف السجل');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:salary_records1s,id',
        ]);

        $count = salary_records1::whereIn('id', $request->ids)->delete();

        return back()->with('success', "تم حذف {$count} سجل بنجاح");
    }

    private function syncDebtsFromSalary(string $month): void
    {
        $repCodes = Representative::whereNotNull('code')->pluck('id', 'code');
        $supCodes = Supervisor::whereNotNull('code')->pluck('id', 'code');

        salary_records1::query()
            ->select(['id', 'star_id', 'amounts_on_pilots', 'salary_date'])
            ->whereMonth('salary_date', date('m', strtotime($month)))
            ->whereYear('salary_date', date('Y', strtotime($month)))
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($repCodes, $supCodes) {
                foreach ($rows as $row) {
                    $code = trim((string) $row->star_id);
                    if ($code === '') {
                        continue;
                    }

                    $amount = $row->amounts_on_pilots;
                    if (!is_numeric($amount) || $amount <= 0) {
                        continue;
                    }

                    if (isset($repCodes[$code])) {
                        Debt::create([
                            'representative_id' => $repCodes[$code],
                            'loan_amount' => $amount,
                            'employee_id' => null,
                            'supervisor_id' => null,
                        ]);
                        continue;
                    }

                    if (isset($supCodes[$code])) {
                        Debt::create([
                            'supervisor_id' => $supCodes[$code],
                            'loan_amount' => $amount,
                            'employee_id' => null,
                            'representative_id' => null,
                        ]);
                    }
                }
            });
    }
}
