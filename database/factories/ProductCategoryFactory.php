<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'T-Shirt',
            'Long Sleeve',
            'Hoodie',
            'Sweater',
            'Totebag',
            'Topi',
            'Jaket',
            'Polo Shirt',
        ]);

        return [
            'name' => $name,
            'slug' => str($name)->slug(),
        ];
    }
}
