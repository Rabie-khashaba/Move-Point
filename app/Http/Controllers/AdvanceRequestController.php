<?php

namespace App\Http\Controllers;

use App\Models\AdvanceRequest;
use App\Models\Representative;
use App\Models\SalaryRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\FirebaseNotificationService;
use App\Services\NotificationService;
use Illuminate\Support\Str;

use App\Exports\AdvanceRequestsExport;
use App\Models\Debt;
use Maatwebsite\Excel\Facades\Excel;

class AdvanceRequestController extends Controller
{
    protected $firebaseService;
    protected $notificationService;

    public function __construct(FirebaseNotificationService $firebaseService, NotificationService $notificationService)
    {
        $this->firebaseService = $firebaseService;
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $this->authorize('view_advance_requests');

        $advances = AdvanceRequest::with(['representative.governorate', 'approver'])
            ->when(request('search'), function ($query, $search) {
                $query->whereHas('representative', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('governorate_id'), function ($query, $governorateId) {
                $query->whereHas('representative', function ($q) use ($governorateId) {
                    $q->where('governorate_id', $governorateId);
                });
            })->when(request('role'), function ($query, $role) {
                switch ($role) {
                    case 'representative':
                        $query->whereNotNull('representative_id');
                        break;
                    case 'employee':
                        $query->whereNotNull('employee_id');
                        break;
                    case 'supervisor':
                        $query->whereNotNull('supervisor_id');
                        break;
                }
            })
            ->latest()
            ->paginate(20);

        $governorates = \App\Models\Governorate::all();
        return view('advance-requests.index', compact('advances', 'governorates'));
    }

    public function create()
    {
        $this->authorize('create_advance_requests');

        $representatives = \App\Models\Representative::where('is_active', true)->get();
        $employees = \App\Models\Employee::active()->get();
        $supervisors = \App\Models\Supervisor::where('is_active', true)->get();

        return view('advance-requests.create', compact('representatives', 'employees', 'supervisors'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_advance_requests');

        $validated = $request->validate([
            'requester_type' => 'required|in:employee,representative,supervisor',
            'employee_id' => 'nullable|exists:employees,id',
            'representative_id' => 'nullable|exists:representatives,id',
            'supervisor_id' => 'nullable|exists:supervisors,id',
            'amount' => 'required|numeric|min:0',
            'installment_months' => 'nullable|integer|min:1|max:12',
            'reason' => 'nullable|string|max:500',
        ]);

        // Ensure only one ID is provided based on requester type
        $requesterId = null;
        switch ($validated['requester_type']) {
            case 'employee':
                if (!$validated['employee_id']) {
                    return back()->withErrors(['employee_id' => 'يجب اختيار الموظف']);
                }
                $requesterId = $validated['employee_id'];
                break;
            case 'representative':
                if (!$validated['representative_id']) {
                    return back()->withErrors(['representative_id' => 'يجب اختيار المندوب']);
                }
                $requesterId = $validated['representative_id'];
                break;
            case 'supervisor':
                if (!$validated['supervisor_id']) {
                    return back()->withErrors(['supervisor_id' => 'يجب اختيار المشرف']);
                }
                $requesterId = $validated['supervisor_id'];
                break;
        }

        // Auto-approve if created from dashboard (employee)
        $status = $validated['requester_type'] === 'employee' ? 'approved' : 'pending';
        $approvedBy = $validated['requester_type'] === 'employee' ? Auth::id() : null;
        $approvedAt = $validated['requester_type'] === 'employee' ? now() : null;
        $monthlyInstallment = $validated['amount'] / $validated['installment_months'];

        $advanceRequest = AdvanceRequest::create([
            'employee_id' => $validated['requester_type'] === 'employee' ? $requesterId : null,
            'representative_id' => $validated['requester_type'] === 'representative' ? $requesterId : null,
            'supervisor_id' => $validated['requester_type'] === 'supervisor' ? $requesterId : null,
            'amount' => $validated['amount'],
            'installment_months' => $validated['installment_months'],
            'monthly_installment' => $monthlyInstallment,
            'reason' => $validated['reason'],
            'status' => $status,
            'approved_by' => $approvedBy,
            'approved_at' => $approvedAt,
        ]);
        if ($status === 'approved') {
            $this->distributeInstallments($advanceRequest);
        }

        // Create notification for admins and supervisors
        try {
            $this->notificationService->notifyAdvanceRequest($advanceRequest, 'created');
        } catch (\Exception $e) {
            \Log::error('Failed to create advance request notification: ' . $e->getMessage());
        }

        $message = $status === 'approved'
            ? 'تم إنشاء طلب السلفة والموافقة عليه بنجاح!'
            : 'تم إنشاء طلب السلفة بنجاح! سيتم مراجعته قريباً.';


        return redirect()->route('advance-requests.index')
            ->with('success', $message);
    }

    public function edit(AdvanceRequest $advanceRequest)
    {

        // جلب كل المندوبين، الموظفين، والمشرفين
        $representatives = \App\Models\Representative::all();
        $employees = \App\Models\Employee::active()->get();
        $supervisors = \App\Models\Supervisor::all();

        return view('advance-requests.edit', [
            'advanceRequest' => $advanceRequest,
            'representatives' => $representatives,
            'employees' => $employees,
            'supervisors' => $supervisors,
        ]);
    }



    public function update(Request $request, AdvanceRequest $advanceRequest)
    {
        $this->authorize('update_advance_requests');

        $validated = $request->validate([
            'requester_type' => 'required|in:employee,representative,supervisor',
            'employee_id' => 'nullable|exists:employees,id',
            'representative_id' => 'nullable|exists:representatives,id',
            'supervisor_id' => 'nullable|exists:supervisors,id',
            'amount' => 'required|numeric|min:0',
            'installment_months' => 'nullable|integer|min:1|max:12',
            'reason' => 'nullable|string|max:500',
        ]);

        // تحديد الـ requester_id حسب النوع
        $requesterId = null;
        switch ($validated['requester_type']) {
            case 'employee':
                if (!$validated['employee_id']) {
                    return back()->withErrors(['employee_id' => 'يجب اختيار الموظف']);
                }
                $requesterId = $validated['employee_id'];
                break;
            case 'representative':
                if (!$validated['representative_id']) {
                    return back()->withErrors(['representative_id' => 'يجب اختيار المندوب']);
                }
                $requesterId = $validated['representative_id'];
                break;
            case 'supervisor':
                if (!$validated['supervisor_id']) {
                    return back()->withErrors(['supervisor_id' => 'يجب اختيار المشرف']);
                }
                $requesterId = $validated['supervisor_id'];
                break;
        }

        // إعادة حساب القسط الشهري
        $monthlyInstallment = $validated['installment_months']
            ? $validated['amount'] / $validated['installment_months']
            : $validated['amount'];

        // تحديث البيانات
        $advanceRequest->update([
            'employee_id' => $validated['requester_type'] === 'employee' ? $requesterId : null,
            'representative_id' => $validated['requester_type'] === 'representative' ? $requesterId : null,
            'supervisor_id' => $validated['requester_type'] === 'supervisor' ? $requesterId : null,
            'amount' => $validated['amount'],
            'installment_months' => $validated['installment_months'],
            'monthly_installment' => $monthlyInstallment,
            'reason' => $validated['reason'],
        ]);

        // إعادة توزيع الأقساط إذا كان الطلب موافق عليه مسبقاً
        if ($advanceRequest->status === 'approved') {
            $this->distributeInstallments($advanceRequest);
        }

        // إشعار المشرفين/الأدمن إذا لزم الأمر
        try {
            $this->notificationService->notifyAdvanceRequest($advanceRequest, 'updated');
        } catch (\Exception $e) {
            \Log::error('Failed to create advance request update notification: ' . $e->getMessage());
        }

        return redirect()->route('advance-requests.index')
            ->with('success', 'تم تحديث طلب السلفة بنجاح!');
    }


    public function show($id)
    {
        $this->authorize('view_advance_requests');

        $advance = AdvanceRequest::with(['representative.governorate', 'approver'])->findOrFail($id);
        return view('advance-requests.show', compact('advance'));
    }

    // public function approve($id)
    // {
    //     $this->authorize('approve_advance_requests');

    //     $advance = AdvanceRequest::findOrFail($id);

    //     if ($advance->status !== 'pending') {
    //         return back()->with('error', 'لا يمكن الموافقة على طلب تمت معالجته مسبقاً');
    //     }

    //     // Check advance request conditions
    //     $validationResult = $this->validateAdvanceRequest($advance);
    //     if (!$validationResult['valid']) {
    //         return back()->with('error', $validationResult['message']);
    //     }

    //     $advance->update([
    //         'status' => 'approved',
    //         'approved_by' => Auth::id(),
    //         'approved_at' => now()
    //     ]);

    //     // توزيع الأقساط بعد الموافقة
    //     $this->distributeInstallments($advance);

    //     // Send notification to the user
    //     $user = $advance->representative?->user ?? $advance->employee?->user ?? $advance->supervisor?->user;
    //     if ($user) {
    //         $this->firebaseService->sendAdvanceRequestApprovalNotification($user, [
    //             'id' => $advance->id,
    //             'amount' => $advance->amount,
    //         ]);
    //     }


    //     //المديونات
    //     $searchColumn = null;

    //     if ($advance->representative_id) {
    //         $searchColumn = 'representative_id';
    //     } elseif ($advance->employee_id) {
    //         $searchColumn = 'employee_id';
    //     } elseif ($advance->supervisor_id) {
    //         $searchColumn = 'supervisor_id';
    //     }

    //     Debt::updateOrCreate(
    //         [$searchColumn => $advance->$searchColumn],
    //         [
    //             'loan_amount' => $advance->amount,
    //             'representative_id' => $advance->representative_id,
    //             'employee_id' => $advance->employee_id,
    //             'supervisor_id' => $advance->supervisor_id,
    //         ]
    //     );


    //     // Note: Removed notifications to all admins/supervisors - only notify the requester

    //     // Create notification for admins and supervisors
    //     try {
    //         $this->notificationService->notifyAdvanceRequest($advance, 'approved');
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to create advance request approval notification: ' . $e->getMessage());
    //     }

    //     return redirect()->route('advance-requests.index')
    //         ->with('success', 'تم الموافقة على طلب السلفة بنجاح!');
    // }



    public function approve(Request $request, $id)
    {
        $this->authorize('approve_advance_requests');

        // ✅ Validation (amount اختياري)
        $request->validate([
            'amount' => 'nullable|numeric|min:1',
        ]);

        $advance = AdvanceRequest::findOrFail($id);

        if ($advance->status !== 'pending') {
            return back()->with('error', 'لا يمكن الموافقة على طلب تمت معالجته مسبقاً');
        }

        // ✅ التحقق من شروط السلفة
        $validationResult = $this->validateAdvanceRequest($advance);
        if (!$validationResult['valid']) {
            return back()->with('error', $validationResult['message']);
        }

        // ✅ تجهيز بيانات التحديث
        $updateData = [
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ];

        // ✅ تحديث المبلغ فقط لو موجود
        if ($request->filled('amount')) {
            $updateData['amount'] = $request->amount;
        }

        // ✅ تنفيذ التحديث
        $advance->update($updateData);

        // ✅ توزيع الأقساط بعد الموافقة
        $this->distributeInstallments($advance);

        // ✅ إرسال إشعار لصاحب الطلب
        $user = $advance->representative?->user
            ?? $advance->employee?->user
            ?? $advance->supervisor?->user;

        if ($user) {
            $this->firebaseService->sendAdvanceRequestApprovalNotification($user, [
                'id' => $advance->id,
                'amount' => $advance->amount,
            ]);
        }

        // ✅ تحديث / إنشاء المديونية
        $searchColumn = null;

        if ($advance->representative_id) {
            $searchColumn = 'representative_id';
        } elseif ($advance->employee_id) {
            $searchColumn = 'employee_id';
        } elseif ($advance->supervisor_id) {
            $searchColumn = 'supervisor_id';
        }

        Debt::updateOrCreate(
            [$searchColumn => $advance->$searchColumn],
            [
                'loan_amount' => $advance->amount,
                'representative_id' => $advance->representative_id,
                'employee_id' => $advance->employee_id,
                'supervisor_id' => $advance->supervisor_id,
            ]
        );

        // ✅ إشعار الإداريين (لو موجود)
        try {
            $this->notificationService->notifyAdvanceRequest($advance, 'approved');
        } catch (\Exception $e) {
            \Log::error('Failed to create advance request approval notification: ' . $e->getMessage());
        }

        return redirect()
            ->route('advance-requests.index')
            ->with('success', 'تم الموافقة على طلب السلفة بنجاح!');
    }


    public function reject(Request $request, $id)
    {
        $this->authorize('approve_advance_requests');

        $advance = AdvanceRequest::findOrFail($id);

        if ($advance->status !== 'pending') {
            return back()->with('error', 'لا يمكن رفض طلب تمت معالجته مسبقاً');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $advance->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        // Send notification to the user
        $user = $advance->representative?->user ?? $advance->employee?->user ?? $advance->supervisor?->user;
        if ($user) {
            $this->firebaseService->sendAdvanceRequestRejectionNotification($user, [
                'id' => $advance->id,
                'amount' => $advance->amount,
            ], $validated['rejection_reason']);
        }

        // Note: Removed notifications to all admins/supervisors - only notify the requester

        // Create notification for admins and supervisors
        try {
            $this->notificationService->notifyAdvanceRequest($advance, 'rejected');
        } catch (\Exception $e) {
            \Log::error('Failed to create advance request rejection notification: ' . $e->getMessage());
        }

        return redirect()->route('advance-requests.index')
            ->with('success', 'تم رفض طلب السلفة بنجاح!');
    }

    public function destroy($id)
    {
        $this->authorize('delete_advance_requests');

        $advance = AdvanceRequest::findOrFail($id);
        $advance->delete();

        return redirect()->route('advance-requests.index')
            ->with('success', 'تم حذف طلب السلفة بنجاح!');
    }

    private function validateAdvanceRequest($advance)
    {
        $representative = $advance->representative;

        // Check if representative has been working for at least one week
        if (empty($representative->start_date)) {
            return [
                'valid' => false,
                'message' => 'هذا الشخص ليس لديه تاريخ بدء العمل'
            ];
        }

        // $oneWeekAgo = Carbon::now()->subWeek();
        // if ($representative->start_date > $oneWeekAgo) {
        //     return [
        //         'valid' => false,
        //         'message' => 'يجب أن يكون المندوب يعمل لمدة أسبوع على الأقل'
        //     ];
        // }


        $oneWeekAgo = Carbon::now()->subWeek();

// لو اليوزر مش Admin طبق الشرط
if (auth()->user()->type !== 'admin') {
    if ($representative->start_date > $oneWeekAgo) {
        return [
            'valid' => false,
            'message' => 'يجب أن يكون المندوب يعمل لمدة أسبوع على الأقل'
        ];
    }
}




        // Check if advance amount is within 80% of salary limit
        // $maxAdvanceAmount = 20000 * 0.8;
        // if ($advance->amount > $maxAdvanceAmount) {
        //     return [
        //         'valid' => false,
        //         'message' => "مبلغ السلفة يتجاوز الحد الأقصى المسموح (80% من المرتب: {$maxAdvanceAmount})"
        //     ];
        // }

        // Check if current date is between 15th and 20th of the month
        $currentDay = Carbon::now()->day;
        if ($currentDay < 1 || $currentDay > 31) {
            return [
                'valid' => false,
                'message' => 'طلبات السلفة متاحة فقط من يوم 15 إلى يوم 20 من الشهر'
            ];
        }

        return ['valid' => true];
    }

    public function calculateInstallment(Request $request)
    {
        $amount = $request->get('amount', 0);
        $months = $request->get('months', 1);

        if ($months > 0) {
            $monthlyInstallment = $amount / $months;
        } else {
            $monthlyInstallment = $amount;
        }

        return response()->json([
            'monthly_installment' => round($monthlyInstallment, 2)
        ]);
    }

    /**
     * Show receipt image for advance request
     */
    public function showReceipt($id)
    {
        $this->authorize('view_advance_requests');

        $advance = AdvanceRequest::findOrFail($id);

        if (!$advance->receipt_image) {
            abort(404, 'لا يوجد إيصال لهذا الطلب');
        }

        // Check if receipt_image is already a full URL
        if (filter_var($advance->receipt_image, FILTER_VALIDATE_URL)) {
            // It's already a full URL, redirect directly
            return redirect($advance->receipt_image);
        }

        // Return a redirect to the proper storage URL
        $storageUrl = asset('storage/app/public/' . $advance->receipt_image);
        return redirect($storageUrl);
    }
    private function distributeInstallments(AdvanceRequest $advance)
    {
        $installmentMonths = $advance->installment_months ?: 1;
        $monthlyInstallment = $advance->monthly_installment ?: $advance->amount;
        $startDate = Carbon::now();

        for ($i = 0; $i < $installmentMonths; $i++) {
            $date = $startDate->copy()->addMonths($i); // Carbon object

            $month = $date->month; // رقم الشهر (1-12)
            $year = $date->year;  // رقم السنة

            // تحديد نوع المستخدم
            $userType = null;
            $userId = null;

            if ($advance->employee_id) {
                $userType = 'employee';
                $userId = $advance->employee_id;
            } elseif ($advance->representative_id) {
                $userType = 'representative';
                $userId = $advance->representative_id;
            } elseif ($advance->supervisor_id) {
                $userType = 'supervisor';
                $userId = $advance->supervisor_id;
            }

            if (!$userType || !$userId) {
                continue; // تخطي إذا لم يتم تحديد نوع المستخدم
            }

            $salary = SalaryRecord::firstOrNew([
                'employee_id' => $advance->employee_id,
                'representative_id' => $advance->representative_id,
                'supervisor_id' => $advance->supervisor_id,
                'user_type' => $userType,
                'month' => $month,
                'year' => $year,
            ]);

            // إذا كان السجل جديد، قم بتعيين القيم الافتراضية
            if (!$salary->exists) {
                // جلب المرتب الأساسي من المستخدم
                $baseSalary = 0;
                switch ($userType) {
                    case 'employee':
                        $employee = \App\Models\Employee::find($userId);
                        $baseSalary = $employee ? $employee->salary : 0;
                        break;
                    case 'representative':
                        $representative = \App\Models\Representative::find($userId);
                        $baseSalary = $representative ? $representative->salary : 0;
                        break;
                    case 'supervisor':
                        $supervisor = \App\Models\Supervisor::find($userId);
                        $baseSalary = $supervisor ? $supervisor->salary : 0;
                        break;
                }

                $salary->fill([
                    'base_salary' => $baseSalary,
                    'advances' => 0,
                    'deductions' => 0,
                    'lost_orders_penalty' => 0,
                    'delivery_penalty' => 0,
                    'commissions' => 0,
                    'cashback' => 0,
                    'net_salary' => $baseSalary,
                    'is_paid' => false
                ]);
            }

            // إضافة السلفة للأقساط
            $salary->advances = ($salary->advances ?? 0) + $monthlyInstallment;

            // إعادة حساب صافي المرتب
            $totalDeductions = $salary->advances + $salary->deductions +
                $salary->lost_orders_penalty + $salary->delivery_penalty;
            $totalAdditions = $salary->commissions + $salary->cashback;
            $salary->net_salary = max(0, $salary->base_salary - $totalDeductions + $totalAdditions);

            $salary->save();
        }
    }



    public function export(Request $request)
    {
        // return $request;
        $query = AdvanceRequest::with(['representative.governorate', 'representative.location', 'employee', 'supervisor']);

        // 🔍 البحث بالاسم
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('requester_name', 'like', "%{$search}%")
                    ->orWhereHas('representative', fn($r) => $r->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('employee', fn($e) => $e->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('supervisor', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        // ⚙️ فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 📅 فلترة بالتاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 👥 فلترة بنوع الموظف (مندوب / موظف / مشرف)
        if ($request->filled('role')) {
            $role = $request->role;
            $query->whereNotNull("{$role}_id");
        }

        $advances = $query->get();

        // اسم الملف
        $filename = "advance_requests_" . now()->format('Y_m_d_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        // 📤 التصدير
        $callback = function () use ($advances) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // دعم العربي

            // رؤوس الأعمدة
            fputcsv($file, [
                'م',
                'مقدم الطلب',
                'نوع الموظف',
                'المبلغ',
                'التقسيط',
                'عدد الأشهر',
                'القسط الشهري',
                'المحافظة',
                'المنطقة',
                'الحالة',
                'تاريخ الطلب'
            ]);

            foreach ($advances as $index => $advance) {
                // تحديد نوع الموظف
                $role = $advance->representative ? 'مندوب' :
                    ($advance->employee ? 'موظف' :
                        ($advance->supervisor ? 'مشرف' : 'غير محدد'));

                fputcsv($file, [
                    $index + 1,
                    $advance->requester_name,
                    $role,
                    number_format($advance->amount, 2),
                    $advance->is_installment ? 'نعم' : 'لا',
                    $advance->is_installment ? $advance->installment_months : '-',
                    $advance->is_installment ? number_format($advance->monthly_installment, 2) : '-',
                    $advance->representative?->governorate?->name ?? 'غير محدد',
                    $advance->representative?->location?->name ?? 'غير محدد',
                    $advance->status === 'pending' ? 'في الانتظار' : ($advance->status === 'approved' ? 'تمت الموافقة' : 'مرفوض'),
                    $advance->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function exportExcel(Request $request)
    {
        $fileName = 'advance_requests_' . now()->format('Y_m_d_His') . '.xlsx';
        return Excel::download(new AdvanceRequestsExport($request->all()), $fileName);
    }


    public function updateCode(Request $request, $id)
    {

        $advanceRequest = AdvanceRequest::findOrFail($id);

        if (!$advanceRequest->representative_id) {
            return redirect()->back()->with('error', 'هذا الموظف ليس له كود مندوب.');
        }

        $representative = Representative::find($advanceRequest->representative_id);
        $representative->code = $request->code;
        $representative->save();

        return redirect()->back()->with('success', 'تم تعديل كود المندوب بنجاح.');

    }

}
