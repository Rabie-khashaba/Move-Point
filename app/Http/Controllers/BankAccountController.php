<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Bank;
use App\Models\Representative;
use App\Models\Governorate;
use App\Models\Location;
use App\Exports\BankAccountsExport;
use App\Imports\BankAccountImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BankAccount::with(['representative.governorate', 'representative.location', 'bank']);

        // فلترة بالبنك (Multiselect)
        if ($request->filled('bank_ids') && is_array($request->bank_ids) && count($request->bank_ids) > 0) {
            $query->whereIn('bank_id', $request->bank_ids);
        }

        // فلترة بالمحافظة
        if ($request->filled('governorate_id')) {
            $query->whereHas('representative', function($q) use ($request) {
                $q->where('governorate_id', $request->governorate_id);
            });
        }

        // فلترة بالمنطقة
        if ($request->filled('location_id')) {
            $query->whereHas('representative', function($q) use ($request) {
                $q->where('location_id', $request->location_id);
            });
        }

        // فلترة بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة بحالة الكود (يمتلك كود / لا يمتلك كود)
        if ($request->filled('code_status')) {
            $codeStatus = $request->code_status;
            $query->whereHas('representative', function ($q) use ($codeStatus) {
                if ($codeStatus === 'with') {
                    $q->whereNotNull('code')->where('code', '!=', '');
                } elseif ($codeStatus === 'without') {
                    $q->where(function ($x) {
                        $x->whereNull('code')->orWhere('code', '');
                    });
                }
            });
        }

        // البحث (الاسم / رقم الهاتف / رقم البطاقة / الكود / رقم الحساب)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('representative', function($repQuery) use ($search) {
                    $repQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('national_id', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%');
                })
                ->orWhere('account_number', 'like', '%' . $search . '%');
            });
        }

        // Statistics (based on current filters)
        $statsQuery = clone $query;
        $totalBankAccounts = (clone $statsQuery)->count();
        $withAccountCount = (clone $statsQuery)->where('status', 'يمتلك حساب')->count();
        $withoutAccountCount = (clone $statsQuery)->where('status', 'لا يمتلك حساب')->count();
        $withoutCodeCount = (clone $statsQuery)->whereHas('representative', function ($q) {
            $q->whereNull('code')->orWhere('code', '');
        })->count();

        $bankAccounts = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // البيانات للفلاتر
        $banks = Bank::all();
        $governorates = Governorate::all();
        $locations = Location::all();

        return view('bank_accounts.index', compact(
            'bankAccounts',
            'banks',
            'governorates',
            'locations',
            'totalBankAccounts',
            'withAccountCount',
            'withoutAccountCount',
            'withoutCodeCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $representatives = Representative::with(['governorate', 'location'])->get();
        $banks = Bank::all();

        return view('bank_accounts.create', compact('representatives', 'banks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'representative_id' => 'required|exists:representatives,id',
                'bank_id' => 'required|exists:banks,id',
                'status' => 'required|in:يمتلك حساب,لا يمتلك حساب',
                'account_owner_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:255',
            ]);

            BankAccount::create([
                'representative_id' => $request->representative_id,
                'bank_id' => $request->bank_id,
                'status' => $request->status,
                'account_owner_name' => $request->account_owner_name,
                'account_number' => $request->account_number,
            ]);

            return redirect()->route('bank-accounts.index')->with('success', 'تم إنشاء الحساب البنكي بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating bank account: ' . $e->getMessage());
            return redirect()->route('bank-accounts.index')->with('error', 'حدث خطأ أثناء إنشاء الحساب البنكي. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $bankAccount = BankAccount::with(['representative', 'bank'])->find($id);

        if (!$bankAccount) {
            return redirect()->route('bank-accounts.index')->with('error', 'الحساب البنكي غير موجود');
        }

        $representatives = Representative::with(['governorate', 'location'])->get();
        $banks = Bank::all();

        return view('bank_accounts.edit', compact('bankAccount', 'representatives', 'banks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $bankAccount = BankAccount::find($id);

            if (!$bankAccount) {
                return redirect()->route('bank-accounts.index')->with('error', 'الحساب البنكي غير موجود');
            }

            $request->validate([
                'representative_id' => 'required|exists:representatives,id',
                'bank_id' => 'required|exists:banks,id',
                'status' => 'required|in:يمتلك حساب,لا يمتلك حساب',
                'account_owner_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:255',
            ]);

            $bankAccount->update([
                'representative_id' => $request->representative_id,
                'bank_id' => $request->bank_id,
                'status' => $request->status,
                'account_owner_name' => $request->account_owner_name,
                'account_number' => $request->account_number,
            ]);

            return redirect()->route('bank-accounts.index')->with('success', 'تم تحديث الحساب البنكي بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating bank account: ' . $e->getMessage());
            return redirect()->route('bank-accounts.index')->with('error', 'حدث خطأ أثناء تحديث الحساب البنكي. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $bankAccount = BankAccount::find($id);

            if (!$bankAccount) {
                return redirect()->route('bank-accounts.index')->with('error', 'الحساب البنكي غير موجود');
            }

            $bankAccount->delete();

            return redirect()->route('bank-accounts.index')->with('success', 'تم حذف الحساب البنكي بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting bank account: ' . $e->getMessage());
            return redirect()->route('bank-accounts.index')->with('error', 'حدث خطأ أثناء حذف الحساب البنكي. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Export bank accounts with applied filters to XLSX.
     */
    public function export(Request $request)
    {
        $fileName = 'bank_accounts_' . now()->format('Y_m_d_His') . '.xlsx';
        return Excel::download(new BankAccountsExport($request->all()), $fileName);
    }

    /**
     * Import bank accounts from XLSX/CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new BankAccountImport();
        $import->import($request->file('file'));

        $imported = $import->getImportedCount();
        $skipped = $import->getSkippedCount();
        $failures = $import->getFailures();

        return redirect()
            ->route('bank-accounts.index')
            ->with('success', "تم الاستيراد بنجاح.")
            ->with('import_failures', $failures);
    }
}
