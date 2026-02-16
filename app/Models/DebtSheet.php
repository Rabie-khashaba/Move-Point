<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'star_id',
        'shortage',
        'credit_note',
        'advances',
        'status',
        'sheet_date',
    ];

    protected $casts = [
        'shortage' => 'decimal:2',
        'credit_note' => 'decimal:2',
        'advances' => 'decimal:2',
        'sheet_date' => 'date:Y-m-d',
    ];
}
