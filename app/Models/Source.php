<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
