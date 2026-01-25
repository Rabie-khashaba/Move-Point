<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'measurement_id',
        'visit_id',
        'notes',
        'employee_id',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relationships
     */

    // Relationship with Lead (Client)
    public function client()
    {
        return $this->belongsTo(Lead::class, 'client_id');
    }

    // Relationship with Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scopes
     */

    // Filter by client
    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // Filter by employee
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // Filter by date range
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Get recent notes
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('date', 'desc')->limit($limit);
    }
}
