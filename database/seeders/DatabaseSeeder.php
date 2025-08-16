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

        // Always create or update sample SGD groups in order of oldest to newest by 'schedule'
        \App\Models\SgdGroup::updateOrCreate(
            [
                'title' => 'Curhat #3',
            ],
            [
                'topic' => 'Pengembangan karir dan pekerjaan',
                'meeting_address' => 'https://zoom.us/j/123456789',
                'schedule' => now()->subDays(3),
                'is_done' => true,
                'category' => 'career',
            ]
        );

        \App\Models\SgdGroup::updateOrCreate(
            [
                'title' => 'Curhat #2',
            ],
            [
                'topic' => 'Diskusi seputar pendidikan dan belajar',
                'meeting_address' => 'https://meet.google.com/abc-defg-hij',
                'schedule' => now()->subDays(2),
                'is_done' => true,
                'category' => 'education',
            ]
        );

        \App\Models\SgdGroup::updateOrCreate(
            [
                'title' => 'Curhat #1',
            ],
            [
                'topic' => '',
                'meeting_address' => 'https://www.youtube.com',
                'schedule' => now()->subDays(1),
                'is_done' => true,
                'category' => 'mental-health',
            ]
        );

        \App\Models\SgdGroup::updateOrCreate(
            [
                'title' => 'Curhat #4',
            ],
            [
                'topic' => 'Hubungan dan relasi sosial',
                'meeting_address' => 'https://teams.microsoft.com/l/meetup-join/19%3ameeting',
                'schedule' => now()->subDays(1),
                'is_done' => true,
                'category' => 'relationships',
            ]
        );

        // Seed cards
        $this->call(CardSeeder::class);

        // Seed quotes
        $this->call(QuoteSeeder::class);

        // Seed professionals
        $this->call(ProfessionalSeeder::class);

        // Seed professional authentication credentials
        // $this->call(ProfessionalAuthSeeder::class);

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
