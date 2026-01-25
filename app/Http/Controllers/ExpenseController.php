<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Safe;
use App\Models\SafeTransaction;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $this->authorize('view_expenses');
        $expenses = Expense::with(['type', 'safe'])->orderByDesc('id')->paginate(20);
        $types = ExpenseType::orderBy('name')->get();
        $safes = Safe::orderBy('name')->get();
        return view('expenses.index', compact('expenses', 'types', 'safes'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_expenses');
        $data = $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'safe_id' => 'required|exists:safes,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:5120',
            'expense_scope' => 'required|in:عام,ثابت',
        ]);

        $safe = Safe::findOrFail($data['safe_id']);
        if ($safe->balance < $data['amount']) {
            return back()->with('error', 'الرصيد في الخزنة غير كافٍ')->withInput();
        }

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments/expenses', 'public');
        }

        $expense = Expense::create([
            'expense_type_id' => $data['expense_type_id'],
            'safe_id' => $data['safe_id'],
            'user_id' => auth()->id(),
            'amount' => $data['amount'],
            'notes' => $data['notes'] ?? null,
            'attachment_path' => $path,
            'expense_scope' => $data['expense_scope'],
        ]);

        // Record withdrawal from safe
        $safe->decrement('balance', $data['amount']);
        SafeTransaction::create([
            'safe_id' => $safe->id,
            'user_id' => auth()->id(),
            'type' => 'withdrawal',
            'amount' => $data['amount'],
            'reference_type' => 'expense',
            'reference_id' => $expense->id,
            'notes' => 'Expense payout',
        ]);

        return redirect()->route('expenses.index')->with('success', 'تم إضافة مصروف');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete_expenses');
        // Optional: refund to safe?
        return back()->with('error', 'الحذف غير مفعل للتسويات المالية');
    }
}


