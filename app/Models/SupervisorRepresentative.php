<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorRepresentative extends Model
{
    protected $table = 'supervisor_representative'; // اسم الجدول

    public $timestamps = false; // لو الجدول مفيهوش created_at / updated_at

    protected $fillable = [
        'supervisor_id',
        'representative_id',
    ];

    // علاقة supervisor (عادة User أو Employee)
    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id');
    }

    // علاقة المندوب
    public function representative()
    {
        return $this->belongsTo(Representative::class, 'representative_id');
    }
}
