<?php

namespace Database\Factories;

use App\Models\CbtModule;
use App\Models\User;
use App\Models\UserModuleProgress;
use App\ProgressStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserModuleProgress>
 */
class UserModuleProgressFactory extends Factory
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
            'cbt_module_id' => CbtModule::factory(),
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
