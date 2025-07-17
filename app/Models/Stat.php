<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stat extends Model
{
    protected $fillable = [
        'user_id',
        'mood',
        'activity',
        'explanation',
        'energy',
        'productivity',
        'day',
        'feedback',
    ];

    protected $casts = [
        'mood' => 'integer',
        'energy' => 'integer',
        'productivity' => 'integer',
    ];

    /**
     * Get the user that owns the stat.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the activity options available for the tracker.
     */
    public static function getActivityOptions(): array
    {
        return [
            'work' => 'Work/Study',
            'exercise' => 'Exercise',
            'social' => 'Social Time',
            'hobbies' => 'Hobbies',
            'rest' => 'Rest/Sleep',
            'entertainment' => 'Entertainment',
            'nature' => 'Nature/Outdoors',
            'food' => 'Food/Cooking',
            'health' => 'Health/Medical',
            'other' => 'Other'
        ];
    }
}
