<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Product::factory(20)
            ->has(
                \App\Models\ProductMedia::factory()
                    ->count(3)
                    ->sequence(fn ($sequence) => ['order_number' => $sequence->index + 1]),
                'media'
            )
            ->create();
    }
}
