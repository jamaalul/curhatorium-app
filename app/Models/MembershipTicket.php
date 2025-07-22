<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipTicket extends Model
{
    protected $fillable = [
        'membership_id', 'ticket_type', 'limit_type', 'limit_value',
    ];
}
