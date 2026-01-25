<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $fillable = ['governorate_id', 'name', 'location', 'address', 'is_active'];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function getNameAttribute($value)
    {
        if (array_key_exists('is_active', $this->attributes) && $this->is_active === false) {
            return trim((string)$value) . ' (قيد الانتظار)';
        }
        return $value;
    }
}
