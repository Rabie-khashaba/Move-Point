<?php

namespace App\Http\Controllers;

use App\Models\Support;
use App\Models\SupportReply;
use Illuminate\Http\Request;

class SupportReplyController extends Controller
{
    public function store(Request $request, Support $support)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);


        // منع الرد إذا البلاغ مغلق
    if ($support->status === 'closed') {
        return back()->with('error', 'لا يمكن إضافة رد لأن البلاغ مغلق.');
    }

        SupportReply::create([
            'support_id' => $support->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return back()->with('success', 'تم إرسال الرد بنجاح');
    }
}
