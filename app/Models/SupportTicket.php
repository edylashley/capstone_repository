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
            default      => ucfirst($this->category),
        };
    }

    /**
     * Status badge CSS classes.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'bg-amber-100 text-amber-800 border-amber-300',
            'resolved' => 'bg-green-100 text-green-800 border-green-300',
            default    => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}

