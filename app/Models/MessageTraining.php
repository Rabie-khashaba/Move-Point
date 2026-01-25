<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Government;
use App\Models\Location;

class MessageTraining extends Model
{
    use HasFactory;

    // Table name (if different from pluralized model name)
    protected $table = 'message_trainings';

    // The attributes that are mass assignable
    protected $fillable = [
        'type',
        'company_id',
        'link_training',
        'description_location',
        'description_training',
        'government_id',
        'location_id',
        'google_map_url',
    ];

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




}
