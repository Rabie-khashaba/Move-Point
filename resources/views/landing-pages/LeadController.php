<?php
namespace App\Http\Controllers;

use App\Services\LeadService;
use App\Models\Governorate;
use App\Models\Source;
use App\Models\User;
use App\Models\Lead;
use App\Models\Location;
use App\Models\Reason;
use App\Models\LeadFollowup;
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
            $totalLeads = Lead::count();
            $newLeads = Lead::where('status', 'جديد')->count();
            $followUpLeads = Lead::where('status', 'متابعة')->count();
            $notInterestedLeads = Lead::where('status', 'غير مهتم')->count();
            $interviewLeads = Lead::where('status', 'مقابلة')->count();
            $negotiationLeads = Lead::where('status', 'مفاوضات')->count();
            $closedLeads = Lead::where('status', 'مغلق')->count();
            $lostLeads = Lead::where('status', 'خسر')->count();
            $oldLeads = Lead::where('status', 'قديم')->count();
            // Get leads with eager loading for better performance
            $leads = Lead::with(['governorate', 'source', 'employee.employee', 'representative'])
                ->when($user && $user->type === 'employee', function($query) use ($user) {
                    return $query->where('assigned_to', $user->id);
                })
                ->when($request->filled('status'), function($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->when($request->filled('date_from'), function($query) use ($request) {
                    return $query->whereDate('created_at', '>=', $request->date_from);
                })
                ->when($request->filled('date_to'), function($query) use ($request) {
                    return $query->whereDate('created_at', '<=', $request->date_to);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('leads.index', compact('leads', 'totalLeads', 'newLeads', 'followUpLeads', 'notInterestedLeads', 'interviewLeads', 'negotiationLeads', 'closedLeads', 'lostLeads', 'oldLeads'));
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@index: '.$e->getMessage(), [
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
            $users = User::where('type','employee')->whereHas('employee', function ($query) {
                $query->where('department_id', 7);
            })->get();
            $locations = Location::all();
            $employeeLeadCounts = $this->service->getEmployeeLeadCounts();
            return view('leads.create', compact('governorates', 'sources', 'users', 'locations', 'employeeLeadCounts'));
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@create: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('leads.index')->with('error', 'تعذر تحميل نموذج الإضافة.');
        }
    }

    public function store(Request $request)
    {
        $this->authorize('create_leads');
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|digits:11|unique:leads,phone',
                'governorate_id' => 'required|exists:governorates,id',
                'source_id' => 'required|exists:sources,id',
                'status' => ['nullable', Rule::in(['متابعة','غير مهتم','عمل مقابلة','مقابلة','مفاوضات','مغلق','خسر','جديد','قديم'])],
                'notes' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
                'next_follow_up' => 'nullable|date',
                'location_id' => 'nullable|exists:locations,id',
            ]);

            // If assigned_to is empty, it will be automatically assigned in the service
            if (empty($validated['assigned_to'])) {
                unset($validated['assigned_to']);
            }

            $lead = $this->service->create($validated);
            
            // If lead was automatically assigned, show which employee got it
            if (empty($request->assigned_to) && $lead->assigned_to) {
                $assignedEmployee = User::find($lead->assigned_to);
                $employeeName = $assignedEmployee ? ($assignedEmployee->employee?->name ?? $assignedEmployee->name) : 'غير محدد';
                return redirect()->route('leads.index')->with('success', "تم إنشاء العميل المحتمل بنجاح! تم تعيينه تلقائياً إلى: {$employeeName}");
            }
            
            return redirect()->route('leads.index')->with('success', 'تم إنشاء العميل المحتمل بنجاح!');
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@store: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'تعذر إنشاء العميل المحتمل.')->withInput();
        }
    }

    public function getLocations($governorateId)
    {
        try {
            $locations = Location::where('governorate_id', $governorateId)->get();
            return response()->json($locations);
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@getLocations: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
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
            Log::error('خطأ في LeadController@show: '.$e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
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
            $users = User::where('type','employee')->whereHas('employee', function ($query) {
                $query->where('department_id', 7);
            })->get();
            $employeeLeadCounts = $this->service->getEmployeeLeadCounts();
            return view('leads.edit', compact('lead', 'governorates', 'sources', 'users', 'employeeLeadCounts'));
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@edit: '.$e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
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
            'user_id'       => auth()->id(),
            'lead_id'       => $id,
            'notes'         => $notes,
            'type'          => $request->type,
            'next_follow_up'=> $nextFollowUp,
            'outcome'       => $request->outcome,
            'reason_id'     => $request->reason_id,
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
            
            // If no employee specified, assign to employee with least leads
            if (empty($employeeId)) {
                $employeeId = $this->getEmployeeWithLeastLeads();
                if (!$employeeId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'لا يوجد موظفين متاحين للتعيين'
                    ], 400);
                }
            }

            // Bulk update leads
            Lead::whereIn('id', $request->leads)
                ->update(['assigned_to' => $employeeId]);

            // Get employee name for message using eager loading
            $assignedEmployee = User::with('employee')
                ->find($employeeId);
            $employeeName = $assignedEmployee ? ($assignedEmployee->employee?->name ?? $assignedEmployee->name) : 'غير محدد';

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

    /**
     * Get the employee with the least number of leads (optimized)
     */
    private function getEmployeeWithLeastLeads(): ?int
    {
        // Get all employees
        $employees = User::where('type', 'employee')->pluck('id');
        
        if ($employees->isEmpty()) {
            return null;
        }

        // Get lead counts for each employee using a more efficient query
        $employeeLeadCounts = Lead::selectRaw('assigned_to, COUNT(*) as lead_count')
            ->whereNotNull('assigned_to')
            ->whereIn('assigned_to', $employees)
            ->groupBy('assigned_to')
            ->pluck('lead_count', 'assigned_to')
            ->toArray();

        // Initialize counts for employees with no leads
        foreach ($employees as $employeeId) {
            if (!isset($employeeLeadCounts[$employeeId])) {
                $employeeLeadCounts[$employeeId] = 0;
            }
        }

        // Find employee with minimum lead count
        $minLeads = min($employeeLeadCounts);
        $employeesWithMinLeads = array_keys($employeeLeadCounts, $minLeads);
        
        // If multiple employees have the same minimum, choose randomly
        $selectedEmployeeId = $employeesWithMinLeads[array_rand($employeesWithMinLeads)];
        
        return $selectedEmployeeId;
    }
    public function updateStatus(Request $request, $id)
    {
        $this->authorize('edit_leads');

        $validated = $request->validate([
            'status' => ['required', Rule::in(['متابعة','غير مهتم','مقابلة','عمل مقابلة','مفاوضات','مغلق','خسر','جديد','قديم'])],
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
            Log::error('خطأ في LeadController@updateStatus: '.$e->getMessage(), [
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
                'status' => ['nullable', Rule::in(['متابعة','غير مهتم','عمل مقابلة','مقابلة','مفاوضات','مغلق','خسر','جديد','قديم'])],
                'notes' => 'nullable|string',
                'assigned_to' => 'nullable|exists:users,id',
                'next_follow_up' => 'nullable|date',
            ]);

            // If assigned_to is empty, it will be automatically assigned in the service
            if (empty($validated['assigned_to'])) {
                unset($validated['assigned_to']);
            }

            $this->service->update($id, $validated);
            return redirect()->route('leads.show', $id)->with('success', 'تم تحديث بيانات العميل المحتمل بنجاح');
        } catch (\Throwable $e) {
            Log::error('خطأ في LeadController@update: '.$e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
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
            Log::error('خطأ في LeadController@destroy: '.$e->getMessage(), ['id' => $id, 'trace' => $e->getTraceAsString()]);
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'تعذر حذف العميل المحتمل'], 500);
            }
            return redirect()->route('leads.index')->with('error', 'تعذر حذف العميل المحتمل.');
        }
    }
}
