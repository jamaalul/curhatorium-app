<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agendas';
    protected $fillable = [
        'type',
        'title',
        'description',
        'event_date',
        'event_time',
        'event_address',
        'is_done'
    ];
}
