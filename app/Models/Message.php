<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // Table name (if different from pluralized model name)
    protected $table = 'messages';

    // The attributes that are mass assignable
    protected $fillable = [
        'government_id',
        'location_id',
        'description',
        'google_map_url',
    ];

    // Relationships
    public function government()
    {
        return $this->belongsTo(Governorate::class, 'government_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
    public function interviews()
    {
        return $this->hasMany(Interview::class, 'message_id');
    }

}
