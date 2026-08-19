<?php

namespace Database\Factories;

use App\Models\AiWindow;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiWindow>
 */
class AiWindowFactory extends Factory
{
    protected $model = AiWindow::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'starts_at' => now(),
            'ends_at' => now()->addHours(24),
            'tokens_used' => 0,
        ];
    }

    /**
     * Create an expired window.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subHours(48),
            'ends_at' => now()->subHours(24),
        ]);
    }

    /**
     * Create a window with specific token usage.
     */
    public function withTokensUsed(int $tokens): static
    {
        return $this->state(fn (array $attributes) => [
            'tokens_used' => $tokens,
        ]);
    }
}
