<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotMessage extends Model
{
    protected $fillable = [
        'chatbot_session_id',
        'role',
        'content',
    ];

    /**
     * Get the session that owns the message.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatbotSession::class, 'chatbot_session_id');
    }
}
