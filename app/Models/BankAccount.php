<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'bank_id',
        'status',
        'account_owner_name',
        'account_number',
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
