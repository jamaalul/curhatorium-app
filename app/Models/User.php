<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\SgdGroup;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'group_id',
        'profile_picture',
        'total_xp',
        'onboarding_completed',
        'provider_name',
        'provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The SGD groups that the user has joined.
     */
    public function sgdGroups()
    {
        return $this->belongsToMany(SgdGroup::class, 'sgd_group_user')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    /**
     * Check if user has joined a specific SGD group.
     */
    public function hasJoinedSgdGroup($sgdGroupId)
    {
        return $this->sgdGroups()->where('sgd_group_id', $sgdGroupId)->exists();
    }

    /**
     * Get the tickets owned by the user.
     */
    public function userTickets()
    {
        return $this->hasMany(\App\Models\UserTicket::class, 'user_id');
    }

    /**
     * Get the user memberships.
     */
    public function userMemberships()
    {
        return $this->hasMany(\App\Models\UserMembership::class, 'user_id');
    }

    /**
     * Get active memberships for the user.
     */
    public function activeMemberships()
    {
        return $this->userMemberships()
            ->where('expires_at', '>', now())
            ->with('membership');
    }

    /**
     * Check if user has active Inner Peace membership.
     */
    public function hasActiveInnerPeaceMembership()
    {
        return $this->activeMemberships()
            ->whereHas('membership', function($query) {
                $query->where('name', 'Inner Peace');
            })
            ->exists();
    }

     public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->is_admin;
    }

    /**
     * The daily XP logs for the user.
     */
    public function dailyXpLogs()
    {
        return $this->hasMany(\App\Models\DailyXpLog::class);
    }

    /**
     * Award XP to the user for a specific activity.
     */
    public function awardXp(string $activity, int $quantity = 1): array
    {
        $xpService = app(\App\Services\XpService::class);
        return $xpService->awardXp($this, $activity, $quantity);
    }

    /**
     * Get XP progress towards psychologist access.
     */
    public function getXpProgress(): array
    {
        $xpService = app(\App\Services\XpService::class);
        return $xpService->getXpProgress($this);
    }

    /**
     * Check if user can access psychologist.
     */
    public function canAccessPsychologist(): bool
    {
        $xpService = app(\App\Services\XpService::class);
        return $xpService->canAccessPsychologist($this);
    }

    /**
     * Get daily XP summary.
     */
    public function getDailyXpSummary(): array
    {
        $xpService = app(\App\Services\XpService::class);
        return $xpService->getDailyXpSummary($this);
    }

    /**
     * Get XP breakdown for all activities.
     */
    public function getXpBreakdown(): array
    {
        $xpService = app(\App\Services\XpService::class);
        return $xpService->getXpBreakdown($this);
    }

    /**
     * Get XP history for the user.
     */
    public function getXpHistory(int $days = 30): array
    {
        $xpService = app(\App\Services\XpService::class);
        return $xpService->getXpHistory($this, $days);
    }

    /**
     * Get active tickets for the user
     */
    public function activeTickets()
    {
        return $this->hasMany(\App\Models\UserTicket::class)
            ->where('expires_at', '>', now())
            ->where('remaining_value', '>', 0);
    }

    /**
     * Check if user can access a specific feature
     */
    public function canAccessFeature(string $feature): bool
    {
        return $this->activeTickets()
            ->where('ticket_type', $feature)
            ->exists();
    }

    /**
     * Get chat sessions for the user
     */
    public function chatSessions()
    {
        return $this->hasMany(\App\Models\ChatSession::class);
    }

    /**
     * Get active chat sessions
     */
    public function activeChatSessions()
    {
        return $this->chatSessions()
            ->whereIn('status', ['waiting', 'pending', 'active']);
    }

    /**
     * Get user's full name with username
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->username})";
    }

    /**
     * Check if user has any active membership
     */
    public function hasActiveMembership(): bool
    {
        return $this->activeMemberships()->exists();
    }

    /**
     * Get user's primary active membership
     */
    public function getPrimaryMembershipAttribute()
    {
        return $this->activeMemberships()
            ->with('membership')
            ->orderBy('expires_at', 'desc')
            ->first();
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for users with memberships
     */
    public function scopeWithMemberships($query)
    {
        return $query->with('userMemberships.membership');
    }

    /**
     * Scope for users with active tickets
     */
    public function scopeWithActiveTickets($query)
    {
        return $query->with('activeTickets');
    }
    /**
     * Get all of the user's messages.
     */
    public function messages()
    {
        return $this->morphMany(MessageV2::class, 'sender');
    }
}