<?php

namespace App\Http\Controllers;

use App\Models\Safe;
use App\Models\SafeTransaction;
use Illuminate\Http\Request;

class SafeController extends Controller
{
    public function index()
    {
        $this->authorize('view_safes');
        $safes = Safe::withCount('transactions')->orderBy('name')->paginate(20);
        return view('safes.index', compact('safes'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage_safes');
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:safes,name',
            'balance' => 'nullable|numeric|min:0',
        ]);
        $safe = Safe::create([
            'name' => $data['name'],
            'balance' => $data['balance'] ?? 0,
        ]);
        if (($data['balance'] ?? 0) > 0) {
            SafeTransaction::create([
                'safe_id' => $safe->id,
                'type' => 'deposit',
                'amount' => $data['balance'],
                'notes' => 'Opening balance',
            ]);
        }
        return back()->with('success', 'تم إنشاء الخزنة');
    }

    public function deposit(Request $request, Safe $safe)
    {
        $this->authorize('manage_safes');
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);
        $safe->increment('balance', $data['amount']);
        SafeTransaction::create([
            'safe_id' => $safe->id,
            'user_id' => auth()->id(),
            'type' => 'deposit',
            'amount' => $data['amount'],
            'notes' => $data['notes'] ?? null,
        ]);
        return back()->with('success', 'تم الإيداع بنجاح');
    }

    public function withdraw(Request $request, Safe $safe)
    {
        $this->authorize('manage_safes');
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);
        if ($safe->balance < $data['amount']) {
            return back()->with('error', 'الرصيد غير كافٍ');
        }
        $safe->decrement('balance', $data['amount']);
        SafeTransaction::create([
            'safe_id' => $safe->id,
            'user_id' => auth()->id(),
            'type' => 'withdrawal',
            'amount' => $data['amount'],
            'notes' => $data['notes'] ?? null,
        ]);
        return back()->with('success', 'تم السحب بنجاح');
    }
}


