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
use Illuminate\Validation\ValidationException;

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

    protected static function booted(): void
    {
        static::saving(function (Chapter $chapter): void {
            if ($chapter->type === ChapterType::Reading) {
                if (blank($chapter->text_content)) {
                    throw ValidationException::withMessages([
                        'text_content' => 'Konten bacaan wajib diisi untuk chapter reading.',
                    ]);
                }

                $chapter->video_url = null;
            }

            if ($chapter->type === ChapterType::Video) {
                if (blank($chapter->video_url)) {
                    throw ValidationException::withMessages([
                        'video_url' => 'URL video wajib diisi untuk chapter video.',
                    ]);
                }

                $chapter->text_content = null;
            }

            if ($chapter->type === ChapterType::Quiz) {
                $chapter->text_content = null;
                $chapter->video_url = null;
            }
        });

        static::saved(function (Chapter $chapter): void {
            if ($chapter->type !== ChapterType::Quiz) {
                $chapter->questions()->each(function (QuizQuestion $question): void {
                    $question->delete();
                });
            }
        });

        static::deleting(function (Chapter $chapter): void {
            if (! $chapter->isForceDeleting()) {
                $chapter->order_number = ((int) static::query()
                    ->withTrashed()
                    ->where('cbt_module_id', $chapter->cbt_module_id)
                    ->max('order_number')) + 1;
                $chapter->saveQuietly();
            }
        });
    }

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
