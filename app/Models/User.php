<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles;

    protected $fillable = [
        'type',
        'name',
        'phone',
        'password',
        'last_login_at',
        'forget_password',
        'device_tokens',
        'notifications_enabled',
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'forget_password' => 'boolean',
        'device_tokens' => 'array',
        'notifications_enabled' => 'boolean',
    ];

    protected $attributes = [
        'type' => 'employee'
    ];

    // Relationships
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }


    public function supervisor()
    {
        return $this->hasOne(Supervisor::class);
    }

    public function representative()
    {
        return $this->hasOne(Representative::class);
    }


    public function leads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function passwordResetRequests()
    {
        return $this->hasMany(PasswordResetRequest::class);
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    /**
     * Unified leave requests relation for all user types.
     * - employee: via Employee -> LeaveRequest (employee_id)
     * - representative: via Representative -> LeaveRequest (representative_id)
     * - supervisor: via Supervisor -> LeaveRequest (supervisor_id)
     */
    public function leaveRequests()
    {
        if ($this->type === 'employee') {
            return $this->hasManyThrough(
                LeaveRequest::class,
                Employee::class,
                'user_id',            // Employee.user_id
                'employee_id',        // LeaveRequest.employee_id
                'id',                 // User.id
                'id'                  // Employee.id
            );
        }
        if ($this->type === 'representative') {
            return $this->hasManyThrough(
                LeaveRequest::class,
                Representative::class,
                'user_id',            // Representative.user_id
                'representative_id',  // LeaveRequest.representative_id
                'id',                 // User.id
                'id'                  // Representative.id
            );
        }
        if ($this->type === 'supervisor') {
            return $this->hasManyThrough(
                LeaveRequest::class,
                Supervisor::class,
                'user_id',            // Supervisor.user_id
                'supervisor_id',      // LeaveRequest.supervisor_id
                'id',                 // User.id
                'id'                  // Supervisor.id
            );
        }

        // Default empty relation for other types
        return $this->hasMany(LeaveRequest::class, 'employee_id')->whereRaw('1=0');
    }

    // Accessors
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/app/public/' . $this->avatar);
        }
        return asset('assets/images/default-avatar.png');
    }

    public function getEmployeeNameAttribute()
    {
        return $this->employee ? $this->employee->name : 'No Employee Name';
    }

    public function getFullTypeAttribute()
    {
        $types = [
            'admin' => 'Administrator',
            'employee' => 'Employee',
            'supervisor' => 'Supervisor',
            'representative' => 'Representative'
        ];

        return $types[$this->type] ?? 'Unknown';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('phone_verified_at');
    }
}
