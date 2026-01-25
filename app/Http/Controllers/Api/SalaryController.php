<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Representative;
use App\Models\Supervisor;
use App\Models\SalaryRecord1;   // ✅ خليك على موديل واحد بس
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function getSalaryByCode($code)
    {
        // ✅ 1) دور في المندوبين الأول
        $person = Representative::where('code', $code)->first();
        $role = 'representative';

        // ✅ 2) لو مش موجود.. دور في المشرفين
        if (!$person) {
            $person = Supervisor::where('code', $code)->first();
            $role = 'supervisor';
        }

        // ✅ 3) لو مش موجود في الاتنين
        if (!$person) {
            return response()->json([
                'status' => false,
                'message' => 'الكود غير موجود لا في المندوبين ولا المشرفين',
            ], 404);
        }

        // ✅ 4) هات السالاري من جدول المرتبات
        // لو جدول SalaryRecord1 بيستخدم star_id = code
        $salary = SalaryRecord1::where('star_id', $code)->get();

        if ($salary->isEmpty()) {
            return response()->json([
                'status' => true,
                'role' => $role,
                'name' => $person->name,
                'code' => $code,
                'message' => 'لا توجد سجلات راتب لهذا الكود حالياً',
                'data' => [],
            ]);
        }

        return response()->json([
            'status' => true,
            'role' => $role,
            'name' => $person->name,
            'code' => $code,
            'records_count' => $salary->count(),
            'data' => $salary,
        ]);
    }
}
