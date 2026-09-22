<?php

namespace Database\Factories;

use App\Models\Chapter;
use App\Models\User;
use App\Models\UserChapterProgress;
use App\ProgressStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserChapterProgress>
 */
class UserChapterProgressFactory extends Factory
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
            'chapter_id' => Chapter::factory(),
            'status' => ProgressStatus::InProgress,
            'started_at' => now(),
            'last_accessed_at' => now(),
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => ProgressStatus::Completed,
            'completed_at' => now(),
        ]);
    }
}
