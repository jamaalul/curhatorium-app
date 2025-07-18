<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonthlyStat;
use App\Models\User;
use Faker\Factory as Faker;

class MonthlyStatSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $userIds = User::pluck('id')->all();
        if (empty($userIds)) return;
        $months = [
            'January 2024', 'December 2023', 'November 2023',
            'October 2023', 'September 2023', 'August 2023'
        ];
        foreach ($months as $month) {
            MonthlyStat::create([
                'user_id' => $faker->randomElement($userIds),
                'month' => $month,
                'avg_mood' => $faker->randomFloat(1, 1, 10),
                'total_entries' => $faker->numberBetween(10, 31),
                'feedback' => $faker->optional()->sentence(),
            ]);
        }
    }
} 