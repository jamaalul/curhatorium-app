<?php

namespace Database\Factories;

use App\Models\EcommerceLink;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EcommerceLink>
 */
class EcommerceLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $platform = fake()->randomElement(['tokopedia', 'shopee', 'tiktok']);

        $url = match ($platform) {
            'tokopedia' => 'https://www.tokopedia.com/curhatorium/'.fake()->slug(3),
            'shopee' => 'https://shopee.co.id/curhatorium/'.fake()->slug(3),
            'tiktok' => 'https://www.tiktok.com/shop/curhatorium/'.fake()->slug(3),
        };

        return [
            'product_id' => Product::factory(),
            'ecommerce_name' => $platform,
            'url' => $url,
        ];
    }
}
