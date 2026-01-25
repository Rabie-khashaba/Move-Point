<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Support;
use App\Models\SupportReply;
use Illuminate\Http\Request;

class SupportController extends Controller
{


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'issue' => 'required|string',
            'date' => 'nullable|date',
        ]);

        // نبحث عن آخر بلاغ مفتوح لنفس الرقم
        $support = Support::where('phone', $data['phone'])
            ->where('status', '!=', 'closed')
            ->latest()
            ->first();

        if ($support) {
            // لو فيه بلاغ مفتوح، نضيف رسالة جديدة
            SupportReply::create([
                'support_id' => $support->id,
                'user_id' => null, // لو الرسالة من الشخص وليس من الادمن
                'message' => $data['issue'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الرسالة للبلاغ المفتوح ✅',
                'data' => $support->load('replies.user')
            ]);
        }

                // لو مفيش بلاغ مفتوح، ننشئ بلاغ جديد
        $support = Support::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'date' => $data['date'] ?? now(),
            'issue' => $data['issue'],
            'status' => 'open',
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء البلاغ بنجاح ✅',
            'data' => $support
        ], 201);
    }

    public function show($id)
    {
        $support = \App\Models\Support::with(['replies.user:id,name'])->find($id);

        if (!$support) {
            return response()->json([
                'success' => false,
                'message' => 'البلاغ غير موجود ❌'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $support->id,
                'name' => $support->name,
                'phone' => $support->phone,
                'issue' => $support->issue,
                'status' => $support->status,
                'date' => $support->date,
                'replies' => $support->replies->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'message' => $reply->message,
                        'sender' => $reply->user->name ?? 'الإدارة',
                        'created_at' => $reply->created_at->format('Y-m-d H:i'),
                    ];
                }),
            ]
        ]);
    }



    // Function لإضافة رد على بلاغ موجود
    public function reply(Request $request, $supportId)
    {
        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        // نبحث عن البلاغ المطلوب
        $support = Support::find($supportId);

        if (!$support) {
            return response()->json([
                'success' => false,
                'message' => 'البلاغ غير موجود ❌'
            ], 404);
        }

        // 📍 لو البلاغ مغلق:
        if ($support->status === 'closed') {

            // ✅ نتحقق هل يوجد بلاغ مفتوح حالي لنفس الرقم
            $openSupport = Support::where('phone', $support->phone)
                ->where('status', '!=', 'closed')
                ->latest()
                ->first();

            if ($openSupport) {
                // لو لاقينا واحد مفتوح → نضيف الرد عليه بدل ما نعمل واحد جديد
                SupportReply::create([
                    'support_id' => $openSupport->id,
                    'user_id' => null,
                    'message' => $data['message'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'تم إضافة الرسالة للبلاغ المفتوح الحالي ✅',
                    'data' => $openSupport->load('replies.user')
                ]);
            }

            // ❌ مفيش بلاغ مفتوح → نعمل بلاغ جديد واحد فقط
            $newSupport = Support::create([
                'name' => $support->name,
                'phone' => $support->phone,
                'date' => now(),
                'issue' => $data['message'], // الرسالة تصبح Issue جديدة
                'status' => 'open',
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء بلاغ جديد لأن البلاغ السابق كان مغلق ✅',
                'data' => $newSupport
            ], 201);
        }

        // ✅ البلاغ مازال مفتوح → أضف الرد بشكل طبيعي
        SupportReply::create([
            'support_id' => $support->id,
            'user_id' => null,
            'message' => $data['message'],
        ]);

        $support->update([
            'status' => 'replied',
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الرسالة ✅',
            'data' => $support->load('replies.user')
        ]);
    }



    public function close($supportId)
    {
        $support = Support::find($supportId);

        if (!$support) {
            return response()->json([
                'success' => false,
                'message' => 'البلاغ غير موجود ❌'
            ], 404);
        }

        if ($support->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'البلاغ مغلق بالفعل ✅'
            ], 200);
        }

        $support->update(['status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'تم إغلاق البلاغ بنجاح ✅'
        ]);
    }




}
