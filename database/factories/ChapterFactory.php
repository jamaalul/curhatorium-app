<?php

namespace Database\Factories;

use App\ChapterType;
use App\Models\CbtModule;
use App\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chapter>
 */
class ChapterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cbt_module_id' => CbtModule::factory(),
            'title' => fake()->sentence(4),
            'type' => ChapterType::Reading,
            'text_content' => fake()->paragraphs(4, true),
            'video_url' => null,
            'order_number' => 1,
        ];
    }

    public function video(): static
    {
        return $this->state(fn (): array => [
            'type' => ChapterType::Video,
            'text_content' => null,
            'video_url' => fake()->url(),
        ]);
    }

    public function quiz(): static
    {
        return $this->state(fn (): array => [
            'type' => ChapterType::Quiz,
            'text_content' => null,
            'video_url' => null,
        ]);
    }
}
