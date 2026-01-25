<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'interview_id',
        'note',
        'status',
        'created_by',
    ];

    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
