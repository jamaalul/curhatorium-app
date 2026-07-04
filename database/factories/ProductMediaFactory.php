<?php

namespace Database\Factories;

use App\Models\ProductMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductMedia>
 */
class ProductMediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'media_type' => 'image',
            'media_url' => fake()->imageUrl(640, 480, 'product', true),
            'order_number' => fake()->numberBetween(1, 5),
        ];
    }
}
