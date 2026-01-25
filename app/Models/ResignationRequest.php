<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResignationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'representative_id',
        'supervisor_id',
        'resignation_date',
        'last_working_day',
        'reason',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'source',
        'active_by',
    ];

    protected $casts = [
        'resignation_date' => 'date',
        'last_working_day' => 'date',
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

    public function activeBy()
    {
        return $this->belongsTo(User::class, 'active_by');
    }

    public function notes()
    {
        return $this->hasMany(ResignationRequestNote::class)->orderBy('created_at', 'desc');
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

    public function getStatusTextAttribute()
    {
        return [
            'pending' => 'في الانتظار',
            'approved' => 'تمت الموافقة',
            'rejected' => 'مرفوض',
            'unresign' => 'تم الرجوع للعمل'
        ][$this->status] ?? $this->status;
    }

    public function getNoticePeriodAttribute()
    {
        return $this->resignation_date->diffInDays($this->last_working_day);
    }

    // Check request source
    public function isFromEmployee()
    {
        return !is_null($this->employee_id);
    }

    public function isFromRepresentative()
    {
        return !is_null($this->representative_id);
    }

    public function isFromSupervisor()
    {
        return !is_null($this->supervisor_id);
    }

    public function isFromMobileApp()
    {
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
