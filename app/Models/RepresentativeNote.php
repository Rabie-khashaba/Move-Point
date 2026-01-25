<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepresentativeNote extends Model
{
    protected $fillable = [
        'representative_id',
        'note',
        'created_by',
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
