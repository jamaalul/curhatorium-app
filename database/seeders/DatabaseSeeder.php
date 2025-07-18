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
                'category' => 'Support',
            ]
        );

        // Seed sample quotes
        \App\Models\Quote::insert([
            ['quote' => 'The only way to do great work is to love what you do.'],
            ['quote' => 'Success is not the key to happiness. Happiness is the key to success.'],
            ['quote' => 'Believe you can and you’re halfway there.'],
            ['quote' => 'Your limitation—it’s only your imagination.'],
            ['quote' => 'Push yourself, because no one else is going to do it for you.'],
        ]);

        // Seed stats, weekly stats, and monthly stats
        $this->call([
            StatSeeder::class,
            WeeklyStatSeeder::class,
            MonthlyStatSeeder::class,
        ]);
    }
}
