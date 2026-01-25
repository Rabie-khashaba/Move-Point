<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'sort_order',
    ];
    protected $appends = ['image_url']; // هتظهر في الـ JSON تلقائي

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        // المسار النسبي
        $path = ltrim($this->image_path, '/');

        // جرّب storage symlink
        if (Storage::disk('public')->exists($path)) {
            return asset('storage/app/public/' . $path);
        }

        // fallback على الـ Route
        return route('sliders.image', $this->id);
    }
}


