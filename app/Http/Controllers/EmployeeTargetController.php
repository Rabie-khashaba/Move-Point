<?php

namespace App\Http\Controllers;

use App\Models\EmployeeTarget;
use App\Models\Employee;
use App\Models\AdvanceRequest;
use App\Models\EmployeeAdjustment;
use App\Models\Representative;
use App\Models\RepresentativeTarget;
use App\Models\Lead;
use App\Models\LeadTarget;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Exports\EmployeeTargetsExport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeTargetController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view_employee_targets');

        $currentYear = request('year', now()->year);
        $currentMonth = request('month', now()->month);


        // Get existing targets for this month/year
        $targets = \App\Models\EmployeeTarget::with('employee')
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->when(request('search'), function ($query) {
                $search = request('search');
                $query->whereHas('employee', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                });
            })
            ->when(request('department_id'), function ($query) {
                $departmentId = request('department_id');
                $query->whereHas('employee', function ($subQuery) use ($departmentId) {
                    $subQuery->where('department_id', $departmentId);
                });
            })
            ->get();







        // 2️⃣ All adjustments for this month


        //dd($targets);




        // Get only sales department employees to show those without targets
        $allEmployees = Employee::active()
            // Only sales department employees (ID 7)
            ->when(request('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(request('department_id'), function ($query, $departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->with('department')
            ->get();


        // Create placeholder targets for employees who don't have one for this month
        // These will be temporary objects that don't exist in the database yet
        foreach ($allEmployees as $employee) {
            $existingTarget = $targets->where('employee_id', $employee->id)->first();
            if (!$existingTarget) {
                // Create a temporary target object (not saved to database)
                $tempTarget = new EmployeeTarget([
                    'employee_id' => $employee->id,
                    'year' => $currentYear,
                    'month' => $currentMonth,
                    'target_follow_ups' => 0,
                    'achieved_follow_ups' => 0,
                    'converted_leads_count' => 0,
                    'converted_leads_amount' => 0,
                    'bonus_amount' => 0,
                    'notes' => ''
                ]);
                $tempTarget->employee = $employee;
                $tempTarget->id = null; // Ensure it's not treated as existing
                $targets->push($tempTarget);
            }
        }

        $departments = \App\Models\Department::all();

        // 2️⃣ All adjustments for this month
        $adjustmentsList = EmployeeAdjustment::where('year', $currentYear)
            ->where('month', $currentMonth)
            ->get()
            ->groupBy('employee_id');

        // 3️⃣ Attach bonus & deduction to each target (including temporary ones)
        foreach ($targets as $target) {
            $employee = $target->employee;
            $departmentName = $employee->department->name ?? '';
            $activeCount = 0;
            $targetBonusAmount = 0;

            // 🔹 Calculate Target Bonus based on department
            if ($departmentName === 'المبيعات - Sales') {
                $activeCount = Representative::where('employee_id', $employee->user_id ?? 0)
                    ->whereYear('converted_to_active_date', $target->year)
                    ->whereMonth('converted_to_active_date', $target->month)
                    ->where('status', 1)
                    ->count();

                $repTargets = RepresentativeTarget::where('year', $target->year)
                    ->where('month', $target->month)
                    ->orderBy('representatives_count', 'asc')
                    ->get();

                foreach ($repTargets as $repTarget) {
                    if ($activeCount >= $repTarget->representatives_count) {
                        $targetBonusAmount = max($targetBonusAmount, $repTarget->bonus_amount);
                    }
                }
            } elseif ($departmentName === 'التسويق - Marketing') {
                $activeCount = Lead::where('moderator_id', $employee->user_id ?? 0)
                    ->whereYear('created_at', $target->year)
                    ->whereMonth('created_at', $target->month)
                    ->count();

                $leadTargets = LeadTarget::where('year', $target->year)
                    ->where('month', $target->month)
                    ->orderBy('leads_count', 'asc')
                    ->get();

                foreach ($leadTargets as $leadTarget) {
                    if ($activeCount >= $leadTarget->leads_count) {
                        $targetBonusAmount = max($targetBonusAmount, $leadTarget->bonus_amount);
                    }
                }
            }

            // 🔹 Calculate Advance Deductions
            $targetDate = Carbon::createFromDate($target->year, $target->month, 1);
            $advances = AdvanceRequest::where('employee_id', $employee->id)->get();
            $totalAdvanceDeduction = 0;

            foreach ($advances as $advance) {
                $startDate = Carbon::parse($advance->created_at);
                $months = (int) $advance->installment_months;
                $endDate = $startDate->copy()->addMonths($months);

                if ($targetDate->between($startDate->copy()->startOfMonth(), $endDate->copy()->endOfMonth())) {
                    $totalAdvanceDeduction += (float) $advance->monthly_installment;
                }
            }

            // 🔹 Calculate Manual Adjustments (Bonus/Deduction)
            $employeeAdjustments = $adjustmentsList[$employee->id] ?? collect();
            $manualBonus = $employeeAdjustments->where('type', 'bonus')->sum('amount');
            $manualDeduction = $employeeAdjustments->where('type', 'deduction')->sum('amount');

            // 🔹 Set properties on target object for use in view
            $target->calculated_active_count = $activeCount;
            $target->calculated_target_bonus = $targetBonusAmount;
            $target->calculated_advance_deduction = $totalAdvanceDeduction;
            $target->calculated_manual_bonus = $manualBonus;
            $target->calculated_manual_deduction = $manualDeduction;

            // 🔹 Calculate Final Salary
            // Note: target_bonus is separate from manual_bonus? 
            // In the view, the user displayed both.
            // final_salary = base_salary + target_bonus + manual_bonus - total_deduction - advance_deduction
            $baseSalary = $employee->salary ?? 0;

            $target->final_salary = $baseSalary + $targetBonusAmount + $manualBonus - $manualDeduction - $totalAdvanceDeduction;
        }

        // Debug logging
        \Log::info('Employee targets index loaded', [
            'current_year' => $currentYear,
            'current_month' => $currentMonth,
            'targets_count' => $targets->count(),
            'targets_with_id' => $targets->where('id', '!=', null)->count(),
            'targets_without_id' => $targets->where('id', null)->count(),
            'sample_targets' => $targets->take(3)->map(function ($target) {
                return [
                    'id' => $target->id,
                    'employee_name' => $target->employee->name ?? 'N/A',
                    'target_follow_ups' => $target->target_follow_ups,
                    'achieved_follow_ups' => $target->achieved_follow_ups,
                    'is_from_db' => $target->id !== null
                ];
            })->toArray()
        ]);


        /* $employees = Employee::withCount([
            'representative as active_representatives_count' => function ($query) {
                $query->where('status', 1);
            }
        ])->get();


        dd($employees); */





        return view('employee-targets.index', compact('targets', 'departments', 'currentYear', 'currentMonth', 'allEmployees'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_employee_targets');

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if target already exists for this employee in this month/year
        $existingTarget = EmployeeTarget::where('employee_id', $validated['employee_id'])
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->first();

        if ($existingTarget) {
            return back()->with('error', 'يوجد هدف بالفعل لهذا الموظف في هذا الشهر!');
        }

        // Create new target
        $target = EmployeeTarget::create([
            'employee_id' => $validated['employee_id'],
            'year' => $validated['year'],
            'month' => $validated['month'],
            'target_follow_ups' => $validated['target_follow_ups'],
            'achieved_follow_ups' => 0,
            'converted_leads_count' => $validated['converted_leads_count'],
            'converted_leads_amount' => $validated['converted_leads_amount'],
            'bonus_amount' => 0,
            'notes' => $validated['notes']
        ]);

        // Update achieved follow-ups and calculate bonus
        $target->updateAchievedFollowUps();
        $target->saveWithBonus();

        return back()->with('success', 'تم حفظ الهدف وإضافة المكافأة للراتب بنجاح!');
    }

    public function show($id)
    {
        $this->authorize('view_employee_targets');

        $target = EmployeeTarget::with('employee.department')->findOrFail($id);

        return view('employee-targets.show', compact('target'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('edit_employee_targets');

        $target = EmployeeTarget::findOrFail($id);

        // Prevent editing locked targets
        if ($target->isLocked()) {
            // Allow editing but log the change
            \Log::info("Editing locked target for employee {$target->employee->name}");
        }

        $validated = $request->validate([
            'target_follow_ups' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        $oldTarget = $target->target_follow_ups;
        $oldNotes = $target->notes;

        // Update the target
        $target->update($validated);

        // Refresh the model to get updated values
        $target->refresh();

        // Update achieved follow-ups based on actual data
        $target->updateAchievedFollowUps();

        $message = 'تم تحديث الهدف بنجاح!';
        $changes = [];

        if ($oldTarget != $validated['target_follow_ups']) {
            $changes[] = 'الهدف';
        }

        if ($oldNotes != ($validated['notes'] ?? '')) {
            $changes[] = 'الملاحظات';
        }

        if (!empty($changes)) {
            $message .= ' تم تحديث: ' . implode(', ', $changes) . '.';
        }

        $message .= ' تم تحديث المحقق تلقائياً.';

        return back()->with('success', $message);
    }

    public function bulkUpdate(Request $request)
    {
        $this->authorize('edit_employee_targets');

        // Add debugging
        \Log::info('Bulk update request received', [
            'targets_count' => count($request->input('targets', [])),
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'targets' => 'required|array',
            'targets.*.target_follow_ups' => 'required|integer|min:0'
        ]);

        $updatedCount = 0;
        $achievedUpdatedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($validated['targets'] as $index => $targetData) {
                \Log::info("Processing target {$index}", $targetData);

                // Handle both existing targets (with ID) and new targets (without ID)
                if (!empty($targetData['id'])) {
                    // Existing target - check if it can be edited
                    $target = EmployeeTarget::find($targetData['id']);
                    if ($target) {
                        \Log::info("Found existing target", [
                            'target_id' => $target->id,
                            'employee' => $target->employee->name,
                            'current_value' => $target->target_follow_ups,
                            'new_value' => $targetData['target_follow_ups'],
                            'is_locked' => $target->isLocked()
                        ]);

                        if ($target->isLocked()) {
                            // Allow editing locked targets but log the change
                            \Log::info("Editing locked target for employee {$target->employee->name}");
                        }

                        $targetChanged = false;
                        $achievedChanged = false;

                        // Check if any values have actually changed
                        $hasChanges = false;
                        $updateData = [];

                        if ($target->target_follow_ups != $targetData['target_follow_ups']) {
                            $updateData['target_follow_ups'] = $targetData['target_follow_ups'];
                            $hasChanges = true;
                        }

                        if ($target->converted_leads_count != ($targetData['converted_leads_count'] ?? $target->converted_leads_count)) {
                            $updateData['converted_leads_count'] = $targetData['converted_leads_count'] ?? 0;
                            $hasChanges = true;
                        }

                        if ($target->converted_leads_amount != ($targetData['converted_leads_amount'] ?? $target->converted_leads_amount)) {
                            $updateData['converted_leads_amount'] = $targetData['converted_leads_amount'] ?? 0;
                            $hasChanges = true;
                        }

                        if (isset($targetData['notes']) && $target->notes != $targetData['notes']) {
                            $updateData['notes'] = $targetData['notes'];
                            $hasChanges = true;
                        }

                        if ($hasChanges) {
                            $target->update($updateData);
                            // Refresh the model to get updated values
                            $target->refresh();
                            // Calculate and save bonus
                            $target->saveWithBonus();
                            $targetChanged = true;
                            $updatedCount++;

                            \Log::info("Target updated for employee {$target->employee->name}: {$oldValue} -> {$targetData['target_follow_ups']}");
                            \Log::info("After refresh, target value is: {$target->target_follow_ups}");
                        } else {
                            // Update notes if target didn't change but notes might have
                            if (isset($targetData['notes']) && $target->notes != $targetData['notes']) {
                                $target->update(['notes' => $targetData['notes']]);
                                $target->refresh();
                                $updatedCount++;
                            }
                        }

                        // Always update achieved follow-ups to ensure they're current
                        $oldAchieved = $target->achieved_follow_ups;
                        $target->updateAchievedFollowUps();
                        if ($oldAchieved != $target->achieved_follow_ups) {
                            $achievedChanged = true;
                            $achievedUpdatedCount++;
                        }
                    }
                } else {
                    // New target - create it only if target_follow_ups > 0
                    if (!empty($targetData['employee_id']) && isset($targetData['target_follow_ups']) && $targetData['target_follow_ups'] > 0) {
                        $currentYear = request('year', now()->year);
                        $currentMonth = request('month', now()->month);

                        // Check if target already exists
                        $existingTarget = EmployeeTarget::where('employee_id', $targetData['employee_id'])
                            ->where('year', $currentYear)
                            ->where('month', $currentMonth)
                            ->first();

                        if ($existingTarget) {
                            $errors[] = "يوجد هدف بالفعل للموظف في هذا الشهر";
                            continue;
                        }

                        $target = EmployeeTarget::create([
                            'employee_id' => $targetData['employee_id'],
                            'year' => $currentYear,
                            'month' => $currentMonth,
                            'target_follow_ups' => $targetData['target_follow_ups'],
                            'achieved_follow_ups' => 0,
                            'converted_leads_count' => $targetData['converted_leads_count'] ?? 0,
                            'converted_leads_amount' => $targetData['converted_leads_amount'] ?? 0,
                            'bonus_amount' => 0,
                            'notes' => $targetData['notes'] ?? ''
                        ]);

                        $target->updateAchievedFollowUps();
                        $updatedCount++;

                        \Log::info("New target created for employee", [
                            'employee_id' => $targetData['employee_id'],
                            'target_value' => $targetData['target_follow_ups']
                        ]);
                    }
                }
            }

            DB::commit();
            \Log::info("Bulk update completed successfully", [
                'updated_count' => $updatedCount,
                'achieved_updated_count' => $achievedUpdatedCount
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("Bulk update failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage());
        }

        $message = '';
        if ($updatedCount > 0) {
            $message .= "تم تحديث {$updatedCount} هدف بنجاح! ";
        }
        if ($achievedUpdatedCount > 0) {
            $message .= "تم تحديث المحقق لـ {$achievedUpdatedCount} هدف تلقائياً.";
        }

        if (empty($message)) {
            $message = 'لم يتم تغيير أي أهداف. المحقق محدث بالفعل.';
        }

        if (!empty($errors)) {
            $message .= ' أخطاء: ' . implode(', ', $errors);
        }

        return back()->with('success', $message);
    }

    public function generateMonthlyTargets()
    {
        $this->authorize('create_employee_targets');

        $employees = Employee::active()->where('department_id', 7)->get(); // Sales department only
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $createdCount = 0;
        $existingCount = 0;

        foreach ($employees as $employee) {
            $target = EmployeeTarget::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'year' => $currentYear,
                    'month' => $currentMonth
                ],
                [
                    'target_follow_ups' => 0,
                    'achieved_follow_ups' => 0
                ]
            );

            if ($target->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $existingCount++;
            }
        }

        $message = "تم إنشاء {$createdCount} هدف جديد";
        if ($existingCount > 0) {
            $message .= " وتم العثور على {$existingCount} هدف موجود مسبقاً";
        }
        $message .= " لشهر " . Carbon::createFromDate($currentYear, $currentMonth, 1)->format('F Y');

        return back()->with('success', $message);
    }

    public function refreshAchievedFollowUps(Request $request)
    {
        $this->authorize('edit_employee_targets');

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $targets = EmployeeTarget::where('year', $year)
            ->where('month', $month)
            ->get();

        $updatedCount = 0;
        foreach ($targets as $target) {
            $oldValue = $target->achieved_follow_ups;
            $target->updateAchievedFollowUps();
            if ($oldValue != $target->achieved_follow_ups) {
                $updatedCount++;
            }
        }

        return back()->with('success', "تم تحديث {$updatedCount} هدف من أصل {$targets->count()} هدف!");
    }

    // public function export(Request $request)
    // {
    //     $this->authorize('view_employee_targets');

    //     $year = $request->get('year', now()->year);
    //     $month = $request->get('month', now()->month);

    //     $targets = EmployeeTarget::with('employee.department')
    //         ->where('year', $year)
    //         ->where('month', $month)
    //         ->get();

    //     $filename = "employee_targets_{$year}_{$month}.csv";

    //     $headers = [
    //         'Content-Type' => 'text/csv; charset=UTF-8',
    //         'Content-Disposition' => "attachment; filename={$filename}",
    //     ];

    //     $callback = function () use ($targets) {
    //         $file = fopen('php://output', 'w');

    //         // Add BOM for Arabic text
    //         fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

    //         // Headers
    //         fputcsv($file, ['اسم الموظف', 'رقم الهاتف', 'القسم', 'عدد المتابعات المطلوبة', 'المحقق', 'النسبة المئوية', 'المتبقي', 'الملاحظات']);

    //         foreach ($targets as $target) {
    //             $achieved = $target->achieved_follow_ups_with_update ?? $target->achieved_follow_ups;
    //             $percentage = $target->target_follow_ups > 0 ? round(($achieved / $target->target_follow_ups) * 100, 2) : 0;
    //             $remaining = max(0, $target->target_follow_ups - $achieved);

    //             fputcsv($file, [
    //                 $target->employee->name ?? 'غير محدد',
    //                 $target->employee->phone ?? '',
    //                 $target->employee->department->name ?? 'غير محدد',
    //                 $target->target_follow_ups,
    //                 $achieved,
    //                 $percentage . '%',
    //                 $remaining,
    //                 $target->notes ?? ''
    //             ]);
    //         }

    //         fclose($file);
    //     };

    //     return response()->stream($callback, 200, $headers);
    // }


    public function export(Request $request)
{
    $this->authorize('view_employee_targets');

    $year = $request->get('year', now()->year);
    $month = $request->get('month', now()->month);

    $filename = "employee_targets_{$year}_{$month}.xlsx";

    return Excel::download(
        new EmployeeTargetsExport($year, $month),
        $filename
    );
}
    /**
     * Update converted leads data
     */
    public function updateConvertedLeads(Request $request, $id)
    {
        $this->authorize('edit_employee_targets');

        $validated = $request->validate([
            'converted_leads_count' => 'required|integer|min:0',
            'converted_leads_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        $target = EmployeeTarget::findOrFail($id);
        $target->converted_leads_count = $validated['converted_leads_count'];
        $target->converted_leads_amount = $validated['converted_leads_amount'];

        if ($validated['notes']) {
            $target->notes = $validated['notes'];
        }

        $target->saveWithBonus();

        return back()->with('success', 'تم تحديث بيانات التحويل وإعادة حساب المكافأة!');
    }


    public function showSalaryForm()
    {
        $this->authorize('edit_employee_targets');

        $currentYear = now()->year;
        $currentMonth = now()->month;

        return view('employee-targets.salary', compact('currentYear', 'currentMonth'));
    }


    // public function storeSalary(Request $request)
    // {
    //     $this->authorize('edit_employee_targets');

    //     $validated = $request->validate([
    //         'employee_id' => 'required|exists:employees,user_id',
    //         'year' => 'required|integer|min:2020|max:2030',
    //         'month' => 'required|integer|min:1|max:12',
    //         'deductions' => 'nullable|numeric',
    //         'bonus_amount' => 'nullable|numeric',
    //         'notes' => 'nullable|string|max:500',
    //     ]);

    //     // 🧭 نجيب الموظف باستخدام user_id
    //     $employee = \App\Models\Employee::where('user_id', $validated['employee_id'])->firstOrFail();

    //     // ✅ التحديث المشروط: نعدل فقط لو القيمة مش صفر
    //     if (!empty($validated['deductions']) && $validated['deductions'] != 0) {
    //         $employee->deductions = $validated['deductions'];
    //     }




    //     if (!empty($validated['bonus_amount']) && $validated['bonus_amount'] != 0) {
    //         $employee->bonus_amount = $validated['bonus_amount'];
    //     }

    //     // ✅ نسمح دايمًا بتعديل الملاحظات
    //     if (!empty($validated['notes'])) {
    //         $employee->notes = $validated['notes'];
    //     }

    //     // 🧮 حساب إجمالي المرتب
    //     $employee->total_salary =
    //         ($employee->salary ?? 0)
    //         + ($employee->bonus_amount ?? 0)
    //         - ($employee->deductions ?? 0);

    //     $employee->save();


    //     EmployeeAdjustment::create([
    //         'employee_id' => $employee->id,
    //         'year' => $validated['notes'],
    //         'month' =>$validated['notes'],
    //         'type' => '$validated['notes']',
    //         'amount' => $validated['bonus_amount'],
    //         'reason' => $validated['notes'],
    //     ]);

    //     return redirect()
    //         ->route('employee-targets.index')
    //         ->with('success', 'تم تعديل الراتب (خصم / مكافأة) بنجاح!');
    // }



    public function storeSalary(Request $request)
    {
        $this->authorize('edit_employee_targets');

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,user_id',
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'deductions' => 'nullable|numeric|min:0',
            'bonus_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // نجيب الموظف عن طريق user_id
        $employee = \App\Models\Employee::where('user_id', $validated['employee_id'])->firstOrFail();

        /** ✅ إضافة خصم (لو موجود) */
        if (!empty($validated['deductions']) && $validated['deductions'] > 0) {
            \App\Models\EmployeeAdjustment::create([
                'employee_id' => $employee->id,
                'year' => $validated['year'],
                'month' => $validated['month'],
                'type' => 'deduction',
                'amount' => $validated['deductions'],
                'reason' => $validated['notes'] ?? 'خصم يدوي',
            ]);
        }

        /** ✅ إضافة مكافأة (لو موجودة) */
        if (!empty($validated['bonus_amount']) && $validated['bonus_amount'] > 0) {
            \App\Models\EmployeeAdjustment::create([
                'employee_id' => $employee->id,
                'year' => $validated['year'],
                'month' => $validated['month'],
                'type' => 'bonus',
                'amount' => $validated['bonus_amount'],
                'reason' => $validated['notes'] ?? 'مكافأة يدوية',
            ]);
        }

        return redirect()
            ->route('employee-targets.index', [
                'year' => $validated['year'],
                'month' => $validated['month'],
            ])
            ->with('success', 'تمت إضافة الخصم / المكافأة بنجاح');
    }
}


