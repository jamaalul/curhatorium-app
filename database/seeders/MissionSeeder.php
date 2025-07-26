<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mission;

class MissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $missions = [
            // Easy missions (2 points)
            [
                'title' => 'Tulis 3 hal yang kamu syukuri hari ini.',
                'description' => 'Practice gratitude by noting three positive things from your day.',
                'points' => 2,
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Minum 1 gelas air putih saat bangun tidur.',
                'description' => 'Stay hydrated to start your day right.',
                'points' => 2,
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Dengarkan 1 lagu yang membuatmu merasa tenang.',
                'description' => 'Find peace through music.',
                'points' => 2,
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Tarik napas dalam-dalam dan buang secara perlahan selama 3 kali.',
                'description' => 'Practice mindful breathing to reduce stress.',
                'points' => 2,
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Lihat pemandangan di luar jendela selama 1 menit.',
                'description' => 'Take a moment to appreciate the world around you.',
                'points' => 2,
                'difficulty' => 'easy',
            ],
            // Medium missions (5 points)
            [
                'title' => 'Tulis jurnal mengenai apa yang kamu rasakan hari ini.',
                'description' => 'Reflect on your thoughts and emotions through written expression.',
                'points' => 5,
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Lakukan teknik grounding 5-4-3-2-1.',
                'description' => 'Practice grounding technique to stay present.',
                'points' => 5,
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Bermeditasi minimal 10 menit.',
                'description' => 'Practice guided meditation to improve focus and reduce anxiety.',
                'points' => 5,
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Lakukan detoks media sosial selama 6 jam.',
                'description' => 'Take a break from social media to focus on yourself.',
                'points' => 5,
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Lakukan olahraga ringan selama 15–20 menit.',
                'description' => 'Engage in physical activity to release endorphins and improve mood.',
                'points' => 5,
                'difficulty' => 'medium',
            ],
            // Hard missions (10 points)
            [
                'title' => 'Ikuti 1 sesi SGD atau Share and Talk Curhatorium.',
                'description' => 'Join a support group discussion to connect with others.',
                'points' => 10,
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Hadapi satu ketakutan kecil yang selama ini kamu hindari.',
                'description' => 'Take a small step towards overcoming something that makes you anxious.',
                'points' => 10,
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Lakukan deep talk dengan satu orang.',
                'description' => 'Have a meaningful conversation with someone you trust.',
                'points' => 10,
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Jogging selama 30 menit.',
                'description' => 'Challenge yourself with a longer physical activity.',
                'points' => 10,
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Ambil keputusan besar yang selama ini ditunda karena takut gagal.',
                'description' => 'Face your fears and make that important decision you\'ve been avoiding.',
                'points' => 10,
                'difficulty' => 'hard',
            ],
        ];

        // Delete old data
        Mission::truncate();

        foreach ($missions as $mission) {
            Mission::create($mission);
        }
    }
} 