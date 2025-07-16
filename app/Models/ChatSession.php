<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Professional;

class ChatSession extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'professional_id',
        'start',
        'end',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}
