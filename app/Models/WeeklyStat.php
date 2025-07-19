<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyStat extends Model
{
    protected $fillable = [
        'user_id',
        'week_start',
        'week_end',
        'avg_mood',
        'avg_productivity',
        'total_entries',
        'best_mood',
        'feedback',
    ];

    /**
     * Get the user that owns the weekly stat.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 