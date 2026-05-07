<?php

namespace App\Models;

use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use MassPrunable;

    protected $fillable = [
        'user_id',
        'email',
        'category',
        'subject',
        'message',
        'attachment_path',
        'admin_reply',
        'status',
        'priority',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];


    /**
     * Prunable query — delete tickets whose expires_at has passed.
     */
    public function prunable()
    {
        return static::whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Human-readable category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'bug' => 'System Bug / Error',
            'correction' => 'Record Correction',
            'account' => 'Account / Login Issue',
            'general' => 'General Question',
            'security' => '🚨 SECURITY ALERT',
            default => ucfirst($this->category),
        };
    }

    /**
     * Check if this is an automated security alert.
     */
    public function getIsSecurityAlertAttribute(): bool
    {
        return $this->category === 'security';
    }

    /**
     * Human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->category === 'security') {
            return 'THREAT BLOCKED';
        }

        return match ($this->status) {
            'pending' => 'PENDING',
            'resolved' => 'RESOLVED',
            default => strtoupper($this->status),
        };
    }

    /**
     * Status badge CSS classes.
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->category === 'security') {
            return 'bg-rose-500/10 text-rose-500 border-rose-500/30 shadow-lg shadow-rose-500/5';
        }

        return match ($this->status) {
            'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
            'resolved' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
            default => 'bg-slate-800 text-slate-400 border-white/5',
        };
    }

    /**
     * Priority badge CSS classes.
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'bg-rose-500/10 text-rose-500 border-rose-500/40 shadow-[0_0_10px_rgba(225,29,72,0.3)] animate-pulse',
            'high' => 'bg-orange-500/10 text-orange-400 border-orange-500/40',
            'medium' => 'bg-blue-500/10 text-blue-400 border-blue-500/40',
            'low' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            default => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
        };
    }

    /**
     * Get the count of pending tickets and recent security alerts for admin notifications.
     */
    public static function getNotificationCount(): int
    {
        return static::where('status', 'pending')
            ->orWhere(fn($q) => $q->where('category', 'security')->where('created_at', '>=', now()->subDay()))
            ->count();
    }
}

