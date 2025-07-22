<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatbotSession extends Model
{
    protected $fillable = [
        'user_id',
        'title',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the messages for the session.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatbotMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the first message content to use as title if no title is set.
     */
    public function getTitleAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        $firstMessage = $this->messages()->where('role', 'user')->first();
        if ($firstMessage) {
            return Str::limit($firstMessage->content, 50);
        }
        
        return 'New Chat';
    }
}
