<?php

namespace App\Exports;

use App\Models\EmployeeTarget;
use App\Models\Employee;
use App\Models\EmployeeAdjustment;
use App\Models\AdvanceRequest;
use App\Models\Representative;
use App\Models\RepresentativeTarget;
use App\Models\Lead;
use App\Models\LeadTarget;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class EmployeeTargetsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
        // 1️⃣ Get existing targets for this month/year
        $targets = EmployeeTarget::with('employee.department')
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->get();

        // 2️⃣ Get all active employees to include those without targets
        $allEmployees = Employee::active()->with('department')->get();

        foreach ($allEmployees as $employee) {
            $existingTarget = $targets->where('employee_id', $employee->id)->first();
            if (!$existingTarget) {
                // Create a temporary target object
                $tempTarget = new EmployeeTarget([
                    'employee_id' => $employee->id,
                    'year' => $this->year,
                    'month' => $this->month,
                    'target_follow_ups' => 0,
                    'achieved_follow_ups' => 0,
                    'converted_leads_count' => 0,
                    'converted_leads_amount' => 0,
                    'bonus_amount' => 0,
                    'notes' => ''
                ]);
                $tempTarget->employee = $employee;
                $targets->push($tempTarget);
            }
        }

        // 3️⃣ Fetch all manual adjustments for this month
        $adjustmentsList = EmployeeAdjustment::where('year', $this->year)
            ->where('month', $this->month)
            ->get()
            ->groupBy('employee_id');

        // 4️⃣ Apply calculation logic to each target
        foreach ($targets as $target) {
            $employee = $target->employee;
            $departmentName = $employee->department->name ?? '';
            $activeCount = 0;
            $targetBonusAmount = 0;

            // 🔹 Calculate Target Bonus
            if ($departmentName === 'المبيعات - Sales') {
                $activeCount = Representative::where('employee_id', $employee->user_id ?? 0)
                    ->whereYear('converted_to_active_date', $this->year)
                    ->whereMonth('converted_to_active_date', $this->month)
                    ->where('status', 1)
                    ->count();

                $repTargets = RepresentativeTarget::where('year', $this->year)
                    ->where('month', $this->month)
                    ->orderBy('representatives_count', 'asc')
                    ->get();

                foreach ($repTargets as $repTarget) {
                    if ($activeCount >= $repTarget->representatives_count) {
                        $targetBonusAmount = max($targetBonusAmount, $repTarget->bonus_amount);
                    }
                }
            } elseif ($departmentName === 'التسويق - Marketing') {
                $activeCount = Lead::where('moderator_id', $employee->user_id ?? 0)
                    ->whereYear('created_at', $this->year)
                    ->whereMonth('created_at', $this->month)
                    ->count();

                $leadTargets = LeadTarget::where('year', $this->year)
                    ->where('month', $this->month)
                    ->orderBy('leads_count', 'asc')
                    ->get();

                foreach ($leadTargets as $leadTarget) {
                    if ($activeCount >= $leadTarget->leads_count) {
                        $targetBonusAmount = max($targetBonusAmount, $leadTarget->bonus_amount);
                    }
                }
            }

            // 🔹 Calculate Advance Deductions
            $targetDate = Carbon::createFromDate($this->year, $this->month, 1);
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

            // 🔹 Calculate Manual Adjustments
            $employeeAdjustments = $adjustmentsList[$employee->id] ?? collect();
            $manualBonus = $employeeAdjustments->where('type', 'bonus')->sum('amount');
            $manualDeduction = $employeeAdjustments->where('type', 'deduction')->sum('amount');

            // 🔹 Attach calculated values to the object
            $target->calculated_active_count = $activeCount;
            $target->calculated_target_bonus = $targetBonusAmount;
            $target->calculated_advance_deduction = $totalAdvanceDeduction;
            $target->calculated_manual_bonus = $manualBonus;
            $target->calculated_manual_deduction = $manualDeduction;
            $target->calculated_final_salary = ($employee->salary ?? 0) + $targetBonusAmount + $manualBonus - $manualDeduction - $totalAdvanceDeduction;
        }

        return $targets;
    }

    public function headings(): array
    {
        return [
            'اسم الموظف',
            'القسم',
            'المرتب الأساسي',
            'المحقق (مناديب/ليدز)',
            'مكافأة التارجت',
            'خصومات يدوية',
            'أقساط سلف',
            'مكافآت يدوية',
            'إجمالي المرتب',
        ];
    }

    public function map($target): array
    {
        $employee = $target->employee;

        return [
            $employee->name ?? 'غير محدد',
            $employee->department->name ?? 'غير محدد',
            number_format($employee->salary ?? 0, 2),
            $target->calculated_active_count,
            number_format($target->calculated_target_bonus, 2),
            number_format($target->calculated_manual_deduction, 2),
            number_format($target->calculated_advance_deduction, 2),
            number_format($target->calculated_manual_bonus, 2),
            number_format($target->calculated_final_salary, 2),
        ];
    }
}
