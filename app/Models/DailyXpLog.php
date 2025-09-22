<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyXpLog extends Model
{
    protected $fillable = [
        'user_id',
        'xp_gained',
        'activity',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 