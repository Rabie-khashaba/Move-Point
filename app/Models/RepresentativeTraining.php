<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepresentativeTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }
}


