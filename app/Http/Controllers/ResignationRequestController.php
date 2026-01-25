<?php

namespace App\Http\Controllers;

use App\Models\ResignationRequest;
use App\Models\ResignationRequestNote;
use App\Models\Employee;
use App\Models\Representative;
use App\Models\Debt;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseNotificationService;
use App\Services\NotificationService;
use App\Services\WhatsAppWorkService;
use Illuminate\Support\Facades\DB;



class ResignationRequestController extends Controller
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
        $this->authorize('view_resignation_requests');

        $resignations = ResignationRequest::with(['employee.department', 'employee.company', 'representative.company', 'supervisor.company', 'approver'])
            ->when(request('search'), function($query, $search) {
                $query->whereHas('employee', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('representative', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('supervisor', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('source'), function($query, $source) {
                if ($source === 'app') {
                    $query->whereNull('source');
                } else {
                    $query->where('source', $source);
                }
            })
            ->when(request('company_id'), function($query, $companyId) {
                $query->where(function($q) use ($companyId) {
                    $q->whereHas('employee', function($emp) use ($companyId) {
                        $emp->where('company_id', $companyId);
                    })
                    ->orWhereHas('representative', function($rep) use ($companyId) {
                        $rep->where('company_id', $companyId);
                    })
                    ->orWhereHas('supervisor', function($sup) use ($companyId) {
                        $sup->where('company_id', $companyId);
                    });
                });
            })
            ->when(request('date_from'), function($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when(request('date_to'), function($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })

            ->latest()
            ->paginate(20);

        // إنشاء query base للإحصائيات (نفس الفلاتر)
        $statsQuery = ResignationRequest::query()
            ->when(request('search'), function($query, $search) {
                $query->whereHas('employee', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('representative', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('supervisor', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('source'), function($query, $source) {
                if ($source === 'app') {
                    $query->whereNull('source');
                } else {
                    $query->where('source', $source);
                }
            })
            ->when(request('company_id'), function($query, $companyId) {
                $query->where(function($q) use ($companyId) {
                    $q->whereHas('employee', function($emp) use ($companyId) {
                        $emp->where('company_id', $companyId);
                    })
                    ->orWhereHas('representative', function($rep) use ($companyId) {
                        $rep->where('company_id', $companyId);
                    })
                    ->orWhereHas('supervisor', function($sup) use ($companyId) {
                        $sup->where('company_id', $companyId);
                    });
                });
            })
            ->when(request('date_from'), function($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when(request('date_to'), function($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            });

        // الإحصائيات
        $totalResignations = (clone $statsQuery)->count();
       /*  $pendingResignations = (clone $statsQuery)->where('status', 'pending')->count();
        $approvedResignations = (clone $statsQuery)->where('status', 'approved')->count();
        $rejectedResignations = (clone $statsQuery)->where('status', 'rejected')->count();
        $unresignResignations = (clone $statsQuery)->where('status', 'unresign')->count(); */

        // إحصائيات حسب الشركة
        $boostaResignations = (clone $statsQuery)->where(function($q) {
            $q->whereHas('employee', function($emp) {
                $emp->where('company_id', 10);
            })
            ->orWhereHas('representative', function($rep) {
                $rep->where('company_id', 10);
            })
            ->orWhereHas('supervisor', function($sup) {
                $sup->where('company_id', 10);
            });
        })->count();

        $noonResignations = (clone $statsQuery)->where(function($q) {
            $q->whereHas('employee', function($emp) {
                $emp->where('company_id', 9);
            })
            ->orWhereHas('representative', function($rep) {
                $rep->where('company_id', 9);
            })
            ->orWhereHas('supervisor', function($sup) {
                $sup->where('company_id', 9);
            });
        })->count();

        $departments = \App\Models\Department::all();
        $governorates = \App\Models\Governorate::all();
        $companies = \App\Models\Company::where('is_active', true)->get();

        return view('resignation-requests.index', compact(
            'resignations',
            'departments',
            'governorates',
            'companies',
            'totalResignations',
            /* 'pendingResignations',
            'approvedResignations',
            'rejectedResignations',
            'unresignResignations', */
            'boostaResignations',
            'noonResignations'
        ));
    }

    /**
     * Store is required by the resource route but we don't allow creating
     * resignation requests from the panel (فقط من التطبيق).
     */
    public function store(Request $request)
    {
        return redirect()->route('resignation-requests.index')
            ->with('error', 'لا يمكن إنشاء طلب استقالة من لوحة التحكم. يتم تقديم الطلب من تطبيق المندوب أو الموظف فقط.');
    }

    public function show($id)
    {
        $this->authorize('view_resignation_requests');

        $resignation = ResignationRequest::with(['employee.department', 'representative', 'supervisor', 'approver'])->findOrFail($id);
        return view('resignation-requests.show', compact('resignation'));
    }

    public function approve(Request $request, $id)
    {
        //dd($id);
        $this->authorize('approve_resignation_requests');

        $resignation = ResignationRequest::findOrFail($id);

        if ($resignation->status !== 'pending') {
            return back()->with('error', 'لا يمكن الموافقة على طلب تمت معالجته مسبقاً');
        }

        $validated = $request->validate([
            'appointment_date' => 'required|date',
            'payment_location' => 'required|string|max:255',
            'payment_location_due' => 'nullable|string|max:255',
        ]);

        // منع الموافقة إذا كان على الموظف / المندوب / المشرف مديونية غير مسددة
        $hasUnpaidDebt = Debt::where('status', 'لم يسدد')
            ->where(function ($q) use ($resignation) {
                if ($resignation->employee_id) {
                    $q->orWhere('employee_id', $resignation->employee_id);
                }
                if ($resignation->representative_id) {
                    $q->orWhere('representative_id', $resignation->representative_id);
                }
                if ($resignation->supervisor_id) {
                    $q->orWhere('supervisor_id', $resignation->supervisor_id);
                }
            })
            ->exists();

        if ($hasUnpaidDebt) {
            $message = 'لا يمكن الموافقة على طلب الاستقالة لوجود مديونية غير مسددة. يرجى تسوية المديونية أولاً.';



            $whatsAppService = app(WhatsAppWorkService::class);

            if ($resignation->employee && $resignation->employee->phone) {
                $whatsAppService->send($resignation->employee->phone, $message . "\n\nالموعد: " . $validated['appointment_date'] . "\nالمكان: " . $validated['payment_location']);
            }

            if ($resignation->representative && $resignation->representative->phone) {
                $whatsAppService->send($resignation->representative->phone, $message . "\n\nالموعد: " . $validated['appointment_date'] . "\nالمكان: " . $validated['payment_location']);
            }

            if ($resignation->supervisor && $resignation->supervisor->phone) {
                $whatsAppService->send($resignation->supervisor->phone, $message . "\n\nالموعد: " . $validated['appointment_date'] . "\nالمكان: " . $validated['payment_location']);
            }

            return back()->with('error', 'لا يمكن الموافقة على طلب الاستقالة لوجود مديونية غير مسددة. يرجى تسوية المديونية أولاً.');
        }


        $resignation->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);



        // Deactivate the employee
        $resignation->employee?->update(['is_active' => false]);

        // Deactivate the representative
        $resignation->representative?->update([
            'is_active' => false,
            'resign_date' => now(),
            'unresign_date' => null,
            'unresign_by' => null,
        ]);

        // Deactivate the supervisor
        $resignation->supervisor?->update(['is_active' => false]);

        // Send notification to the user
        $user = $resignation->employee?->user ?? $resignation->representative?->user ?? $resignation->supervisor?->user;
        if ($user) {
            $this->firebaseService->sendResignationRequestApprovalNotification($user, [
                'id' => $resignation->id,
                'resignation_date' => $resignation->resignation_date,
            ]);

            // إرسال رسالة بالموعد لاستلام الأوراق
            $message = "تم تحديد موعد لاستلام الأوراق:\nالتاريخ: {$validated['appointment_date']}\nالمكان: {$validated['payment_location']}";

            $whatsAppService = app(WhatsAppWorkService::class);

            $phones = [
                $resignation->employee?->phone,
                $resignation->representative?->phone,
                $resignation->supervisor?->phone,
                $user->phone ?? null,
            ];

            foreach (array_filter($phones) as $phone) {
                $whatsAppService->send($phone, $message);
            }
        }

        // Note: Removed notifications to all admins/supervisors - only notify the requester

        // Create notification for admins and supervisors
        try {
            $this->notificationService->notifyResignationRequest($resignation, 'approved');
        } catch (\Exception $e) {
            Log::error('Failed to create resignation request approval notification: ' . $e->getMessage());
        }

        return redirect()->route('resignation-requests.index')
            ->with('success', 'تم الموافقة على طلب الاستقالة بنجاح!');
    }

    public function reject(Request $request, $id)
    {
        $this->authorize('approve_resignation_requests');

        $resignation = ResignationRequest::findOrFail($id);

        if ($resignation->status !== 'pending') {
            return back()->with('error', 'لا يمكن رفض طلب تمت معالجته مسبقاً');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $resignation->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        // Send notification to the user
        $user = $resignation->employee?->user ?? $resignation->representative?->user ?? $resignation->supervisor?->user;
        if ($user) {
            $this->firebaseService->sendResignationRequestRejectionNotification($user, [
                'id' => $resignation->id,
                'resignation_date' => $resignation->resignation_date,
            ], $validated['rejection_reason']);
        }

        // Note: Removed notifications to all admins/supervisors - only notify the requester

        // Create notification for admins and supervisors
        try {
            $this->notificationService->notifyResignationRequest($resignation, 'rejected');
        } catch (\Exception $e) {
            Log::error('Failed to create resignation request rejection notification: ' . $e->getMessage());
        }

        return redirect()->route('resignation-requests.index')
            ->with('success', 'تم رفض طلب الاستقالة بنجاح!');
    }

    public function destroy($id)
    {
        $this->authorize('delete_resignation_requests');

        $resignation = ResignationRequest::findOrFail($id);
        $resignation->delete();

        return redirect()->route('resignation-requests.index')
            ->with('success', 'تم حذف طلب الاستقالة بنجاح!');
    }

    public function export(Request $request)
    {
        $this->authorize('view_resignation_requests');

        $resignations = ResignationRequest::with(['employee.department', 'representative', 'supervisor', 'approver'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->get();

        $filename = "resignation_requests_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($resignations) {
            $file = fopen('php://output', 'w');

            // Add BOM for Arabic text
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'الاسم', 'القسم', 'تاريخ الاستقالة', 'آخر يوم عمل', 'السبب',
                'الحالة', 'تمت الموافقة بواسطة', 'تاريخ الموافقة'
            ]);

            foreach ($resignations as $resignation) {
                $name = $resignation->employee?->name
                    ?? $resignation->representative?->name
                    ?? $resignation->supervisor?->name
                    ?? 'غير محدد';
                $department = $resignation->employee?->department?->name ?? 'غير محدد';
                $resignationDate = $resignation->resignation_date ? $resignation->resignation_date->format('Y-m-d') : '-';
                $lastDay = $resignation->last_working_day ? $resignation->last_working_day->format('Y-m-d') : '-';
                $approvedAt = $resignation->approved_at ? $resignation->approved_at->format('Y-m-d') : 'لم تتم المعالجة';

                fputcsv($file, [
                    $name,
                    $department,
                    $resignationDate,
                    $lastDay,
                    $resignation->reason,
                    $resignation->status_text ?? $resignation->status,
                    $resignation->approver->name ?? 'غير محدد',
                    $approvedAt
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }



    public function toggleStatus(Request $request, $id)
    {
        //
        $this->authorize('edit_representatives_no');

        DB::beginTransaction();

        try {

            $type = $request->type; // Representative | Employee | Supervisor
            $model = null;

            switch ($type) {
                case 'Representative':
                    $model = Representative::findOrFail($id);
                    $model->update([
                        'is_active' => ! $model->is_active,
                        'unresign_date' => now(),
                        'unresign_by' => Auth::id(),
                        'resign_date' => null,
                        'company_id' => $request->company_id,
                        'governorate_id' => $request->governorate_id,
                        'location_id' => $request->location_id,
                    ]);
                    break;

                case 'Employee':
                    $model = Employee::findOrFail($id);
                    $model->update([
                        'is_active' => ! $model->is_active,
                    ]);
                    break;

                case 'Supervisor':
                    $model = Supervisor::findOrFail($id);
                    $model->update([
                        'is_active' => ! $model->is_active,
                        'company_id' => $request->company_id,
                        'governorate_id' => $request->governorate_id,
                        'location_id' => $request->location_id,
                    ]);
                    break;

                default:
                    abort(400, 'نوع غير صالح');
            }

            // Sync user status if exists
            if (method_exists($model, 'user') && $model->user) {
                $model->user->update([
                    'is_active' => $model->is_active
                ]);
            }

            // إذا تم تفعيل الشخص، ابحث عن طلب الاستقالة المرتبط به وغير حالته إلى "unresign"
            if ($model->is_active) {
                $resignationRequest = null;

                switch ($type) {
                    case 'Representative':
                        $resignationRequest = ResignationRequest::where('representative_id', $id)

                            ->latest()
                            ->first();
                        break;

                    case 'Employee':
                        $resignationRequest = ResignationRequest::where('employee_id', $id)

                            ->latest()
                            ->first();
                        break;

                    case 'Supervisor':
                        $resignationRequest = ResignationRequest::where('supervisor_id', $id)

                            ->latest()
                            ->first();
                        break;
                }

                // تحديث حالة طلب الاستقالة إلى "unresign"
                if ($resignationRequest) {
                    $resignationRequest->update([
                        'status' => 'unresign',
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                        'active_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            $status = $model->is_active ? 'نشط' : 'غير نشط';

            return redirect()
                ->route('resignation-requests.index')
                ->with('success', "تم تغيير الحالة إلى: {$status}");

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getNotes($id)
    {
        $this->authorize('view_resignation_requests');

        $resignation = ResignationRequest::findOrFail($id);
        $notes = $resignation->notes()->with('createdBy')->get();

        return response()->json([
            'success' => true,
            'notes' => $notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'note' => $note->note,
                    'status' => $note->status,
                    'status_text' => $note->status === 'approved' ? 'موافق' : ($note->status === 'rejected' ? 'غير موافق' : null),
                    'created_by' => $note->createdBy?->name ?? 'غير محدد',
                    'created_at' => $note->created_at->format('Y-m-d H:i'),
                    'created_at_formatted' => $note->created_at->diffForHumans(),
                ];
            })
        ]);
    }

    public function storeNote(Request $request, $id)
    {
        $this->authorize('view_resignation_requests');

        $validated = $request->validate([
            'note' => 'required|string|max:5000',
            'status' => 'nullable|in:approved,rejected',
        ]);

        $resignation = ResignationRequest::findOrFail($id);

        DB::beginTransaction();
        try {
            $note = ResignationRequestNote::create([
                'resignation_request_id' => $resignation->id,
                'note' => $validated['note'],
                'status' => $validated['status'] ?? null,
                'created_by' => Auth::id(),
            ]);

            /* // إذا تم تحديد حالة، قم بتحديث حالة طلب الاستقالة
            if ($validated['status']) {
                // لا يمكن تغيير الحالة إذا كانت "unresign"
                if ($resignation->status !== 'unresign') {
                    $resignation->update([
                        'status' => $validated['status'],
                        'approved_by' => Auth::id(),
                        'approved_at' => now()
                    ]);

                    // إذا تمت الموافقة، قم بتعطيل الموظف/المندوب/المشرف
                    if ($validated['status'] === 'approved') {
                        $resignation->employee?->update(['is_active' => false]);
                        $resignation->representative?->update([
                            'is_active' => false,
                            'resign_date' => now(),
                            'unresign_date' => null,
                            'unresign_by' => null,
                        ]);
                        $resignation->supervisor?->update(['is_active' => false]);

                        // Sync user status if exists
                        $user = $resignation->employee?->user ?? $resignation->representative?->user ?? $resignation->supervisor?->user;
                        if ($user) {
                            $user->update(['is_active' => false]);
                        }
                    }
                }
            } */

            DB::commit();

            $note->load('createdBy');

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الملاحظة بنجاح',
                'note' => [
                    'id' => $note->id,
                    'note' => $note->note,
                    'status' => $note->status,
                    'status_text' => $note->status === 'approved' ? 'موافق' : ($note->status === 'rejected' ? 'غير موافق' : null),
                    'created_by' => $note->createdBy?->name ?? 'غير محدد',
                    'created_at' => $note->created_at->format('Y-m-d H:i'),
                    'created_at_formatted' => $note->created_at->diffForHumans(),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ الملاحظة: ' . $e->getMessage()
            ], 500);
        }
    }

    /* public function updateStatus(Request $request, $id)
    {
        $this->authorize('approve_resignation_requests');

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $resignation = ResignationRequest::findOrFail($id);

        // لا يمكن تغيير الحالة إذا كانت "unresign"
        if ($resignation->status === 'unresign') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تغيير حالة طلب تم الرجوع فيه للعمل'
            ], 400);
        }

        $resignation->update([
            'status' => $validated['status'],
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        // إذا تمت الموافقة، قم بتعطيل الموظف/المندوب/المشرف
        if ($validated['status'] === 'approved') {
            $resignation->employee?->update(['is_active' => false]);
            $resignation->representative?->update([
                'is_active' => false,
                'resign_date' => now(),
                'unresign_date' => null,
                'unresign_by' => null,
            ]);
            $resignation->supervisor?->update(['is_active' => false]);

            // Sync user status if exists
            $user = $resignation->employee?->user ?? $resignation->representative?->user ?? $resignation->supervisor?->user;
            if ($user) {
                $user->update(['is_active' => false]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحالة بنجاح'
        ]);
    } */

    public function report()
    {
        $this->authorize('view_resignation_requests');

        // جلب البيانات: الأشخاص الذين عملوا active وعدد الطلبات لكل واحد
        $activeByStats = ResignationRequest::whereNotNull('active_by')
            ->selectRaw('active_by, COUNT(*) as count')
            ->with('activeBy:id,name,phone')
            ->groupBy('active_by')
            ->get()
            ->map(function ($item) {
                $user = $item->activeBy;
                return [
                    'user_id' => $item->active_by,
                    'user_name' => $user ? $user->name : 'غير محدد',
                    'user_phone' => $user ? $user->phone : 'غير محدد',
                    'count' => $item->count,
                ];
            })
            ->sortByDesc('count')
            ->values();

        return view('resignation-requests.report', compact('activeByStats'));
    }


}
