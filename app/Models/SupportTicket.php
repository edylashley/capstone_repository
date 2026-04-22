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
        'admin_reply',
        'status',
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
            'bug'        => 'System Bug / Error',
            'correction' => 'Record Correction',
            'account'    => 'Account / Login Issue',
            'general'    => 'General Question',
            'security'   => '🚨 SECURITY ALERT',
            default      => ucfirst($this->category),
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
     * Status badge CSS classes.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'  => $this->category === 'security' 
                            ? 'bg-rose-500/10 text-rose-500 border-rose-500/20' 
                            : 'bg-amber-500/10 text-amber-500 border-amber-500/20',
            'resolved' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
            default    => 'bg-slate-800 text-slate-400 border-white/5',
        };
    }
}

