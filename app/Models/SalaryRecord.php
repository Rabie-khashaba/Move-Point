<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'representative_id',
        'supervisor_id',
        'user_type',
        'user_code',
        'year',
        'month',
        'base_salary',
        'advances',
        'deductions',
        'lost_orders_penalty',
        'delivery_penalty',
        'commissions',
        'cashback',
        'net_salary',
        'notes',
        'is_paid',
        'paid_at'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'advances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'lost_orders_penalty' => 'decimal:2',
        'delivery_penalty' => 'decimal:2',
        'commissions' => 'decimal:2',
        'cashback' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime'
    ];

    
    public function storeSalary(Request $request)
    {
        $employeeId        = $request->employee_id;
        $totalAdvance      = $request->advances;           // إجمالي السلفة
        $installmentMonths = $request->installment_months; // عدد الأشهر

        // قيمة القسط الشهري
        $monthlyAdvance = $installmentMonths > 0 
            ? round($totalAdvance / $installmentMonths, 2)
            : $totalAdvance;

        // الشهر الحالي كبداية
        $startDate = Carbon::now();

        for ($i = 0; $i < $installmentMonths; $i++) {
            $month = $startDate->copy()->addMonths($i);

            Salary::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                ],
                [
                    'advances'    => $monthlyAdvance,
                ]
            );
        }

        return back()->with('success', 'Salary & installments saved successfully.');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
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

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    public function getTotalDeductionsAttribute()
    {
        return $this->advances + $this->deductions + $this->lost_orders_penalty + $this->delivery_penalty;
    }

    public function getTotalAdditionsAttribute()
    {
        return $this->commissions + $this->cashback;
    }

    public function getMonthNameAttribute()
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];
        return $months[$this->month] ?? $this->month;
    }

    // Helper methods to get user information
    public function getUserAttribute()
    {
        switch ($this->user_type) {
            case 'employee':
                return $this->employee;
            case 'representative':
                return $this->representative;
            case 'supervisor':
                return $this->supervisor;
            default:
                return null;
        }
    }

    public function getUserNameAttribute()
    {
        $user = $this->user;
        return $user ? $user->name : 'غير محدد';
    }

    public function getUserPhoneAttribute()
    {
        $user = $this->user;
        return $user ? $user->phone : 'غير محدد';
    }

    public function getUserCodeAttribute()
    {
        $user = $this->user;
        if ($this->user_type === 'representative' && $this->representative_id) {
            return $this->representative->code ?? 'غير محدد';
        }
        if ($this->user_type === 'supervisor' && $this->supervisor_id) {
            return $this->supervisor->code ?? 'غير محدد';
        }
        if ($this->user_type === 'employee' && $this->employee_id) {
            return $this->employee->code ?? 'غير محدد';
        }
        return $this->user_code ?? 'غير محدد';
    }

   public function representativeCodeAttribute()
   {
    return $this->belongsTo(Representative::class, 'code', 'code');
   }

    public function getUserDepartmentAttribute()
    {
        $user = $this->user;
        if ($this->user_type === 'employee' && $user && $user->department) {
            return $user->department->name;
        }
        return 'غير محدد';
    }
}
