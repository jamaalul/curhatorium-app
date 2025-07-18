<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeeklyStat;
use App\Models\User;
use Faker\Factory as Faker;

class WeeklyStatSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $userIds = User::pluck('id')->all();
        if (empty($userIds)) return;

        foreach (range(1, 10) as $i) {
            $start = $faker->dateTimeBetween('-3 months', 'now');
            $end = (clone $start)->modify('+6 days');
            WeeklyStat::create([
                'user_id' => $faker->randomElement($userIds),
                'week_start' => $start->format('Y-m-d'),
                'week_end' => $end->format('Y-m-d'),
                'avg_mood' => $faker->randomFloat(1, 1, 10),
                'total_entries' => $faker->numberBetween(3, 7),
                'feedback' => $faker->optional()->sentence(),
            ]);
        }
    }
} 