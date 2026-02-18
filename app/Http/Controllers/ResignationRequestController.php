<?php

namespace App\Http\Controllers;

use App\Exports\ResignationRequestsExport;
use App\Models\ResignationRequest;
use App\Models\ResignationRequestNote;
use App\Models\Employee;
use App\Models\Debt;
use App\Models\DebtSheet;
use App\Models\Representative;
use App\Models\Supervisor;
use App\Models\Company;
use App\Models\ResignationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseNotificationService;
use App\Services\NotificationService;
use App\Services\WhatsAppServicebyair;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;



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

        $resignations = ResignationRequest::with(['employee.department', 'employee.company', 'representative.company', 'supervisor.company', 'approver', 'latestNote'])
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

        $departments = \App\Models\Department::all();
        $governorates = \App\Models\Governorate::all();
        $companies = Company::where('is_active', true)->get();
        $resignationMessages = ResignationMessage::latest()->get();
        $companyResignationStats = $companies->map(function ($company) use ($statsQuery) {
            $count = (clone $statsQuery)->where(function ($q) use ($company) {
                $q->whereHas('employee', function ($emp) use ($company) {
                    $emp->where('company_id', $company->id);
                })
                ->orWhereHas('representative', function ($rep) use ($company) {
                    $rep->where('company_id', $company->id);
                })
                ->orWhereHas('supervisor', function ($sup) use ($company) {
                    $sup->where('company_id', $company->id);
                });
            })->count();

            return [
                'id' => $company->id,
                'name' => $company->name,
                'count' => $count,
            ];
        });

        return view('resignation-requests.index', compact(
            'resignations',
            'departments',
            'governorates',
            'companies',
            'resignationMessages',
            'companyResignationStats',
            'totalResignations'
        ));
    }

    public function show($id)
    {
        $this->authorize('view_resignation_requests');

        $resignation = ResignationRequest::with(['employee.department', 'representative', 'supervisor', 'approver'])->findOrFail($id);
        return view('resignation-requests.show', compact('resignation'));
    }

    // public function approve($id)
    // {
    //     $this->authorize('approve_resignation_requests');

    //     $resignation = ResignationRequest::findOrFail($id);

    //     if ($resignation->status !== 'pending') {
    //         return back()->with('error', 'لا يمكن الموافقة على طلب تمت معالجته مسبقاً');
    //     }

    //     $resignation->update([
    //         'status' => 'approved',
    //         'approved_by' => Auth::id(),
    //         'approved_at' => now()
    //     ]);

    //     // Deactivate the employee
    //     $resignation->employee?->update(['is_active' => false]);

    //     // Send notification to the user
    //     $user = $resignation->employee?->user ?? $resignation->representative?->user ?? $resignation->supervisor?->user;
    //     if ($user) {
    //         $this->firebaseService->sendResignationRequestApprovalNotification($user, [
    //             'id' => $resignation->id,
    //             'resignation_date' => $resignation->resignation_date,
    //         ]);
    //     }

    //     // Note: Removed notifications to all admins/supervisors - only notify the requester

    //     // Create notification for admins and supervisors
    //     try {
    //         $this->notificationService->notifyResignationRequest($resignation, 'approved');
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to create resignation request approval notification: ' . $e->getMessage());
    //     }

    //     return redirect()->route('resignation-requests.index')
    //         ->with('success', 'تم الموافقة على طلب الاستقالة بنجاح!');
    // }



    public function approve(Request $request, $id)
    {
        //dd($id);
        $this->authorize('approve_resignation_requests');

        $resignation = ResignationRequest::findOrFail($id);

        if (!in_array($resignation->status, ['pending', 'initial_approved'], true)) {
            return back()->with('error', 'لا يمكن الموافقة على طلب تمت معالجته مسبقاً');
        }

        $validated = $request->validate([
            'appointment_date' => 'required|date',
            'payment_location' => 'required|string|max:255',
            'payment_location_due' => 'nullable|string|max:255',
        ]);

        // منع الموافقة إذا كان على الموظف / المندوب / المشرف مديونية غير مسددة
        $hasUnpaidDebt = $this->hasOutstandingDebt($resignation);

        if ($hasUnpaidDebt) {
            $message = 'لا يمكن الموافقة على طلب الاستقالة لوجود مديونية غير مسددة. يرجى تسوية المديونية أولاً.';



            $whatsapp = app(WhatsAppServicebyair::class);
            $employee = auth()->user()?->employee;
            $deviceToken = $employee?->device?->device_token;

            if ($resignation->employee && $resignation->employee->phone) {
                $whatsapp->send2($resignation->employee->phone, $message . "\n\nالموعد: " . $validated['appointment_date'] . "\nالمكان: " . $validated['payment_location'], null, null, null, $deviceToken);
            }

            if ($resignation->representative && $resignation->representative->phone) {
                $whatsapp->send2($resignation->representative->phone, $message . "\n\nالموعد: " . $validated['appointment_date'] . "\nالمكان: " . $validated['payment_location'], null, null, null, $deviceToken);
            }

            if ($resignation->supervisor && $resignation->supervisor->phone) {
                $whatsapp->send2($resignation->supervisor->phone, $message . "\n\nالموعد: " . $validated['appointment_date'] . "\nالمكان: " . $validated['payment_location'], null, null, null, $deviceToken);
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

            $whatsapp = app(WhatsAppServicebyair::class);
            $employee = auth()->user()?->employee;
            $deviceToken = $employee?->device?->device_token;

            $phones = [
                $resignation->employee?->phone,
                $resignation->representative?->phone,
                $resignation->supervisor?->phone,
                $user->phone ?? null,
            ];

            foreach (array_filter($phones) as $phone) {
                $whatsapp->send2($phone, $message, null, null, null, $deviceToken);
            }
        }

        // Note: Removed notifications to all admins/supervisors - only notify the requester

        // Create notification for admins and supervisors
        try {
            $this->notificationService->notifyResignationRequest($resignation, 'approved');
        } catch (\Exception $e) {
            \Log::error('Failed to create resignation request approval notification: ' . $e->getMessage());
        }

        return redirect()->route('resignation-requests.index')
            ->with('success', 'تم الموافقة على طلب الاستقالة بنجاح!');
    }

    public function initialApprove(Request $request, $id)
    {
        $this->authorize('approve_resignation_requests');

        $validated = $request->validate([
            'message_id' => 'required|exists:resignation_messages,id',
        ]);

        $resignation = ResignationRequest::with(['employee', 'representative', 'supervisor'])->findOrFail($id);

        if ($resignation->status !== 'pending') {
            return back()->with('error', 'لا يمكن تنفيذ الموافقة المبدئية على طلب تمت معالجته مسبقاً');
        }

        $hasUnpaidDebt = $this->hasOutstandingDebt($resignation);

        if ($hasUnpaidDebt) {
            return back()->with('error', 'لا يمكن إرسال الموافقة المبدئية لوجود مديونية غير مسددة. يرجى تسوية المديونية أولاً.');
        }

        $resignationMessage = ResignationMessage::findOrFail($validated['message_id']);

        $resignation->update([
            'status' => 'initial_approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $whatsapp = app(WhatsAppServicebyair::class);
        $employee = auth()->user()?->employee;
        $deviceToken = $employee?->device?->device_token;
        $phones = [
            $resignation->employee?->phone,
            $resignation->representative?->phone,
            $resignation->supervisor?->phone,
        ];
        foreach (array_filter($phones) as $phone) {
            $whatsapp->send2($phone, $resignationMessage->content, null, null, null, $deviceToken);
        }

        return back()->with('success', 'تمت الموافقة المبدئية وإرسال الرسالة بنجاح.');
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
            \Log::error('Failed to create resignation request rejection notification: ' . $e->getMessage());
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

        $filename = "resignation_requests_" . now()->format('Y-m-d') . ".xlsx";

        return Excel::download(new ResignationRequestsExport($request), $filename);
    }



    public function toggleStatus(Request $request, $id)
    {
        //return $request->all();
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
                $statusMap = [
                    'approved' => ['text' => 'موافق', 'class' => 'success'],
                    'rejected' => ['text' => 'غير موافق', 'class' => 'danger'],
                    'no_reply' => ['text' => 'لم يرد', 'class' => 'warning'],
                    'follow_up_again' => ['text' => 'متابعة مرة أخرى', 'class' => 'info'],
                    'other' => ['text' => 'أخرى', 'class' => 'secondary'],
                ];

                return [
                    'id' => $note->id,
                    'note' => $note->note,
                    'status' => $note->status,
                    'status_text' => $statusMap[$note->status]['text'] ?? null,
                    'status_class' => $statusMap[$note->status]['class'] ?? 'secondary',
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
            'status' => 'required|in:no_reply,follow_up_again,other',
        ]);

        $resignation = ResignationRequest::findOrFail($id);

        DB::beginTransaction();
        try {
            $note = ResignationRequestNote::create([
                'resignation_request_id' => $resignation->id,
                'note' => $validated['note'],
                'status' => $validated['status'],
                'created_by' => Auth::id(),
            ]);

            // إذا تم تحديد حالة، قم بتحديث حالة طلب الاستقالة
            // if ($validated['status']) {
            //     // لا يمكن تغيير الحالة إذا كانت "unresign"
            //     if ($resignation->status !== 'unresign') {
            //         $resignation->update([
            //             'status' => $validated['status'],
            //             'approved_by' => Auth::id(),
            //             'approved_at' => now()
            //         ]);

            //         // إذا تمت الموافقة، قم بتعطيل الموظف/المندوب/المشرف
            //         if ($validated['status'] === 'approved') {
            //             $resignation->employee?->update(['is_active' => false]);
            //             $resignation->representative?->update([
            //                 'is_active' => false,
            //                 'resign_date' => now(),
            //                 'unresign_date' => null,
            //                 'unresign_by' => null,
            //             ]);
            //             $resignation->supervisor?->update(['is_active' => false]);

            //             // Sync user status if exists
            //             $user = $resignation->employee?->user ?? $resignation->representative?->user ?? $resignation->supervisor?->user;
            //             if ($user) {
            //                 $user->update(['is_active' => false]);
            //             }
            //         }
            //     }
            // }

            DB::commit();

            $note->load('createdBy');

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الملاحظة بنجاح',
                'note' => [
                    'id' => $note->id,
                    'note' => $note->note,
                    'status' => $note->status,
                    'status_text' => [
                        'no_reply' => 'لم يرد',
                        'follow_up_again' => 'متابعة مرة أخرى',
                        'other' => 'أخرى',
                    ][$note->status] ?? null,
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

    private function hasOutstandingDebt(ResignationRequest $resignation): bool
    {
        $hasDebtRecord = Debt::where('status', 'لم يسدد')
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

        $code = $resignation->employee?->code
            ?? $resignation->representative?->code
            ?? $resignation->supervisor?->code;

        $hasDebtSheetValues = false;
        if (!empty($code)) {
            $hasDebtSheetValues = DebtSheet::where('star_id', (string) $code)
                ->where(function ($q) {
                    $q->where('shortage', '>', 0)
                        ->orWhere('credit_note', '>', 0)
                        ->orWhere('advances', '>', 0);
                })
                ->exists();
        }

        return $hasDebtRecord || $hasDebtSheetValues;
    }

}
