<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingSessionPostpone extends Model
{
    protected $fillable = [
        'training_session_id',
        'follow_up_date',
        'reason',
        'note',
        'created_by',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function trainingSession()
    {
        return $this->belongsTo(TrainingSession::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
