<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'logo',
        'phone',
        'email',
        'website',
        'address',
        'description',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_active' => true
    ];

    // Relationships
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function representatives()
    {
        return $this->hasMany(Representative::class);
    }

    // Accessors
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/app/public/' . $this->logo);
        }
        return asset('assets/images/default-company.png');
    }

    public function getDisplayNameAttribute()
    {
        return $this->name ?? 'Unnamed Company';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
