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
        'total_entries',
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