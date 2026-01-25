<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'representative_id',
        'supervisor_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime'
    ];

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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    // Check if request is from employee (dashboard) or representative/supervisor (mobile app)
    public function isFromEmployee()
    {
        // If employee_id exists and is not null, it's from dashboard
        return !is_null($this->employee_id);
    }

    public function isFromRepresentative()
    {
        // If representative_id exists and is not null, it's from representative
        return !is_null($this->representative_id);
    }

    public function isFromSupervisor()
    {
        // If supervisor_id exists and is not null, it's from supervisor
        return !is_null($this->supervisor_id);
    }

    public function isFromMobileApp()
    {
        // If representative_id or supervisor_id exists, it's from mobile app
        return !is_null($this->representative_id) || !is_null($this->supervisor_id);
    }

    // Get the requester name regardless of type
    public function getRequesterNameAttribute()
    {
        if ($this->employee) {
            return $this->employee->name;
        } elseif ($this->representative) {
            return $this->representative->name;
        } elseif ($this->supervisor) {
            return $this->supervisor->name;
        }
        return 'غير محدد';
    }

    // Get the requester phone regardless of type
    public function getRequesterPhoneAttribute()
    {
        if ($this->employee) {
            return $this->employee->phone;
        } elseif ($this->representative) {
            return $this->representative->phone;
        } elseif ($this->supervisor) {
            return $this->supervisor->phone;
        }
        return 'غير محدد';
    }
}
