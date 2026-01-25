<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'employee_id',
        'supervisor_id',
        'amount',
        'orders_count',
        'status',
        'receipt_image',
        'delivered_at',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'delivered_at' => 'datetime'
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeNotDelivered($query)
    {
        return $query->where('status', 'not_delivered');
    }

    public function scopeByRepresentative($query, $representativeId)
    {
        return $query->where('representative_id', $representativeId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function getReceiptImageUrlAttribute()
    {
        if ($this->receipt_image) {
            return asset('storage/app/public/' . $this->receipt_image);
        }
        return null;
    }

    public function getStatusTextAttribute()
    {
        return [
            'pending' => 'في الانتظار',
            'delivered' => 'تم التسليم',
            'not_delivered' => 'لم يسلم'
        ][$this->status] ?? $this->status;
    }

    // Check deposit source
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

    // Get the depositor name regardless of type
    public function getDepositorNameAttribute()
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

    // Get the depositor phone regardless of type
    public function getDepositorPhoneAttribute()
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
}
