<?php
namespace App\Models;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Governorate extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'is_active', 'inactive_date'];
    protected $casts = [
        'is_active' => 'boolean',
        'inactive_date' => 'date',
    ];

    public function getNameAttribute($value)
    {
        if (array_key_exists('is_active', $this->attributes) && $this->is_active === false) {
            return trim((string)$value) . ' (قيد الانتظار)';
        }
        return $value;
    }

    /**
     * Get the locations for the governorate.
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }


}
