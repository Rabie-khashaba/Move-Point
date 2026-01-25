<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageWorking extends Model
{
    use HasFactory;

    protected $table = 'message_workings';

    protected $fillable = [
        'government_id',
        'location_id',
        'company_id',
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

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class, 'message_id');
    }
}
