<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'shift',
        'start_time',
        'end_time',
        'work_days',
        'is_active',
        'effective_date',
        'end_date',
        'notes'
    ];

    protected $casts = [
        'work_days' => 'array',
        'is_active' => 'boolean',
        'effective_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }
}
