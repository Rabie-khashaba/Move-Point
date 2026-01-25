<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaitingRepresentative extends Model
{
    protected $fillable = [
        'representative_id',
        'date',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'status' => 'integer',
    ];

    // العلاقة مع المندوب
    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }
}
