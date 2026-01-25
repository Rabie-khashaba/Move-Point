<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkStart extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'message_id',
        'governorate_id',
        'location_id',
        'date',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relations
    public function representative()
    {
        return $this->belongsTo(Representative::class, 'representative_id');
    }

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
