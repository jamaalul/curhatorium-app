<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMembership extends Model
{
    protected $fillable = [
        'user_id', 'membership_id', 'started_at', 'expires_at',
    ];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}
