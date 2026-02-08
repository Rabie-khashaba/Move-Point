<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaitingRepresentativeFollowup extends Model
{
    protected $fillable = [
        'waiting_representative_id',
        'status',
        'follow_up_date',
        'note',
        'created_by',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function waitingRepresentative()
    {
        return $this->belongsTo(WaitingRepresentative::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
