<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadFollowup extends Model
{
    protected $fillable = [
        'lead_id', 'user_id', 'notes', 'type', 'next_follow_up', 'outcome','reason_id'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class,'lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function reason()
    {
        return $this->belongsTo(Reason::class,'reason_id');
    }

}
