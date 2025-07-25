<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Always create or update the admin user
        User::updateOrCreate(
            [
                'email' => 'admin@curhatorium.com',
            ],
            [
                'username' => 'admin',
                'name' => 'Admin User',
                'password' => bcrypt('abcd1234'),
                'is_admin' => true,
                'group_id' => null,
            ]
        );

        // Always create or update the tester user
        User::updateOrCreate(
            [
                'email' => 'tester@curhatorium.com',
            ],
            [
                'username' => 'tester',
                'name' => null,
                'password' => bcrypt('tester1234'),
                'is_admin' => false,
                'group_id' => null,
            ]
        );

        // Always create or update a sample SGD group
        \App\Models\SgdGroup::updateOrCreate(
            [
                'title' => 'Sample Group',
            ],
            [
                'topic' => 'Mental Health Awareness',
                'meeting_address' => 'https://www.youtube.com',
                'schedule' => now()->addDays(1),
                'is_done' => false,
                'category' => 'mental-health', // Changed from 'Support' to 'mental-health'
            ]
        );

        // Seed cards
        $this->call(CardSeeder::class);

        // Seed quotes
        $this->call(QuoteSeeder::class);

        // Seed professionals
        $this->call(ProfessionalSeeder::class);

        // Seed stats, weekly stats, and monthly stats
        $this->call([
            StatSeeder::class,
            WeeklyStatSeeder::class,
            MonthlyStatSeeder::class,
        ]);

        // Other seeders
        $this->call(MissionSeeder::class);
        $this->call(MembershipSeeder::class);
    }
}
