<?php

namespace App\Models;

use App\QuizQuestionType;
use Database\Factories\QuizQuestionOptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class QuizQuestionOption extends Model
{
    /** @use HasFactory<QuizQuestionOptionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quiz_question_id',
        'option_text',
        'is_correct',
        'order_number',
    ];

    protected $hidden = [
        'is_correct',
    ];

    protected $attributes = [
        'is_correct' => false,
    ];

    protected static function booted(): void
    {
        static::saving(function (QuizQuestionOption $option): void {
            $question = QuizQuestion::query()->find($option->quiz_question_id);

            if (! $question || $question->type !== QuizQuestionType::MultipleChoice) {
                throw ValidationException::withMessages([
                    'option_text' => 'Opsi hanya dapat ditambahkan pada pertanyaan multiple choice.',
                ]);
            }
        });

        static::deleting(function (QuizQuestionOption $option): void {
            if (! $option->isForceDeleting()) {
                $option->order_number = ((int) static::query()
                    ->withTrashed()
                    ->where('quiz_question_id', $option->quiz_question_id)
                    ->max('order_number')) + 1;
                $option->saveQuietly();
            }
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'order_number' => 'integer',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'selected_option_id');
    }
}
