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
    ];

    protected $casts = [
        'shortage' => 'decimal:2',
        'credit_note' => 'decimal:2',
        'advances' => 'decimal:2',
    ];
}

