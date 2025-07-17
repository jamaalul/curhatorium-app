<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ChatSession;
use App\Models\User;
use App\Models\Professional;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'sender_type',
        'session_id',
        'message',
    ];

    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }

    public function sender()
    {
        // This is a manual polymorphic relationship based on sender_type and sender_id
        if ($this->sender_type === 'user') {
            return $this->belongsTo(User::class, 'sender_id');
        } elseif ($this->sender_type === 'professional') {
            return $this->belongsTo(Professional::class, 'sender_id');
        }
        return null;
    }
}
