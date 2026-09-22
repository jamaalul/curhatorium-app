<?php

namespace Database\Factories;

use App\Models\Chapter;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuizAttempt>
 */
class QuizAttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'chapter_id' => Chapter::factory()->quiz(),
            'attempt_number' => 1,
            'score' => 0,
            'max_score' => 0,
            'started_at' => now(),
            'submitted_at' => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn (): array => [
            'submitted_at' => now(),
        ]);
    }
}
