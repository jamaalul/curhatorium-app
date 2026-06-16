<?php

namespace Database\Factories;

use App\Models\MembershipPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MembershipPlan>
 */
class MembershipPlanFactory extends Factory
{
    protected $model = MembershipPlan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Calm', 'Mindful', 'Serene']),
            'price_idr' => fake()->randomElement([49900, 79900, 149900]),
            'billing_cycle' => fake()->randomElement(['monthly', 'yearly']),
            'ai_window_hours' => fake()->randomElement([2, 6, 12]),
        ];
    }
}
