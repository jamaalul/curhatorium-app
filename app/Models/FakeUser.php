<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FakeUser extends Model
{
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'profile_picture',
        'total_xp',
        'group_id',
        'is_admin',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];
}
