<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\salary_records1;
use App\Models\Representative;
use App\Models\Supervisor;
use App\Models\Debt;
use App\Imports\SalaryImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class SalaryRecord1Controller extends Controller
{
    public function index(Request $request)
    {
        $query = salary_records1::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function($qr) use ($q) {
                $qr->where('name','like',"%{$q}%")
                    ->orWhere('star_id','like',"%{$q}%")
                    ->orWhere('zone','like',"%{$q}%");
            });
        }

        // فلترة بالشهر
        if ($month = request('month')) {
            $query->whereMonth('salary_date', date('m', strtotime($month)))
                ->whereYear('salary_date', date('Y', strtotime($month)));
        }

        $records = $query->orderBy('id','desc')->paginate(25);

        return view('salary-records1.index', compact('records'));
    }

    public function showImportForm()
    {
        return view('salary-records1.import');
    }

    public function import(Request $request)
    {
       // return $request;
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'mode' => 'required|in:append,replace',
            'month' => 'required|date_format:Y-m'
        ]);

        $file = $request->file('file');
        $mode = $request->mode;
        $month = $request->month;

        if ($mode === 'replace') {
            // استبدال: نحذف البيانات القديمة داخل Transaction
            DB::transaction(function() use ($file, $month) {
                DB::table('salary_records1s')->truncate();
                Excel::import(new SalaryImport($month), $file);
            });
            $this->syncDebtsFromSalary($month);
            return redirect()->route('salary-record1.index')->with('success', 'تم استبدال البيانات واستيراد الملف بنجاح.');
        } else {
            // إضافة
            Excel::import(new SalaryImport($month), $file);
            $this->syncDebtsFromSalary($month);
            return redirect()->route('salary-record1.index')->with('success', 'تم إضافة بيانات الملف إلى قاعدة البيانات.');
        }
    }

    // استيراد مباشر من المسار على السيرفر (اختياري)
    public function importFromServer(Request $request)
    {
        $request->validate(['mode' => 'required|in:append,replace']);
        $mode = $request->mode;
        $path = '/mnt/data/قبض مناديب نهائي 7-10 شهر 9-2025 (2).xlsx';
        if (!file_exists($path)) {
            return back()->with('error', 'الملف غير موجود في /mnt/data');
        }

        if ($mode === 'replace') {
            DB::transaction(function() use ($path) {
                DB::table('salary_records1s')->truncate();
                Excel::import(new SalaryImport, $path);
            });
            return redirect()->route('salary-record1.index')->with('success', 'تم استبدال البيانات واستيراد الملف من السيرفر.');
        } else {
            Excel::import(new SalaryImport, $path);
            return redirect()->route('salary-record1.index')->with('success', 'تم إضافة بيانات الملف من السيرفر.');
        }
    }

    public function destroy(salary_records1 $record)
    {
        $record->delete();
        return back()->with('success','تم حذف السجل');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:salary_records1s,id'
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
