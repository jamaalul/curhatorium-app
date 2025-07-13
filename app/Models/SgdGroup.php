<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SgdGroup extends Model
{
    protected $table = 'sgd_groups';
    protected $fillable = [
        'title',
        'topic',
        'meeting_address',
        'schedule',
        'is_done',
        'category'
    ];

    protected $casts = [
        'schedule' => 'datetime',
        'is_done' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sgdGroup) {
            if (empty($sgdGroup->meeting_address)) {
                $sgdGroup->meeting_address = urlencode(Str::random(12));
            }
        });
    }
}
