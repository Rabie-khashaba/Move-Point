<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WhatsAppLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'message',
        'status',
        'service',
        'response',
        'error',
        'ip_address',
        'user_agent',
        'attempts',
        'sent_at',
        'failed_at',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for pending/failed messages
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'waiting', 'failed']);
    }

    /**
     * Scope for sent messages
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for failed messages
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for today's messages
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Scope for this week's messages
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope for this month's messages
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'sent' => 'success',
            'failed' => 'danger',
            'pending' => 'warning',
            'waiting' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Get status text in Arabic
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'sent' => 'مرسل',
            'failed' => 'فشل',
            'pending' => 'معلق',
            'waiting' => 'في الانتظار',
            default => 'غير محدد'
        };
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute()
    {
        return $this->phone;
    }

    /**
     * Get WhatsApp link
     */
    public function getWhatsAppLinkAttribute()
    {
        $phone = str_replace(['+', ' ', '-'], '', $this->phone);
        return "https://wa.me/{$phone}";
    }

    /**
     * Get message preview
     */
    public function getMessagePreviewAttribute()
    {
        return strlen($this->message) > 100 
            ? substr($this->message, 0, 100) . '...' 
            : $this->message;
    }

    /**
     * Get time ago
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Check if message can be resent
     */
    public function canResend()
    {
        return in_array($this->status, ['failed', 'pending', 'waiting']) && $this->attempts < 5;
    }

    /**
     * Increment attempts
     */
    public function incrementAttempts()
    {
        $this->increment('attempts');
        $this->save();
    }

    /**
     * Mark as sent
     */
    public function markAsSent($response = null)
    {
        $this->update([
            'status' => 'sent',
            'response' => $response,
            'sent_at' => now(),
            'attempts' => $this->attempts + 1
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed($error = null)
    {
        $this->update([
            'status' => 'failed',
            'error' => $error,
            'failed_at' => now(),
            'attempts' => $this->attempts + 1
        ]);
    }
}
