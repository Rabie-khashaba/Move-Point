<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Representative;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
        'contact',
        'national_id',
        'salary',
        'start_date',
        'department_id',
        'attachments',
        'is_active',
        'shift',
        'days_off',
        'bonus_amount',
        'deductions',
        'total_salary',
        'notes',
        'whatsapp_phone',
        'company_id',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'bonus_amount' => 'decimal:2',
        'deductions' => 'decimal:2',
        'total_salary' => 'decimal:2',

    ];

    protected $attributes = [
        'is_active' => true,

    ];


    public function device()
    {
        return $this->belongsTo(Device::class, 'whatsapp_phone', 'phone_number');
    }

    public function adjustments()
    {
        return $this->hasMany(EmployeeAdjustment::class);
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function target()
    {
        return $this->hasOne(\App\Models\EmployeeTarget::class, 'employee_id', 'id');
    }



    public function leads()
    {
        return $this->hasMany(Lead::class, 'moderator_id', 'user_id');
    }



    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function roles()
    {
        return $this->hasManyThrough(
            Role::class,
            UserRole::class,
            'user_id',
            'id',
            'user_id',
            'role_id'
        );
    }

    public function representative()
    {
        return $this->hasMany(Representative::class, 'employee_id', 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function targets()
    {
        return $this->hasMany(EmployeeTarget::class);
    }

    public function salaryRecords()
    {
        return $this->hasMany(SalaryRecord::class);
    }

    public function resignationRequests()
    {
        return $this->hasMany(ResignationRequest::class);
    }

    public function getCurrentWorkScheduleAttribute()
    {
        return $this->workSchedules()->active()->latest('effective_date')->first();
    }

    // Scope for sales department (ID 7)
    public function scopeSalesDepartment($query)
    {
        return $query->where('department_id', 7);
    }

    // Accessors
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/app/public/' . $this->profile_photo);
        }
        return asset('assets/images/default-avatar.png');
    }

    public function getFullNameAttribute()
    {
        return $this->name ?? 'Employee ' . $this->id;
    }

    public function getEmploymentStatusAttribute()
    {
        if ($this->termination_date) {
            return 'Terminated';
        }
        return $this->is_active ? 'Active' : 'Inactive';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeHighPerformers($query)
    {
        return $query->where('performance_rating', '>=', 4.0);
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'employee_id');
    }
    public function debits()
    {
        return $this->hasMany(Debt::class);
    }
}
