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

     public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->is_admin;
    }
}