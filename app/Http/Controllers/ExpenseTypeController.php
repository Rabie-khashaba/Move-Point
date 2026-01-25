<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseTypeController extends Controller
{
    public function index()
    {
        $this->authorize('view_expense_types');
        $types = ExpenseType::orderBy('name')->paginate(20);
        return view('expense_types.index', compact('types'));
    }

    public function create()
    {
        $this->authorize('create_expense_types');
        return view('expense_types.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create_expense_types');
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:expense_types,name',
        ]);
        ExpenseType::create($data);
        return redirect()->route('expense-types.index')->with('success', 'تم إنشاء نوع المصروفات');
    }

    public function edit(ExpenseType $expenseType)
    {
        $this->authorize('edit_expense_types');
        return view('expense_types.edit', ['type' => $expenseType]);
    }

    public function update(Request $request, ExpenseType $expenseType)
    {
        $this->authorize('edit_expense_types');
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:expense_types,name,' . $expenseType->id,
        ]);
        $expenseType->update($data);
        return redirect()->route('expense-types.index')->with('success', 'تم تحديث نوع المصروفات');
    }

    public function destroy(ExpenseType $expenseType)
    {
        $this->authorize('delete_expense_types');
        $expenseType->delete();
        return redirect()->route('expense-types.index')->with('success', 'تم حذف نوع المصروفات');
    }
}


