<?php

namespace App\Models;

use App\ChapterType;
use Database\Factories\ChapterFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chapter extends Model
{
    /** @use HasFactory<ChapterFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cbt_module_id',
        'title',
        'type',
        'text_content',
        'video_url',
        'order_number',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'type' => ChapterType::class,
            'order_number' => 'integer',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CbtModule::class, 'cbt_module_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order_number');
    }

    public function chapterProgresses(): HasMany
    {
        return $this->hasMany(UserChapterProgress::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order_number');
    }
}
