<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ShareTalkTicketConsumption extends Model
{
    protected $fillable = [
        'user_id',
        'chat_session_id',
        'ticket_source', // 'calm_starter' or 'paid'
        'consumed_at',
    ];

    protected $casts = [
        'consumed_at' => 'datetime',
    ];

    /**
     * Get the user who consumed the ticket
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the chat session for which the ticket was consumed
     */
    public function chatSession()
    {
        return $this->belongsTo(ChatSession::class);
    }

    /**
     * Check if this consumption is from a paid ticket (not Calm Starter)
     */
    public function isPaidTicket()
    {
        return $this->ticket_source === 'paid';
    }

    /**
     * Check if this consumption is from Calm Starter membership
     */
    public function isCalmStarterTicket()
    {
        return $this->ticket_source === 'calm_starter';
    }
} 