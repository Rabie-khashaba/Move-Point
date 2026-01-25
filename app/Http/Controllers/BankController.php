<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Bank::query();

        // فلترة بالاسم
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $banks = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('banks.index', compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('banks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:banks,name',
            ]);

            Bank::create([
                'name' => $request->name,
            ]);

            return redirect()->route('banks.index')->with('success', 'تم إنشاء البنك بنجاح');
        } catch (\Exception $e) {
            \Log::error('Error creating bank: ' . $e->getMessage());
            return redirect()->route('banks.index')->with('error', 'حدث خطأ أثناء إنشاء البنك. يرجى المحاولة مرة أخرى.');
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
        $bank = Bank::find($id);

        if (!$bank) {
            return redirect()->route('banks.index')->with('error', 'البنك غير موجود');
        }

        return view('banks.edit', compact('bank'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $bank = Bank::find($id);

            if (!$bank) {
                return redirect()->route('banks.index')->with('error', 'البنك غير موجود');
            }

            $request->validate([
                'name' => 'required|string|max:255|unique:banks,name,' . $id,
            ]);

            $bank->update([
                'name' => $request->name,
            ]);

            return redirect()->route('banks.index')->with('success', 'تم تحديث البنك بنجاح');
        } catch (\Exception $e) {
            \Log::error('Error updating bank: ' . $e->getMessage());
            return redirect()->route('banks.index')->with('error', 'حدث خطأ أثناء تحديث البنك. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $bank = Bank::find($id);

            if (!$bank) {
                return redirect()->route('banks.index')->with('error', 'البنك غير موجود');
            }

            $bank->delete();

            return redirect()->route('banks.index')->with('success', 'تم حذف البنك بنجاح');
        } catch (\Exception $e) {
            \Log::error('Error deleting bank: ' . $e->getMessage());
            return redirect()->route('banks.index')->with('error', 'حدث خطأ أثناء حذف البنك. يرجى المحاولة مرة أخرى.');
        }
    }
}
