<?php

namespace Database\Factories;

use App\Models\Chapter;
use App\Models\QuizQuestion;
use App\QuizQuestionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuizQuestion>
 */
class QuizQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chapter_id' => Chapter::factory()->quiz(),
            'question' => fake()->sentence().'?',
            'type' => QuizQuestionType::MultipleChoice,
            'accepted_answer' => null,
            'points' => 1,
            'order_number' => 1,
        ];
    }

    public function shortAnswer(): static
    {
        return $this->state(fn (): array => [
            'type' => QuizQuestionType::ShortAnswer,
            'accepted_answer' => fake()->word(),
        ]);
    }
}
