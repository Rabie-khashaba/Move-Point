<?php
namespace App\Http\Controllers;

use App\Models\Reason;
use Illuminate\Http\Request;

class ReasonController extends Controller
{
    public function index()
    {
        // عرض الأسباب مع الترتيب تنازليًا والتقسيم إلى صفحات
        $reasons = Reason::orderBy('id', 'desc')->paginate(10);

        return view('reasons.index', compact('reasons'));
    }

    public function create()
    {
        return view('reasons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ], [
            'name.required' => 'حقل الاسم مطلوب',
            'name.string'   => 'الاسم يجب أن يكون نصاً',
            'name.max'      => 'الاسم يجب ألا يتجاوز 255 حرفاً',
        ]);

        Reason::create($request->all());

        return redirect()->route('reasons.index')
            ->with('success', '✅ تم إضافة السبب بنجاح');
    }

    public function edit(Reason $reason)
    {
        return view('reasons.edit', compact('reason'));
    }

    public function update(Request $request, Reason $reason)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ], [
            'name.required' => 'حقل الاسم مطلوب',
            'name.string'   => 'الاسم يجب أن يكون نصاً',
            'name.max'      => 'الاسم يجب ألا يتجاوز 255 حرفاً',
        ]);

        $reason->update($request->all());

        return redirect()->route('reasons.index')
            ->with('success', '✅ تم تحديث السبب بنجاح');
    }

    public function destroy(Reason $reason)
    {
        $reason->delete();

        return redirect()->route('reasons.index')
            ->with('success', '🗑️ تم حذف السبب بنجاح');
    }
}
