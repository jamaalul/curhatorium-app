<?php

namespace Database\Factories;

use App\Models\CbtModule;
use App\Models\User;
use App\Models\UserCbtModule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserCbtModule>
 */
class UserCbtModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'cbt_module_id' => CbtModule::factory(),
            'source_order_id' => null,
            'granted_at' => now(),
            'revoked_at' => null,
        ];
    }

    public function revoked(): static
    {
        return $this->state(fn (): array => [
            'revoked_at' => now(),
        ]);
    }
}
