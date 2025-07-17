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

    /**
     * The users who have joined this SGD group.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'sgd_group_user')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    /**
     * Check if the group is starting within 15 minutes.
     */
    // public function isStartingSoon()
    // {
    //     $now = now();
    //     $startTime = $this->schedule;
    //     $fifteenMinutesBefore = $startTime->copy()->subMinutes(15);
        
    //     return $now->gte($fifteenMinutesBefore) && $now->lt($startTime);
    // }

    /**
     * Check if the group has already started.
     */
    public function hasStarted()
    {
        return now()->gte($this->schedule);
    }

    /**
     * Check if the group is upcoming (not started yet).
     */
    public function isUpcoming()
    {
        return now()->lt($this->schedule);
    }
}
