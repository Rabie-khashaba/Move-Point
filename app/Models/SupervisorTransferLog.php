<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorTransferLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'old_supervisor_id',
        'new_supervisor_id',
        'transferred_by',
        'reason'
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function oldSupervisor()
    {
        return $this->belongsTo(Supervisor::class, 'old_supervisor_id');
    }

    public function newSupervisor()
    {
        return $this->belongsTo(Supervisor::class, 'new_supervisor_id');
    }

    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }
}
