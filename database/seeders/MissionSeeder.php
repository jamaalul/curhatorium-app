<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mission;

class MissionSeeder extends Seeder
{
    public function run()
    {
        $missions = [
            // Easy
            [
                'title' => 'Take 5 Deep Breaths',
                'description' => 'Practice mindful breathing for 2 minutes to reduce stress and center yourself.',
                'points' => 10,
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Drink 8 Glasses of Water',
                'description' => 'Stay hydrated throughout the day to maintain physical and mental energy.',
                'points' => 10,
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Write 3 Things You\'re Grateful For',
                'description' => 'Practice gratitude by noting three positive things from your day.',
                'points' => 10,
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Take a 10-Minute Walk',
                'description' => 'Get some fresh air and light exercise to boost your mood and energy.',
                'points' => 10,
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Compliment Someone',
                'description' => 'Spread positivity by giving a genuine compliment to someone today.',
                'points' => 10,
                'difficulty' => 'easy',
            ],
            // Medium
            [
                'title' => '15-Minute Meditation',
                'description' => 'Practice guided meditation to improve focus and reduce anxiety.',
                'points' => 20,
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Journal for 10 Minutes',
                'description' => 'Reflect on your thoughts and emotions through written expression.',
                'points' => 20,
                'difficulty' => 'medium',
            ],
            [
                'title' => '30-Minute Exercise',
                'description' => 'Engage in physical activity to release endorphins and improve mood.',
                'points' => 20,
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Call a Friend or Family Member',
                'description' => 'Strengthen social connections by reaching out to someone you care about.',
                'points' => 20,
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Learn Something New',
                'description' => 'Spend 20 minutes learning a new skill or reading about a topic of interest.',
                'points' => 20,
                'difficulty' => 'medium',
            ],
            // Hard
            [
                'title' => 'Face a Fear',
                'description' => 'Take a small step towards overcoming something that makes you anxious.',
                'points' => 40,
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Have a Difficult Conversation',
                'description' => 'Address an important issue you\'ve been avoiding with someone close to you.',
                'points' => 40,
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Digital Detox for 3 Hours',
                'description' => 'Disconnect from all digital devices and focus on offline activities.',
                'points' => 40,
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Practice Vulnerability',
                'description' => 'Share something personal or meaningful with someone you trust.',
                'points' => 40,
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Set a Challenging Goal',
                'description' => 'Define a specific, measurable goal that pushes you out of your comfort zone.',
                'points' => 40,
                'difficulty' => 'hard',
            ],
        ];

        foreach ($missions as $mission) {
            Mission::create($mission);
        }
    }
} 