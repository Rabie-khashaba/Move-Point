<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafeTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'safe_id',
        'user_id',
        'type',
        'amount',
        'reference_type',
        'reference_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function safe()
    {
        return $this->belongsTo(Safe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // If this transaction references an Expense, we can resolve it by type/id
    public function expense()
    {
        return $this->belongsTo(Expense::class, 'reference_id');
    }

    public function expenseType()
    {
        return $this->hasOneThrough(
            ExpenseType::class,
            Expense::class,
            'id',                 // Expense.id
            'id',                 // ExpenseType.id
            'reference_id',       // SafeTransaction.reference_id
            'expense_type_id'     // Expense.expense_type_id
        );
    }
}


