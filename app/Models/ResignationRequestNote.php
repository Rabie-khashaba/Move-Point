<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResignationRequestNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'resignation_request_id',
        'note',
        'status',
        'created_by',
    ];

    public function resignationRequest()
    {
        return $this->belongsTo(ResignationRequest::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
