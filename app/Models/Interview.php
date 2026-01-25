<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    // Table name (if different from pluralized model name)
    protected $table = 'interviews';

    // The attributes that are mass assignable
    protected $fillable = [
        'lead_id',
        'message_id',
        'supervisor_id',
        'date_interview',
        'note',
        'status',
        'assigned_to',
    ];

    // The attributes that should be cast to native types
    protected $casts = [
        'date_interview' => 'datetime', // Ensuring the date is a DateTime object
    ];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function notes()
    {
        return $this->hasMany(InterviewNote::class)->latest();
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }

}
