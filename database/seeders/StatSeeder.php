<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stat;
use App\Models\User;
use Faker\Factory as Faker;

class StatSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $userIds = User::pluck('id')->all();
        if (empty($userIds)) return;

        foreach (range(1, 20) as $i) {
            $createdAt = $faker->dateTimeBetween('-2 months', 'now');
            Stat::create([
                'user_id' => $faker->randomElement($userIds),
                'mood' => $faker->numberBetween(1, 10),
                'activity' => $faker->randomElement(array_keys(Stat::getActivityOptions())),
                'explanation' => $faker->optional()->sentence(),
                'energy' => $faker->numberBetween(1, 10),
                'productivity' => $faker->numberBetween(1, 10),
                'day' => $createdAt->format('l'), // e.g., 'Monday'
                'feedback' => $faker->optional()->sentence(),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
} 