<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\SafeTransaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function revenue(Request $request)
    {
        $this->authorize('view_revenue_reports');
        $from = $request->get('from');
        $to = $request->get('to');
        $safeName = $request->get('safe');
        $type = $request->get('type'); // 'deposit' | 'withdrawal'
        $expenseTypeId = $request->get('expense_type_id');

        $transactions = SafeTransaction::with(['safe','user','expense.type'])
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->when($type, function($q) use ($type){
                if ($type === 'إيداع' || $type === 'deposit') {
                    $q->where('type', 'deposit');
                } elseif ($type === 'سحب' || $type === 'withdrawal') {
                    $q->where('type', 'withdrawal');
                }
            })
            ->when($safeName, function($q) use ($safeName){
                $q->whereHas('safe', function($qq) use ($safeName){
                    $qq->where('name', $safeName);
                });
            })
            ->when($expenseTypeId, function($q) use ($expenseTypeId){
                $q->whereHas('expense.type', function($qq) use ($expenseTypeId){
                    $qq->where('id', $expenseTypeId);
                });
            })
            ->orderByDesc('id')
            ->paginate(50);

        $totalDeposits = SafeTransaction::when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = SafeTransaction::when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->where('type', 'withdrawal')->sum('amount');

        return view('reports.revenue', compact('transactions', 'from', 'to', 'totalDeposits', 'totalWithdrawals'));
    }
}


