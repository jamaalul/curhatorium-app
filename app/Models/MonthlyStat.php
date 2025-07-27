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
        'avg_productivity',
        'total_entries',
        'best_mood',
        'feedback',
    ];

    protected $casts = [
        'avg_mood' => 'float',
        'avg_productivity' => 'float',
        'best_mood' => 'float',
        'total_entries' => 'integer',
    ];

    /**
     * Get the user that owns the monthly stat.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 