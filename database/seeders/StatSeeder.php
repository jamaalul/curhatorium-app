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

        // Create 84 stats (12 weeks * 7 days) starting from 3 months ago
        $startDate = now()->subMonths(3)->startOfDay();
        $activities = array_keys(Stat::getActivityOptions());
        
        foreach (range(1, 84) as $i) {
            $createdAt = $startDate->copy()->addDays($i - 1);
            Stat::create([
                'user_id' => $faker->randomElement($userIds),
                'mood' => $faker->numberBetween(1, 10),
                'activity' => $faker->randomElement($activities),
                'explanation' => $faker->sentence(),
                'energy' => $faker->numberBetween(1, 10),
                'productivity' => $faker->numberBetween(1, 10),
                'day' => $createdAt->format('l'), // e.g., 'Monday'
                'feedback' => $faker->sentence(),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
} 