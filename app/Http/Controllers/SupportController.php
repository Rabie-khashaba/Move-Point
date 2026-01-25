<?php

namespace App\Http\Controllers;

use App\Models\Support;
use App\Models\SupportReply;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $supports = Support::latest()->paginate(10);
        return view('supports.index', compact('supports'));
    }

    public function create()
    {
        return view('supports.create');
    }

    public function store(Request $request)
    {
                $data = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'date' => 'nullable|date',
                'issue' => 'required|string',
            ]);

            // 🔹 نبحث عن آخر بلاغ مفتوح بنفس الهاتف
            $support = Support::where('phone', $data['phone'])
                ->where('status', '!=', 'closed')
                ->latest()
                ->first();

            if ($support) {
                // لو البلاغ موجود، نضيف رسالة جديدة فقط
                SupportReply::create([
                    'support_id' => $support->id,
                    'user_id' => auth()->id(), // أو null لو الرد من العميل
                    'message' => $data['issue'],
                ]);

                // تحديث حالة البلاغ
                $support->update([
                    'status' => 'open',
                    'is_read' => false,
                ]);
                return redirect()->route('supports.show', $support)->with('success', 'تم إضافة الرسالة للبلاغ الحالي ✅');
            }
                // لو مفيش بلاغ مفتوح، ننشئ بلاغ جديد
                $support = Support::create($data);
                return redirect()->route('supports.index')->with('success', 'تم إضافة البلاغ بنجاح ✅');

    }

    public function show($id)
    {
        $support = Support::with(['replies.user'])->findOrFail($id);

        // ✅ عند فتح البلاغ من الأدمن يتم اعتباره "مقروء"
        if ($support->is_read == false) {
            $support->update(['is_read' => true]);
        }

        return view('supports.show', compact('support'));
    }

    public function reply(Request $request, Support $support)
    {
        $request->validate([
            'reply_message' => 'required|string|max:2000',
        ]);

        // 📍 لو البلاغ مغلق
        if ($support->status === 'closed') {

            // 🔍 نبحث عن بلاغ مفتوح آخر لنفس الهاتف
            $openSupport = Support::where('phone', $support->phone)
                ->where('status', '!=', 'closed')
                ->latest()
                ->first();

            if ($openSupport) {
                // ✅ يوجد بلاغ مفتوح → نضيف الرد عليه
                SupportReply::create([
                    'support_id' => $openSupport->id,
                    'user_id' => auth()->id(),
                    'message' => $request->reply_message,
                ]);

                $openSupport->update([
                    'status' => 'replied',
                    'is_read' => false,
                ]);

                return redirect()
                    ->route('supports.show', $openSupport)
                    ->with('success', 'تم إضافة الرد على البلاغ المفتوح الحالي ✅');
            }

            // ❌ لا يوجد بلاغ مفتوح → ننشئ بلاغ جديد واحد فقط
            $newSupport = Support::create([
                'name' => $support->name,
                'phone' => $support->phone,
                'date' => now(),
                'issue' => $request->reply_message,
                'status' => 'open',
                'is_read' => false,
            ]);

            return redirect()
                ->route('supports.show', $newSupport)
                ->with('success', 'تم إنشاء بلاغ جديد لأن البلاغ السابق كان مغلق ✅');
        }

        // ✅ البلاغ مفتوح → أضف رد عادي
        SupportReply::create([
            'support_id' => $support->id,
            'user_id' => auth()->id(),
            'message' => $request->reply_message,
        ]);

        $support->update([
            'status' => 'replied',
            'is_read' => false,
        ]);

        return redirect()
            ->route('supports.show', $support)
            ->with('success', 'تم إرسال الرد بنجاح ✅');
    }



    public function close(Support $support)
    {
        $support->update(['status' => 'closed']);
        return back()->with('success', 'تم إنهاء البلاغ.');
    }

    public function destroy(Support $support)
    {
        $support->delete();
        return back()->with('success', 'تم حذف البلاغ.');
    }
}
