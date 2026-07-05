<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\XpService;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
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

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    /**
     * The daily XP logs for the user.
     */
    public function dailyXpLogs()
    {
        return $this->hasMany(DailyXpLog::class);
    }

    /**
     * Award XP to the user for a specific activity.
     */
    public function awardXp(string $activity, int $quantity = 1): array
    {
        $xpService = app(XpService::class);

        return $xpService->awardXp($this, $activity, $quantity);
    }

    /**
     * Get XP progress towards psychologist access.
     */
    public function getXpProgress(): array
    {
        $xpService = app(XpService::class);

        return $xpService->getXpProgress($this);
    }

    /**
     * Check if user can access psychologist.
     */
    public function canAccessPsychologist(): bool
    {
        $xpService = app(XpService::class);

        return $xpService->canAccessPsychologist($this);
    }

    /**
     * Get daily XP summary.
     */
    public function getDailyXpSummary(): array
    {
        $xpService = app(XpService::class);

        return $xpService->getDailyXpSummary($this);
    }

    /**
     * Get XP breakdown for all activities.
     */
    public function getXpBreakdown(): array
    {
        $xpService = app(XpService::class);

        return $xpService->getXpBreakdown($this);
    }

    /**
     * Get XP history for the user.
     */
    public function getXpHistory(int $days = 30): array
    {
        $xpService = app(XpService::class);

        return $xpService->getXpHistory($this, $days);
    }

    /**
     * Get consultations for the user
     */
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    /**
     * Get active consultations
     */
    public function activeConsultations()
    {
        return $this->consultations()
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
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get all of the user's consultation messages.
     */
    public function consultationMessages()
    {
        return $this->morphMany(ConsultationMessage::class, 'sender');
    }

    /**
     * Get the user's active subscription.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class)->where('status', 'active');
    }
}
