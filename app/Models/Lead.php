<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'governorate_id',
        'source_id',
        'status',
        'follow_ups',
        'notes',
        'assigned_to',
        'next_follow_up',
        'is_active',
        'referred_by',
        'referred_by_type',
        'location_id',
        'moderator_id',
        'advertiser_id',
        'transportation',
    ];

    protected $casts = [
        'follow_ups' => 'array',
        'next_follow_up' => 'date',
        'is_active' => 'boolean',
    ];


    // Relationships
    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
    public function followUps()
    {
        return $this->hasMany(LeadFollowup::class);
    }


    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function employee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function representative()
    {
        return $this->hasOne(Representative::class, 'phone', 'phone');
    }
    public function supervisor()
    {
        return $this->hasOne(Supervisor::class, 'phone', 'phone');
    }
    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
    public function referredByType()
    {
        return $this->belongsTo(User::class, 'referred_by_type');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class);
    }

    public function advertiser()
    {
        return $this->belongsTo(Advertiser::class, 'advertiser_id');
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        $colors = [
            'متابعة' => 'primary',
            'غير مهتم' => 'danger',
            'عمل مقابلة' => 'success',
            'مقابلة' => 'info',
            'مفاوضات' => 'warning',
            'مغلق' => 'secondary',
            'خسر' => 'dark'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getDisplayNameAttribute()
    {
        return $this->name ?? 'Unnamed Lead';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('lead_score', '>=', 8);
    }
    public function interviews()
    {
        return $this->hasMany(Interview::class, 'lead_id');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'client_id');
    }


}
