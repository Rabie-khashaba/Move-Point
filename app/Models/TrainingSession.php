<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'governorate_id',
        'location_id',
        'message_id',
        'date',
        'type',
        'note',
        'status',
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function message()
    {
        return $this->belongsTo(MessageTraining::class);
    }
}
