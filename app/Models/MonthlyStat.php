<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyStat extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'avg_mood',
        'avg_weekly_mood',
        'mood_fluctuation',
        'good_mood_days',
        'low_mood_days',
        'most_frequent_mood',
        'avg_productivity',
        'total_entries',
        'best_mood',
        'activity_analysis',
        'productivity_analysis',
        'pattern_analysis',
        'feedback',
    ];

    protected $casts = [
        'avg_mood' => 'float',
        'avg_weekly_mood' => 'float',
        'mood_fluctuation' => 'float',
        'good_mood_days' => 'integer',
        'low_mood_days' => 'integer',
        'most_frequent_mood' => 'integer',
        'avg_productivity' => 'float',
        'best_mood' => 'float',
        'total_entries' => 'integer',
        'activity_analysis' => 'array',
        'productivity_analysis' => 'array',
        'pattern_analysis' => 'array',
    ];

    /**
     * Get the user that owns the monthly stat.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 