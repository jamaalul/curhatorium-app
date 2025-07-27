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
}