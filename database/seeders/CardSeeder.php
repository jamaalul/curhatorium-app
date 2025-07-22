<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Card;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cards = [
            [
                'content' => 'Take a deep breath and relax.',
                'category' => 'Mindfulness',
            ],
            [
                'content' => 'Write down three things you are grateful for.',
                'category' => 'Gratitude',
            ],
            [
                'content' => 'Go for a short walk outside.',
                'category' => 'Physical',
            ],
            [
                'content' => 'Send a kind message to a friend.',
                'category' => 'Social',
            ],
            [
                'content' => 'Read a page from your favorite book.',
                'category' => 'Leisure',
            ],
        ];

        foreach ($cards as $card) {
            Card::create($card);
        }
    }
} 