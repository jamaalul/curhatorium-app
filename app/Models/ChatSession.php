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
        'status',
        'type',
        'pending_end', // Add pending_end to fillable
        'jitsi_room', // Add jitsi_room for video sessions
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'pending_end' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    /**
     * Get the ticket consumptions for this chat session.
     */
    public function shareTalkTicketConsumptions()
    {
        return $this->hasMany(\App\Models\ShareTalkTicketConsumption::class);
    }
}
