<?php

namespace App\Http\Controllers;

use App\Services\LeadService;
use App\Models\Governorate;
use App\Models\Source;
use App\Models\User;
use App\Models\Lead;
use App\Models\Location;
use App\Models\Advertiser;
use App\Models\Reason;
use App\Models\LeadFollowup;
use App\Models\Representative;
use App\Imports\LeadImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    protected $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('view_leads');

        try {
            $user = auth()->user();
            $filters = [];

            // Employee filter
            if ($user && $user->type === 'employee') {
                $filters['assigned_to'] = $user->id;
            }

            // Status filter
            if ($request->filled('status')) {
                $filters['status'] = $request->status;
            }

            // Date range filter
            if ($request->filled('date_from') || $request->filled('date_to')) {
                $filters['date_range'] = [
                    'from' => $request->date_from,
                    'to' => $request->date_to
                ];
            }

            //            $totalLeads = Lead::count();
            //
            //            $followUpLeads = Lead::where('status', 'متابعة')->count();
            //            $notInterestedLeads = Lead::where('status', 'غير مهتم')->count();
            //            $interviewLeads = Lead::where('status', 'مقابلة')->count();

            // فلترة بالـ dates
            $leadQuery = Lead::with(['governorate', 'location', 'source', 'employee.employee', 'representative', 'moderator', 'referredBy', 'lastFollowUp'])
                ->join('governorates', 'leads.governorate_id', '=', 'governorates.id')
                ->when($request->filled('assigned_to'), function ($query) use ($request) {
                    if ($request->assigned_to == 0) {
                        // الموظف لم يتم تحديده
                        return $query->whereNull('leads.assigned_to');
                    } else {
                        return $query->where('leads.assigned_to', $request->assigned_to);
                    }
                })
                ->when($request->filled('date_from'), function ($query) use ($request) {
                    return $query->whereDate('leads.created_at', '>=', $request->date_from);
                })
                ->when($request->filled('date_to'), function ($query) use ($request) {
                    return $query->whereDate('leads.created_at', '<=', $request->date_to);
                })
                ->when($request->filled('governorate_id'), function ($query) use ($request) {
                    return $query->where('leads.governorate_id', $request->governorate_id);
                })
                ->when($request->filled('location_id'), function ($query) use ($request) {
                    return $query->where('leads.location_id', $request->location_id);
                })
                ->when($request->filled('transportation'), function ($query) use ($request) {
                    if ($request->transportation === '__none__') {
                        return $query->where(function ($q) {
                            $q->whereNull('leads.transportation')
                                ->orWhere('leads.transportation', '');
                        });
                    }
                    return $query->where('leads.transportation', $request->transportation);
                })
                ->where(function ($q) {
                    $q->where('governorates.is_active', true)
                        ->orWhere(function ($q2) {
                            $q2->where('governorates.is_active', false)
                                ->where(function ($q3) {
                                    $q3->whereNull('governorates.inactive_date')
                                        ->orWhereRaw('DATE(leads.created_at) < DATE(governorates.inactive_date)');
                                });
                        });
                })
                ->select('leads.*');




            $totalLeads = (clone $leadQuery)->count();
            $followUpLeads = (clone $leadQuery)->where('status', 'متابعة')->count();
            $notInterestedLeads = (clone $leadQuery)->where('status', 'غير مهتم')->count();
            $interviewLeads = (clone $leadQuery)->where('status', 'مقابلة')->count();
            $newLeads = (clone $leadQuery)->where('status', 'جديد')->count();
            $notRespondedLeads = (clone $leadQuery)->where('status', 'لم يرد')->count();
            $nightShiftLeads = (clone $leadQuery)->where('status', 'شفت مسائي')->count();
            $noTransportLeads = (clone $leadQuery)->where('status', 'بدون وسيلة نقل')->count();

            //            $newLeads = Lead::where('status', 'جديد')->count();
            $negotiationLeads = Lead::where('status', 'مفاوضات')->count();
            $closedLeads = Lead::where('status', 'مغلق')->count();
            $lostLeads = Lead::where('status', 'خسر')->count();
            $oldLeads = Lead::where('status', 'قديم')->count();
            //$notRespondedLeads = Lead::where('status', 'لم يرد')->count();
            // Get leads with eager loading for better performance
            $leads = (clone $leadQuery)
                ->when($user && $user->type === 'employee', function ($query) use ($user) {
                    return $query->where('assigned_to', $user->id);
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->when($request->filled('search'), function ($query) use ($request) {
                    $term = trim($request->search);
                    return $query->where(function ($q) use ($term) {
                        $q->where('leads.name', 'like', "%{$term}%")
                            ->orWhere('leads.phone', 'like', "%{$term}%")
                            ->orWhere('governorates.name', 'like', "%{$term}%")
                            ->orWhereHas('employee.employee', function ($e) use ($term) {
                                $e->where('name', 'like', "%{$term}%");
                            });
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $governorates = Governorate::orderBy('name')->get();
            $locations = Location::orderBy('name')->get();

            return view('leads.index', compact('leads', 'totalLeads', 'newLeads', 'followUpLeads', 'notInterestedLeads', 'interviewLeads', 'negotiationLeads', 'closedLeads', 'lostLeads', 'oldLeads', 'notRespondedLeads', 'noTransportLeads', 'nightShiftLeads', 'governorates', 'locations'));
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'تعذر تحميل العملاء المحتملين.');
        }
    }

    public function create()
    {
        $this->authorize('create_leads');
        try {
            $governorates = Governorate::all();
            $sources = Source::all();
            $users = User::where('type', 'employee')->whereHas('employee', function ($query) {
                $query->where('department_id', 7)->where('is_active', true);
            })->get();
            $locations = Location::all();
            $advertisers = Advertiser::all();
            $employeeLeadCounts = $this->service->getEmployeeLeadCounts();
            return view('leads.create', compact('governorates', 'sources', 'users', 'locations', 'employeeLeadCounts', 'advertisers'));
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@create: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('leads.index')->with('error', 'تعذر تحميل نموذج الإضافة.');
        }
    }

    public function showImportForm()
    {
        $this->authorize('create_leads');
        $importFailures = session('import_failures');
        if ($importFailures) {
            session()->forget('import_failures');
        }
        return view('leads.import', compact('importFailures'));
    }

    public function import(Request $request)
    {
        $this->authorize('create_leads');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
        $import = new LeadImport($this->service, auth()->id());
        $import->import($request->file('file'));

        $failures   = $import->getFailures();
        $imported   = $import->getImportedCount();
        $skipped    = $import->getSkippedCount();
        $duplicates = $import->getDuplicatePhones();
        $duplicateErrorMessage = 'رقم الهاتف موجود بالفعل';
        $nonDuplicateFailures = array_values(array_filter($failures, function ($failure) use ($duplicateErrorMessage) {
            $errors = $failure['errors'] ?? [];
            foreach ($errors as $error) {
                if ($error !== $duplicateErrorMessage) {
                    return true;
                }
            }
            return false;
        }));

        $redirect = redirect()->route('leads.import.form');

        if (!empty($duplicates)) {
            $redirect->with('duplicate_phones', $duplicates);
        }

        if (!empty($nonDuplicateFailures)) {
            $redirect->with('import_failures', $nonDuplicateFailures);
            $redirect->with('error', 'فيه مشكله في الشيت راجع البيانات مره اخري');
        }

        if ($imported > 0) {
            $message = "تم اضافه {$imported} عميل محتمل.";
            if ($skipped > 0) {
                $message .= " تم تخطي {$skipped} سجل.";
            }
            $redirect->with('success', $message);
        }

        return $redirect;
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@import: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'تعذر استيراد العملاء المحتملين.');
        }
    }

    public function store(Request $request)
    {
        // return $request;
        $this->authorize('create_leads');
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|digits:11|unique:leads,phone',
                'governorate_id' => 'required|exists:governorates,id',
                'source_id' => 'required|exists:sources,id',
                'status' => ['nullable', Rule::in(['متابعة', 'لم يرد', 'غير مهتم', 'عمل مقابلة', 'مقابلة', 'مفاوضات', 'مغلق', 'خسر', 'جديد', 'قديم'])],
                'notes' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
                'next_follow_up' => 'nullable|date',
                'location_id' => 'nullable|exists:locations,id',
                'advertiser_id' => 'nullable|exists:advertisers,id',
                'transportation' => 'nullable|string',
            ]);

            // If assigned_to is empty, it will be automatically assigned in the service
            /* if (empty($validated['assigned_to'])) {
                unset($validated['assigned_to']);
            } */


            $validated['moderator_id'] = auth()->id();
            $lead = $this->service->create($validated);

            // If lead was automatically assigned, show which employee got it
            /* if (empty($request->assigned_to) && $lead->assigned_to) {
                $assignedEmployee = User::find($lead->assigned_to);
                $employeeName = $assignedEmployee ? ($assignedEmployee->employee?->name ?? $assignedEmployee->name) : 'غير محدد';
                return redirect()->route('leads.index')->with('success', "تم إنشاء العميل المحتمل بنجاح! تم تعيينه تلقائياً إلى: {$employeeName}");
            } */

            return redirect()->route('leads.index')->with('success', 'تم إنشاء العميل المحتمل بنجاح!');
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@store: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'تعذر إنشاء العميل المحتمل.')->withInput();
        }
    }

    public function getLocations($governorateId)
    {
        try {
            $locations = Location::where('governorate_id', $governorateId)->get();
            return response()->json($locations);
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@getLocations: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([], 500);
        }
    }

    public function show($id)
    {
        $this->authorize('view_leads');
        try {
            $lead = Lead::with(['governorate', 'source', 'employee.employee', 'followUps.user', 'followUps.reason'])->findOrFail($id);
            $governorates = Governorate::all();
            $sources = Source::all();
            $users = User::all();
            return view('leads.show', compact('lead', 'governorates', 'sources', 'users'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('leads.index')->with('error', 'العميل المحتمل غير موجود.');
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@show: ' . $e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
            return redirect()->route('leads.index')->with('error', 'حدث خطأ غير متوقع.');
        }
    }

    public function edit($id)
    {
        $this->authorize('edit_leads');
        try {
            $lead = $this->service->find($id);
            $governorates = Governorate::all();
            $sources = Source::all();
            $users = User::where('type', 'employee')->whereHas('employee', function ($query) {
                $query->where('department_id', 7);
            })->get();
            $employeeLeadCounts = $this->service->getEmployeeLeadCounts();
            return view('leads.edit', compact('lead', 'governorates', 'sources', 'users', 'employeeLeadCounts'));
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@edit: ' . $e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
            return redirect()->route('leads.index')->with('error', 'تعذر تحميل بيانات العميل المحتمل للتعديل.');
        }
    }

    public function addFollowup(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        // Dynamic validation based on status
        $validationRules = [
            'notes' => 'nullable|string',
            'status' => 'nullable|string',
            'type' => 'nullable|string',
            'next_follow_up' => 'nullable|date',
            'outcome' => 'nullable|string',
            'status' => 'nullable|string',
        ];

        // Add reason_id validation only for غير مهتم status
        if ($request->filled('status') && $request->status === 'غير مهتم') {
            $validationRules['reason_id'] = 'required|exists:reasons,id';
        }

        $request->validate($validationRules);

        // Set next_follow_up to today if not provided
        $nextFollowUp = $request->next_follow_up ?? now()->format('Y-m-d');

        // Prepare notes
        $notes = $request->notes;


        // Create the follow-up
        $followup = $lead->followUps()->create([
            'user_id' => auth()->id(),
            'lead_id' => $id,
            'notes' => $notes,
            'type' => $request->type,
            'next_follow_up' => $nextFollowUp,
            'outcome' => $request->outcome,
            'reason_id' => $request->reason_id,
        ]);

        // Update lead status if provided
        if ($request->filled('status')) {
            $lead->update(['status' => $request->status]);
        } else {
            // Update lead status from "جديد" to "متابعة" if needed (only if no status was provided)
            if ($lead->status === 'جديد') {
                $lead->update(['status' => 'متابعة']);
            }
        }

        return redirect()->route('leads.show', $id)
            ->with('success', 'تمت إضافة المتابعة بنجاح');
    }

    public function bulkAssign(Request $request)
    {
        $this->authorize('edit_leads');

        $request->validate([
            'leads' => 'required|array',
            'leads.*' => 'exists:leads,id',
            'employee_id' => 'nullable|exists:users,id'
        ]);

        try {
            $employeeId = $request->employee_id;

            // If no employee specified, assign in round-robin across eligible employees
            if (empty($employeeId)) {
                $assignments = $this->service->distributeLeadsRoundRobin($request->leads);
                if (empty($assignments)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'لا يوجد موظفين متاحين للتعيين'
                    ], 400);
                }

                foreach ($assignments as $leadId => $assigneeId) {
                    // تحديث lead
                    \App\Models\Lead::where('id', $leadId)
                        ->update(['assigned_to' => $assigneeId]);

                    // تحديث كل المقابلات الخاصة بالـ lead ده
                    \App\Models\Interview::where('lead_id', $leadId)
                        ->update(['assigned_to' => $assigneeId]);
                }

                $employeeId = null; // already distributed individually
            }

            // If specific employee chosen, bulk update
            if ($employeeId) {
                Lead::whereIn('id', $request->leads)
                    ->update(['assigned_to' => $employeeId]);

                // تحديث كل المقابلات الخاصة بالـ lead ده
                \App\Models\Interview::where('lead_id', $request->leads)
                    ->update(['assigned_to' => $employeeId]);
            }

            // Get employee name for message using eager loading
            $employeeName = $employeeId
                ? (User::with('employee')->find($employeeId)?->employee?->name ?? User::find($employeeId)?->name ?? 'غير محدد')
                : 'تم توزيع العملاء على عدة موظفين';

            return response()->json([
                'success' => true,
                'message' => "تمت إعادة توزيع العملاء المحتملين بنجاح إلى: {$employeeName}",
                'redirect' => route('leads.index')
            ]);
        } catch (\Throwable $e) {
            Log::error('خطأ أثناء إعادة توزيع العملاء المحتملين: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'تعذر إعادة توزيع العملاء المحتملين'
            ], 500);
        }
    }

    // Removed duplicate least-leads logic; using LeadService instead
    public function updateStatus(Request $request, $id)
    {
        $this->authorize('edit_leads');

        $validated = $request->validate([
            'status' => ['required', Rule::in(['متابعة', 'لم يرد', 'غير مهتم', 'مقابلة', 'عمل مقابلة', 'مفاوضات', 'مغلق', 'خسر', 'جديد', 'قديم'])],
        ]);

        try {
            $lead = $this->service->updateStatus($id, $validated['status']);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث الحالة بنجاح',
                    'lead' => $lead
                ]);
            }

            return redirect()->route('leads.show', $id)->with('success', 'تم تحديث حالة العميل المحتمل بنجاح');
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@updateStatus: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'تعذر تحديث الحالة'], 500);
            }

            return redirect()->back()->with('error', 'تعذر تحديث الحالة.');
        }
    }


    public function update(Request $request, $id)
    {
        $this->authorize('edit_leads');
        try {
            $validated = $request->validate([
                'name' => 'string|max:255',
                'phone' => 'digits:11|unique:leads,phone',
                'governorate_id' => 'exists:governorates,id',
                'source_id' => 'exists:sources,id',
                'status' => ['nullable', Rule::in(['متابعة', 'لم يرد', 'غير مهتم', 'عمل مقابلة', 'مقابلة', 'مفاوضات', 'مغلق', 'خسر', 'جديد', 'قديم'])],
                'notes' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
                'next_follow_up' => 'nullable|date',
                'transportation' => 'nullable|string',

            ]);

            // If assigned_to is empty, it will be automatically assigned in the service
            if (empty($validated['assigned_to'])) {
                unset($validated['assigned_to']);
            }

            $this->service->update($id, $validated);
            return redirect()->route('leads.show', $id)->with('success', 'تم تحديث بيانات العميل المحتمل بنجاح');
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@update: ' . $e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'تعذر تحديث بيانات العميل المحتمل.')->withInput();
        }
    }

    public function destroy($id)
    {
        $this->authorize('delete_leads');
        try {
            $this->service->delete($id);

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'تم حذف العميل المحتمل بنجاح']);
            }

            return redirect()->route('leads.index')->with('success', 'تم حذف العميل المحتمل بنجاح');
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@destroy: ' . $e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'تعذر حذف العميل المحتمل'], 500);
            }
            return redirect()->route('leads.index')->with('error', 'تعذر حذف العميل المحتمل.');
        }
    }

    public function waiting(Request $request)
    {

        $this->authorize('view_leads');
        try {
            $leads = Lead::with(['governorate', 'location', 'source'])
                ->join('governorates', 'leads.governorate_id', '=', 'governorates.id')
                ->when($request->filled('search'), function ($query) use ($request) {
                    $term = trim($request->search);
                    return $query->where(function ($q) use ($term) {
                        $q->where('leads.name', 'like', "%{$term}%")
                            ->orWhere('leads.phone', 'like', "%{$term}%")
                            ->orWhere('governorates.name', 'like', "%{$term}%");
                    });
                })
                ->when($request->filled('governorate_id'), function ($query) use ($request) {
                    return $query->where('leads.governorate_id', $request->governorate_id);
                })
                ->when($request->filled('location_id'), function ($query) use ($request) {
                    return $query->where('leads.location_id', $request->location_id);
                })
                ->when($request->filled('date_from'), function ($query) use ($request) {
                    return $query->whereDate('leads.created_at', '>=', $request->date_from);
                })
                ->when($request->filled('date_to'), function ($query) use ($request) {
                    return $query->whereDate('leads.created_at', '<=', $request->date_to);
                })
                ->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->where('governorates.is_active', false)
                            ->where(function ($q3) {
                                $q3->whereNull('governorates.inactive_date')
                                    ->orWhereRaw('DATE(leads.created_at) >= DATE(governorates.inactive_date)');
                            });
                    })
                        ->orWhereHas('location', function ($l) {
                            $l->where('is_active', false);
                        });
                })
                ->select('leads.*')
                ->orderBy('leads.created_at', 'desc')
                ->paginate(20);


            $governorates = Governorate::where('is_active', false)
                ->withCount([
                    'leads as leads_count' => function ($q) use ($request) {
                        $q->when(
                            $request->filled('date_from'),
                            fn($q2) =>
                            $q2->whereDate('leads.created_at', '>=', $request->date_from)
                        );
                        $q->when(
                            $request->filled('date_to'),
                            fn($q2) =>
                            $q2->whereDate('leads.created_at', '<=', $request->date_to)
                        );
                        // فلترة بناءً على inactive_date
                        $q->where(function ($q2) {
                            $q2->whereNull('governorates.inactive_date')
                                ->orWhereRaw('DATE(leads.created_at) >= DATE(governorates.inactive_date)');
                        });
                    }
                ])
                ->get();



            return view('leads.waiting', compact('leads', 'governorates'));
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@waiting: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('leads.index')->with('error', 'تعذر تحميل قائمة الانتظار.');
        }
    }

    public function search(Request $request)
    {
        $this->authorize('view_leads');

        $phone = $request->get('phone');
        $lead = null;
        $representative = null; // ✅ الحل هنا

        if ($phone) {
            try {
                $lead = Lead::where('phone', 'LIKE', '%' . $phone . '%')
                    ->select('id', 'name', 'phone', 'assigned_to')
                    ->with(['assignedTo.employee'])
                    ->first();


                // البحث عن مندوب بنفس رقم الهاتف
                $representative = Representative::where('phone', $phone)->first();
            } catch (\Exception $e) {
                Log::error('Error in LeadController@search: ' . $e->getMessage());
                return back()->with('error', 'حدث خطأ أثناء البحث');
            }
        }

        return view('leads.search', compact('lead', 'phone', 'representative'));
    }
}

