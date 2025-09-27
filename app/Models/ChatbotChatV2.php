<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotChatV2 extends Model
{
    protected $table = 'chatbot_chat_v2_s';

    protected $fillable = [
        'user_id',
        'title',
        'identifier',
        'mode',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatbotChatMessageV2::class, 'chat_id');
    }
}
