<?php
// app/Models/NotificationLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $table = 'notifications_log';

    protected $fillable = [
        'user_id',
        'type',
        'channel',      // sms | email
        'recipient',    // phone or email address
        'subject',
        'message',
        'status',       // sent | failed | pending
        'raw_response',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Helpers ────────────────────────────────────────────────────

    /**
     * Create an in-app ("system") notification for a user.
     */
    public static function notify(int $userId, string $type, string $subject, string $message): self
    {
        return self::create([
            'user_id' => $userId,
            'type'    => $type,
            'channel' => 'system',
            'recipient' => 'in-app',
            'subject' => $subject,
            'message' => $message,
            'status'  => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function scopeSystem($query)
    {
        return $query->where('channel', 'system');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeSms($query)
    {
        return $query->where('channel', 'sms');
    }

    public function scopeEmail($query)
    {
        return $query->where('channel', 'email');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    // ── Computed ───────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'sent' => 'bg-success-bg text-success',
            'failed' => 'bg-danger-bg text-danger',
            default => 'bg-warning-bg text-warning',
        };
    }

    public function getChannelIconAttribute(): string
    {
        return $this->channel === 'sms' ? '📱' : '✉️';
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'invoice_generated' => 'Invoice Generated',
            'payment_received' => 'Payment Received',
            'overdue_reminder' => 'Overdue Reminder',
            'lease_expiry' => 'Lease Expiry',
            'welcome' => 'Welcome',
            'custom' => 'Custom',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
