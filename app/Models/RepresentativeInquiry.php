<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepresentativeInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'representative_id',
        'inquiry_type',
        'inquiry_field_result',
        'inquiry_field_notes',
        'inquiry_field_attachments',
        'inquiry_security_result',
        'inquiry_security_notes',
        'inquiry_security_attachments',
        'security_inactive_reason',
    ];

    protected $casts = [
        'inquiry_field_attachments' => 'array',
        'inquiry_security_attachments' => 'array',
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }
}
