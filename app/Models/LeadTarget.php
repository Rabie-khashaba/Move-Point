<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lead;

class LeadTarget extends Model
{
    protected $fillable = [
        'leads_count',
        'bonus_amount',
        'year',
        'month',
        'notes'
    ];

    protected $casts = [
        'leads_count' => 'integer',
        'bonus_amount' => 'decimal:2'
    ];

    // Validation rules
    public static $rules = [
        'leads_count' => 'required|integer|min:0',
        'bonus_amount' => 'required|numeric|min:0',
        'year' => 'required|integer|min:2020|max:2030',
        'month' => 'required|integer|min:1|max:12',
        'notes' => 'nullable|string|max:500'
    ];


    // علاقة الموظف صاحب التارجت
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }



    public function getActualRepresentativesCountAttribute()
    {
        return Lead::whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month)
            ->count();
    }

    /**
     * Check if target is reached (actual count >= target count)
     */
    public function isTargetReached()
    {
        return $this->actual_representatives_count >= $this->representatives_count;
    }

    /**
     * Give bonus to employees who reached the target
     */
    public function giveBonusToQualifiedEmployees()
    {
        // Get all employees in sales department (department_id = 7)
        $salesEmployees = \App\Models\Employee::where('department_id', 6)
            ->where('is_active', true)
            ->with('user')
            ->get();

        foreach ($salesEmployees as $employee) {
            // Count representatives converted by this employee in this month
            $employeeRepresentativesCount = Lead::where('moderator_id', $employee->user_id)
                ->whereYear('created_at', $this->year)
                ->whereMonth('created_at', $this->month)
                ->count();

            // If employee converted >= target count, give them bonus
            if ($employeeRepresentativesCount >= $this->representatives_count) {
                $this->addProgressiveBonusToEmployeeSalary($employee, $employeeRepresentativesCount);
            }
        }
    }

    /**
     * Static method to process all targets and give bonuses to qualified employees
     */
    public static function processAllTargets($year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        // Get all targets for the specified month/year
        $targets = self::where('year', $year)
            ->where('month', $month)
            ->get();

        $processedCount = 0;

        foreach ($targets as $target) {
            $target->giveBonusToQualifiedEmployees();
            $processedCount++;
        }

        return $processedCount;
    }

    /**
     * Add bonus to specific employee's salary
     */
    private function addBonusToEmployeeSalary($employee)
    {
        // Find or create salary record for this month/year
        $salaryRecord = \App\Models\SalaryRecord::firstOrCreate([
            'employee_id' => $employee->id,
            'year' => $this->year,
            'month' => $this->month,
        ], [
            'base_salary' => $employee->salary,
            'net_salary' => $employee->salary,
            'is_paid' => false
        ]);

        // Check if bonus already given to avoid duplicate
        if (!isset($salaryRecord->representative_bonus_given) || !$salaryRecord->representative_bonus_given) {
            // Add bonus to net salary
            $salaryRecord->net_salary += $this->bonus_amount;
            $salaryRecord->bonus_amount = ($salaryRecord->bonus_amount ?? 0) + $this->bonus_amount;
            $salaryRecord->representative_bonus_given = true; // Mark as given
            $salaryRecord->save();
        }
    }

    /**
     * Add progressive bonus to employee salary (removes previous bonuses and adds highest one)
     */
    private function addProgressiveBonusToEmployeeSalary($employee, $actualRepresentativesCount)
    {
        // Find or create salary record for this month/year
        $salaryRecord = \App\Models\SalaryRecord::firstOrCreate([
            'employee_id' => $employee->id,
            'year' => $this->year,
            'month' => $this->month,
        ], [
            'base_salary' => $employee->salary,
            'net_salary' => $employee->salary,
            'is_paid' => false
        ]);

        // Get all targets that this employee qualifies for (sorted by representatives_count DESC)
        $qualifyingTargets = self::where('year', $this->year)
            ->where('month', $this->month)
            ->where('leads_count', '<=', $actualRepresentativesCount)
            ->orderBy('leads_count', 'desc')
            ->get();

        if ($qualifyingTargets->isNotEmpty()) {
            // Get the highest bonus amount from all qualifying targets
            $highestBonus = $qualifyingTargets->max('bonus_amount');

            // Remove any previous representative bonuses and add the highest one
            $previousRepresentativeBonus = $salaryRecord->representative_bonus_amount ?? 0;
            $salaryRecord->net_salary = $salaryRecord->net_salary - $previousRepresentativeBonus + $highestBonus;
            $salaryRecord->bonus_amount = ($salaryRecord->bonus_amount ?? 0) - $previousRepresentativeBonus + $highestBonus;
            $salaryRecord->representative_bonus_amount = $highestBonus;
            $salaryRecord->representative_bonus_given = true;
            $salaryRecord->save();
        }
    }

    /**
     * Save and process bonuses
     */
    public function saveWithBonusProcessing()
    {
        $this->save();
        $this->giveBonusToQualifiedEmployees();
    }

    /**
     * Scope for current month/year
     */
    public function scopeCurrentPeriod($query)
    {
        return $query->where('year', now()->year)
                    ->where('month', now()->month);
    }


}
