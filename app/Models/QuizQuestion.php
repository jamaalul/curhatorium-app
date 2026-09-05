<?php

namespace App\Models;

use App\ChapterType;
use App\QuizQuestionType;
use Database\Factories\QuizQuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class QuizQuestion extends Model
{
    /** @use HasFactory<QuizQuestionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chapter_id',
        'question',
        'type',
        'accepted_answer',
        'points',
        'order_number',
    ];

    protected $hidden = [
        'accepted_answer',
    ];

    protected $attributes = [
        'points' => 1,
    ];

    protected static function booted(): void
    {
        static::saving(function (QuizQuestion $question): void {
            $chapter = Chapter::query()->find($question->chapter_id);

            if (! $chapter || $chapter->type !== ChapterType::Quiz) {
                throw ValidationException::withMessages([
                    'question' => 'Pertanyaan hanya dapat ditambahkan pada chapter quiz.',
                ]);
            }

            if ($question->type === QuizQuestionType::ShortAnswer && blank($question->accepted_answer)) {
                throw ValidationException::withMessages([
                    'accepted_answer' => 'Jawaban yang diterima wajib diisi untuk short answer.',
                ]);
            }

            if ($question->type === QuizQuestionType::MultipleChoice) {
                $question->accepted_answer = null;
            }

            if ((float) $question->points < 0) {
                throw ValidationException::withMessages([
                    'points' => 'Poin tidak boleh negatif.',
                ]);
            }
        });

        static::saved(function (QuizQuestion $question): void {
            if ($question->type === QuizQuestionType::ShortAnswer) {
                $question->options()->each(function (QuizQuestionOption $option): void {
                    $option->delete();
                });
            }
        });

        static::deleting(function (QuizQuestion $question): void {
            if (! $question->isForceDeleting()) {
                $question->order_number = ((int) static::query()
                    ->withTrashed()
                    ->where('chapter_id', $question->chapter_id)
                    ->max('order_number')) + 1;
                $question->saveQuietly();
            }
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'type' => QuizQuestionType::class,
            'points' => 'decimal:2',
            'order_number' => 'integer',
        ];
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizQuestionOption::class)->orderBy('order_number');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }
}
