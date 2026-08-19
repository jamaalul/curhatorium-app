<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiWindow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'starts_at',
        'ends_at',
        'tokens_used',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'tokens_used' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to find an active window for a given user.
     */
    public function scopeActiveForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId)
            ->where('ends_at', '>', now());
    }
}
