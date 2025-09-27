<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotChatMessageV2 extends Model
{
    protected $table = 'chatbot_chat_message_v2_s';

    protected $fillable = [
        'chat_id',
        'message',
        'role',
    ];

    public function chat()
    {
        return $this->belongsTo(ChatbotChatV2::class, 'chat_id');
    }
}
