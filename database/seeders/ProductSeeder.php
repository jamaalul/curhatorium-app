<?php

namespace Database\Seeders;

use App\Models\EcommerceLink;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductMedia;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a fixed set of categories so products can be spread across them.
        $categories = collect([
            ['name' => 'T-Shirt', 'slug' => 't-shirt'],
            ['name' => 'Long Sleeve', 'slug' => 'long-sleeve'],
            ['name' => 'Hoodie', 'slug' => 'hoodie'],
            ['name' => 'Sweater', 'slug' => 'sweater'],
            ['name' => 'Totebag', 'slug' => 'totebag'],
        ])->map(fn (array $data) => ProductCategory::firstOrCreate(['slug' => $data['slug']], $data));

        // Create 20 products, each with 1–3 media items and 1–2 ecommerce links.
        Product::factory(20)
            ->has(
                ProductMedia::factory()
                    ->count(fake()->numberBetween(1, 3))
                    ->sequence(fn ($sequence) => ['order_number' => $sequence->index + 1]),
                'media'
            )
            ->has(
                EcommerceLink::factory()
                    ->count(fake()->numberBetween(1, 2)),
                'ecommerceLinks'
            )
            ->create([
                'product_category_id' => fn () => $categories->random()->id,
            ]);
    }
}
