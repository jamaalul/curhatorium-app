<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTicket extends Model
{
    protected $fillable = [
        'user_id', 'ticket_type', 'limit_type', 'limit_value', 'remaining_value', 'expires_at',
    ];
}
