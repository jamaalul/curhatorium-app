<?php

namespace Database\Factories;

use App\Models\MembershipPlan;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_ref' => Order::generateOrderRef(),
            'user_id' => User::factory(),
            'orderable_type' => MembershipPlan::class,
            'orderable_id' => MembershipPlan::factory(),
            'quantity' => 1,
            'unit_price' => fake()->randomElement([49900, 79900, 149900]),
            'gross_amount' => fn (array $attributes) => $attributes['unit_price'] * $attributes['quantity'],
            'status' => 'pending',
            'expired_at' => now()->addMinutes(15),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'expired_at' => now()->addMinutes(15),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => 'paid',
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => 'expired',
            'expired_at' => now()->subMinutes(1),
        ]);
    }
}
