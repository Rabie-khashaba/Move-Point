<?php

namespace App\Http\Controllers;

use App\Models\WorkSchedule;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkScheduleController extends Controller
{
    public function index()
    {
        $this->authorize('view_work_schedules');
        
        $schedules = WorkSchedule::with('employee')
            ->when(request('search'), function($query, $search) {
                $query->whereHas('employee', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when(request('shift'), function($query, $shift) {
                $query->where('shift', $shift);
            })
            ->when(request('status'), function($query, $status) {
                $query->where('is_active', $status);
            })
            ->latest()
            ->paginate(20);

        return view('work-schedules.index', compact('schedules'));
    }

    public function create()
    {
        $this->authorize('create_work_schedules');
        
        $employees = Employee::active()->get();
        return view('work-schedules.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_work_schedules');
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift' => 'required|in:صباحي,مسائي',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'work_days' => 'required|array|min:1',
            'work_days.*' => 'in:السبت,الأحد,الاثنين,الثلاثاء,الأربعاء,الخميس,الجمعة',
            'effective_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:effective_date',
            'notes' => 'nullable|string|max:500'
        ]);

        // Deactivate previous active schedule for this employee
        WorkSchedule::where('employee_id', $validated['employee_id'])
            ->where('is_active', true)
            ->update(['is_active' => false, 'end_date' => now()]);

        WorkSchedule::create($validated);

        return redirect()->route('work-schedules.index')
            ->with('success', 'تم إضافة مواعيد العمل بنجاح!');
    }

    public function show($id)
    {
        $this->authorize('view_work_schedules');
        
        $schedule = WorkSchedule::with('employee')->findOrFail($id);
        return view('work-schedules.show', compact('schedule'));
    }

    public function edit($id)
    {
        $this->authorize('edit_work_schedules');
        
        $schedule = WorkSchedule::findOrFail($id);
        $employees = Employee::active()->get();
        return view('work-schedules.edit', compact('schedule', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_work_schedules');
        
        $schedule = WorkSchedule::findOrFail($id);
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift' => 'required|in:صباحي,مسائي',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'work_days' => 'required|array|min:1',
            'work_days.*' => 'in:السبت,الأحد,الاثنين,الثلاثاء,الأربعاء,الخميس,الجمعة',
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date|after:effective_date',
            'notes' => 'nullable|string|max:500'
        ]);

        $schedule->update($validated);

        return redirect()->route('work-schedules.index')
            ->with('success', 'تم تحديث مواعيد العمل بنجاح!');
    }

    public function destroy($id)
    {
        $this->authorize('delete_work_schedules');
        
        $schedule = WorkSchedule::findOrFail($id);
        $schedule->update(['is_active' => false, 'end_date' => now()]);

        return redirect()->route('work-schedules.index')
            ->with('success', 'تم إلغاء تفعيل مواعيد العمل بنجاح!');
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit_work_schedules');
        
        $schedule = WorkSchedule::findOrFail($id);
        $schedule->update(['is_active' => !$schedule->is_active]);

        $status = $schedule->is_active ? 'تفعيل' : 'إلغاء تفعيل';
        return redirect()->route('work-schedules.index')
            ->with('success', "تم {$status} مواعيد العمل بنجاح!");
    }
}
