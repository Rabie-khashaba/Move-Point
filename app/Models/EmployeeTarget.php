<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LeadFollowup;
use Illuminate\Validation\Rule;

class EmployeeTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'target_follow_ups',
        'achieved_follow_ups',
        'converted_leads_count',
        'converted_leads_amount',
        'bonus_amount',
        'deductions',
        'total_salary',
        'notes'
    ];

    protected $casts = [
        'target_follow_ups' => 'integer',
        'achieved_follow_ups' => 'integer',
        'converted_leads_count' => 'integer',
        'converted_leads_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'deductions' => 'decimal:2',
        'total_salary' => 'decimal:2',
    ];

    // Validation rules
    public static $rules = [
        'employee_id' => 'required|exists:employees,user_id',
        'year' => 'required|integer|min:2020|max:2030',
        'month' => 'required|integer|min:1|max:12',
        'target_follow_ups' => 'required|integer|min:0',
        'converted_leads_count' => 'required|integer|min:0',
        'converted_leads_amount' => 'required|numeric|min:0',
        'notes' => 'nullable|string|max:500'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'user_id');
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByMonth($query, $month)
    {
        return $query->where('month', $month);
    }

    public function scopeByYearMonth($query, $year, $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    public function scopeBySalesDepartment($query)
    {
        return $query->whereHas('employee', function ($q) {
            $q->where('department_id', 7); // Sales department
        });
    }

    public function getAchievementPercentageAttribute()
    {
        if ($this->target_follow_ups > 0) {
            return round(($this->achieved_follow_ups / $this->target_follow_ups) * 100, 2);
        }
        return 0;
    }

    public function getRemainingFollowUpsAttribute()
    {
        return max(0, $this->target_follow_ups - $this->achieved_follow_ups);
    }

    /**
     * Get the actual number of follow-ups made by the employee in this month
     */
    public function getActualFollowUpsAttribute()
    {
        // Get the employee's user account
        $employee = $this->employee;
        if (!$employee) {
            return 0;
        }

        // Get the user associated with this employee
        $user = $employee->user;
        if (!$user) {
            return 0;
        }

        // Count follow-ups made by this user in the target month and year
        return LeadFollowup::where('user_id', $user->id)
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month)
            ->count();
    }

    /**
     * Get the actual number of representatives converted by the employee in this month
     */
    public function getActualRepresentativesConvertedAttribute()
    {
        // Get the employee's user account
        $employee = $this->employee;
        if (!$employee) {
            return 0;
        }

        // Get the user associated with this employee
        $user = $employee->user;
        if (!$user) {
            return 0;
        }

        // Count representatives converted by this user in the target month and year
        return \App\Models\Representative::where('created_by', $user->id)
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month)
            ->count();
    }


    /**
     * Update the achieved_follow_ups based on actual follow-ups
     */
    public function updateAchievedFollowUps()
    {
        $actualFollowUps = $this->actual_follow_ups;
        if ($this->achieved_follow_ups != $actualFollowUps) {
            $this->achieved_follow_ups = $actualFollowUps;
            $this->saveQuietly(); // Save without triggering events
        }
    }


    /**
     * Get the achieved follow-ups with automatic update
     */
    public function getAchievedFollowUpsWithUpdateAttribute()
    {
        // Only update if we haven't already calculated it in this request
        if (!isset($this->attributes['_achieved_updated'])) {
            $this->updateAchievedFollowUps();
            $this->attributes['_achieved_updated'] = true;
        }
        return $this->achieved_follow_ups;
    }

    /**
     * Calculate bonus based on converted leads
     */
    public function calculateBonus()
    {
        // Simple calculation: 10% of amount for each converted lead
        $this->bonus_amount = ($this->converted_leads_amount * 0.10) * $this->converted_leads_count;
        return $this->bonus_amount;
    }

    /**
     * Add bonus to employee salary
     */
    public function addBonusToSalary()
    {
        $employee = $this->employee;
        if (!$employee) {
            return;
        }

        // Find or create salary record for this month/year
        $salaryRecord = \App\Models\SalaryRecord::firstOrCreate([
            'employee_id' => $employee->id,
            'year' => $this->year,
            'month' => $this->month,
        ], [
            'basic_salary' => $employee->salary,
            'total_salary' => $employee->salary,
            'status' => 'pending'
        ]);

        // Add bonus to total salary
        $salaryRecord->total_salary += $this->bonus_amount;
        $salaryRecord->bonus_amount = ($salaryRecord->bonus_amount ?? 0) + $this->bonus_amount;
        $salaryRecord->save();
    }

    /**
     * Save with bonus calculation and add to salary
     */
    public function saveWithBonus()
    {
        $this->calculateBonus();
        $this->save();
        $this->addBonusToSalary();
    }


    /**
     * Check if target can be edited (only if it's a new target with 0 follow-ups)
     */
    public function canBeEdited()
    {
        return $this->target_follow_ups == 0 && $this->achieved_follow_ups == 0;
    }

    /**
     * Check if target is locked (has been set and cannot be changed)
     */
    public function isLocked()
    {
        return $this->target_follow_ups > 0;
    }

    /**
     * Get the current target value with proper formatting
     */
    public function getFormattedTargetAttribute()
    {
        return $this->target_follow_ups;
    }

    /**
     * Check if target was recently updated
     */
    public function wasRecentlyUpdated()
    {
        return $this->wasChanged('target_follow_ups');
    }

    /**
     * Boot method to automatically update achieved follow-ups when target is accessed
     */
    protected static function boot()
    {
        parent::boot();

        // Remove automatic update to avoid conflicts with form submissions
        // Updates will be handled explicitly in controllers
    }
}
