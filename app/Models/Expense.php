<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_type_id',
        'safe_id',
        'user_id',
        'expense_scope',
        'amount',
        'notes',
        'attachment_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function type()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    public function safe()
    {
        return $this->belongsTo(Safe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


