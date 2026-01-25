<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'supervisor_id',
        'representative_id',
        'loan_amount',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }


    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }



}


