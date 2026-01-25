<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Safe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(SafeTransaction::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}


