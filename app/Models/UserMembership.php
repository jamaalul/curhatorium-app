<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMembership extends Model
{
    protected $fillable = [
        'user_id', 'membership_id', 'started_at', 'expires_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the membership is active
     */
    public function isActive(): bool
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    /**
     * Get the status of the membership
     */
    public function getStatusAttribute(): string
    {
        return $this->isActive() ? 'Active' : 'Expired';
    }
}
