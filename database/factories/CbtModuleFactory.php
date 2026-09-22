<?php

namespace Database\Factories;

use App\Models\CbtModule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CbtModule>
 */
class CbtModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'slug' => fake()->unique()->slug(4),
            'description' => fake()->paragraphs(3, true),
            'price' => fake()->randomFloat(2, 0, 500000),
            'is_published' => false,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'is_published' => true,
            'published_at' => now(),
        ]);
    }
}
