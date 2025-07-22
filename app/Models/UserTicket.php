<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTicket extends Model
{
    // For 'unlimited' tickets, limit_value and remaining_value are null. For others, these are numeric.
    protected $fillable = [
        'user_id', 'ticket_type', 'limit_type', 'limit_value', 'remaining_value', 'expires_at',
    ];
}
