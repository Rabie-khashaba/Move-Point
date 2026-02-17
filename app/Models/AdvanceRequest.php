<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'employee_id',
        'supervisor_id',
        'amount',
        'installment_months',
        'monthly_installment',
        'reason',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'receipt_image'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
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

    public function scopeByRepresentative($query, $representativeId)
    {
        return $query->where('representative_id', $representativeId);
    }

    public function getIsInstallmentAttribute()
    {
        return !is_null($this->installment_months) && $this->installment_months > 1;
    }

    public function getRemainingAmountAttribute()
    {
        // This would need to be calculated based on actual payments made
        return $this->amount;
    }

    // Check request source
    public function isFromRepresentative()
    {
        return !is_null($this->representative_id);
    }

    public function isFromEmployee()
    {
        return !is_null($this->employee_id);
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
        if ($this->representative) {
            return $this->representative->name;
        } elseif ($this->employee) {
            return $this->employee->name;
        } elseif ($this->supervisor) {
            return $this->supervisor->name;
        }
        return 'غير محدد';
    }

    // Get the requester phone regardless of type
    public function getRequesterPhoneAttribute()
    {
        if ($this->representative) {
            return $this->representative->phone;
        } elseif ($this->employee) {
            return $this->employee->phone;
        } elseif ($this->supervisor) {
            return $this->supervisor->phone;
        }
        return 'غير محدد';
    }

    /**
     * Get the receipt image URL
     */
    public function getReceiptImageUrlAttribute()
    {
        if ($this->receipt_image) {
            return asset('storage/app/public/' . $this->receipt_image);
        }
        return null;
    }

    /**
     * Check if receipt image exists
     */
    public function getHasReceiptAttribute()
    {
        return !is_null($this->receipt_image);
    }
}