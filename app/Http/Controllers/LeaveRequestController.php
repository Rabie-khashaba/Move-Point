<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseNotificationService;
use App\Services\NotificationService;

class LeaveRequestController extends Controller
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
        $this->authorize('view_leave_requests');
        
        $leaves = LeaveRequest::with(['employee', 'representative', 'supervisor', 'approver'])
            ->when(request('search'), function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('employee', function($emp) use ($search) {
                        $emp->where('name', 'like', "%{$search}%");
                    })->orWhereHas('representative', function($rep) use ($search) {
                        $rep->where('name', 'like', "%{$search}%");
                    })->orWhereHas('supervisor', function($sup) use ($search) {
                        $sup->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('type'), function($query, $type) {
                $query->where('type', $type);
            })
            ->when(request('date_from'), function($query, $date) {
                $query->where('start_date', '>=', $date);
            })
            ->when(request('date_to'), function($query, $date) {
                $query->where('end_date', '<=', $date);
            })
            ->when(request('mobile_only'), function($query) {
                $query->where(function($q) {
                    $q->whereNotNull('representative_id')->orWhereNotNull('supervisor_id');
                });
            })
            ->latest()
            ->paginate(20);

        return view('leave-requests.index', compact('leaves'));
    }

    public function create()
    {
        $this->authorize('create_leave_requests');
        
        $employees = Employee::active()->get();
        return view('leave-requests.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_leave_requests');
        
        $validated = $request->validate([
            'requester_type' => 'required|in:employee,representative,supervisor',
            'employee_id' => 'nullable|exists:employees,id',
            'representative_id' => 'nullable|exists:representatives,id',
            'supervisor_id' => 'nullable|exists:supervisors,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:سنوية,مرضية,طارئة,أخرى',
            'reason' => 'nullable|string|max:500'
        ]);

        // Set the appropriate ID based on requester_type
        $validated['employee_id'] = null;
        $validated['representative_id'] = null;
        $validated['supervisor_id'] = null;

        switch ($validated['requester_type']) {
            case 'employee':
                $validated['employee_id'] = $request->employee_id;
                break;
            case 'representative':
                $validated['representative_id'] = $request->representative_id;
                break;
            case 'supervisor':
                $validated['supervisor_id'] = $request->supervisor_id;
                break;
        }

        // Validate that the selected ID exists
        $selectedId = $validated['employee_id'] ?? $validated['representative_id'] ?? $validated['supervisor_id'];
        if (empty($selectedId)) {
            return back()->withErrors(['employee_id' => 'يجب اختيار ' . ($validated['requester_type'] === 'employee' ? 'موظف' : ($validated['requester_type'] === 'representative' ? 'مندوب' : 'مشرف'))])->withInput();
        }

        // Check for overlapping leave requests based on the requester type
        $requesterId = $validated['employee_id'] ?? $validated['representative_id'] ?? $validated['supervisor_id'];
        $requesterType = !empty($validated['employee_id']) ? 'employee_id' : (!empty($validated['representative_id']) ? 'representative_id' : 'supervisor_id');
        
        $overlapping = LeaveRequest::where($requesterType, $requesterId)
            ->where('status', '!=', 'rejected')
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function($q) use ($validated) {
                        $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['employee_id' => 'يوجد إجازة أخرى في نفس الفترة'])->withInput();
        }

        // Auto-approve if created from dashboard (employee request)
        if (!empty($validated['employee_id'])) {
            
            $validated['status'] = 'approved';
            $validated['approved_by'] = Auth::id();
            $validated['approved_at'] = now();
        }

        $leaveRequest = LeaveRequest::create($validated);

        // Create notification for admins and supervisors
        try {
            $this->notificationService->notifyLeaveRequest($leaveRequest, 'created');
        } catch (\Exception $e) {
            \Log::error('Failed to create leave request notification: ' . $e->getMessage());
        }

        return redirect()->route('leave-requests.index')
            ->with('success', 'تم إضافة طلب الإجازة بنجاح!');
    }

    public function show($id)
    {
        $this->authorize('view_leave_requests');
        
        $leave = LeaveRequest::with(['employee', 'representative', 'supervisor', 'approver'])->findOrFail($id);
        return view('leave-requests.show', compact('leave'));
    }

    public function edit($id)
    {
        $this->authorize('edit_leave_requests');
        
        $leave = LeaveRequest::findOrFail($id);
        $employees = Employee::active()->get();
        return view('leave-requests.edit', compact('leave', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_leave_requests');
        
        $leave = LeaveRequest::findOrFail($id);
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:سنوية,مرضية,طارئة,أخرى',
            'reason' => 'nullable|string|max:500'
        ]);

        $leave->update($validated);

        return redirect()->route('leave-requests.index')
            ->with('success', 'تم تحديث طلب الإجازة بنجاح!');
    }

    public function approve($id)
    {
        $this->authorize('approve_leave_requests');
        
        $leave = LeaveRequest::findOrFail($id);
        
        if ($leave->status !== 'pending') {
            return back()->with('error', 'لا يمكن الموافقة على طلب تمت معالجته مسبقاً');
        }

        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        // Send notification to the user
        $user = $leave->employee?->user ?? $leave->representative?->user ?? $leave->supervisor?->user;
        if ($user) {
            $this->firebaseService->sendLeaveRequestApprovalNotification($user, [
                'id' => $leave->id,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
            ]);
        }

        // Note: Removed notifications to all admins/supervisors - only notify the requester

        // Create notification for admins and supervisors
        try {
            $this->notificationService->notifyLeaveRequest($leave, 'approved');
        } catch (\Exception $e) {
            \Log::error('Failed to create leave request approval notification: ' . $e->getMessage());
        }

        return redirect()->route('leave-requests.index')
            ->with('success', 'تم الموافقة على طلب الإجازة بنجاح!');
    }

    public function reject(Request $request, $id)
    {
        $this->authorize('approve_leave_requests');
        
        $leave = LeaveRequest::findOrFail($id);
        
        if ($leave->status !== 'pending') {
            return back()->with('error', 'لا يمكن رفض طلب تمت معالجته مسبقاً');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $leave->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        // Send notification to the user
        $user = $leave->employee?->user ?? $leave->representative?->user ?? $leave->supervisor?->user;
        if ($user) {
            $this->firebaseService->sendLeaveRequestRejectionNotification($user, [
                'id' => $leave->id,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
            ], $validated['rejection_reason']);
        }

        // Note: Removed notifications to all admins/supervisors - only notify the requester

        // Create notification for admins and supervisors
        try {
            $this->notificationService->notifyLeaveRequest($leave, 'rejected');
        } catch (\Exception $e) {
            \Log::error('Failed to create leave request rejection notification: ' . $e->getMessage());
        }

        return redirect()->route('leave-requests.index')
            ->with('success', 'تم رفض طلب الإجازة بنجاح!');
    }

    public function destroy($id)
    {
        $this->authorize('delete_leave_requests');
        
        $leave = LeaveRequest::findOrFail($id);
        $leave->delete();

        return redirect()->route('leave-requests.index')
            ->with('success', 'تم حذف طلب الإجازة بنجاح!');
    }
}
