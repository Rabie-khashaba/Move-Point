<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Support extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'date',
        'issue',
        'status',
        'reply_message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];


    public function replies()
    {
        return $this->hasMany(SupportReply::class);
    }
}
