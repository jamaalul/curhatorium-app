<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageV2 extends Model
{
    use HasFactory;

    protected $table = 'messages_v2';

    protected $fillable = [
        'sender_id',
        'sender_type',
        'room',
        'message',
    ];

    public function sender()
    {
        return $this->morphTo();
    }
}
