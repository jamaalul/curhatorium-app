<?php

namespace App\Models;

use Database\Factories\QuizQuestionOptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
