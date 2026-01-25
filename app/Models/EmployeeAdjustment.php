<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAdjustment extends Model
{

    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'type',
        'amount',
        'reason',
    ];
}
