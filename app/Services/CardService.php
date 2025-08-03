<?php

namespace App\Services;

use App\Models\Card;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CardService
{
    /**
     * Get all available cards
     */
    public function getAllCards(): array
    {
        return Card::orderBy('created_at', 'desc')->get()->toArray();
    }

    /**
     * Get card by ID
     */
    public function getCard(int $id): ?array
    {
        $card = Card::find($id);
        return $card ? $card->toArray() : null;
    }

    /**
     * Get random card
     */
    public function getRandomCard(): ?array
    {
        $card = Card::inRandomOrder()->first();
        return $card ? $card->toArray() : null;
    }

    /**
     * Get cards by category
     */
    public function getCardsByCategory(string $category): array
    {
        return Card::where('category', $category)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get cards by difficulty level
     */
    public function getCardsByDifficulty(string $difficulty): array
    {
        return Card::where('difficulty', $difficulty)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get recommended cards for user
     */
    public function getRecommendedCards(User $user, int $limit = 5): array
    {
        // Get user's XP level to determine appropriate difficulty
        $userXp = $user->total_xp;
        
        $difficulty = match (true) {
            $userXp < 100 => 'beginner',
            $userXp < 500 => 'intermediate',
            $userXp < 1000 => 'advanced',
            default => 'expert'
        };

        return Card::where('difficulty', $difficulty)
            ->orWhere('difficulty', 'beginner') // Always include beginner cards
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Process card interaction and award XP
     */
    public function processCardInteraction(User $user, int $cardId, string $interactionType = 'view'): array
    {
        return DB::transaction(function () use ($user, $cardId, $interactionType) {
            $card = Card::find($cardId);
            
            if (!$card) {
                return [
                    'success' => false,
                    'message' => 'Card not found.'
                ];
            }

            Log::info('Card interaction processed', [
                'user_id' => $user->id,
                'card_id' => $cardId,
                'interaction_type' => $interactionType,
                'card_title' => $card->title
            ]);

            // Award XP based on interaction type
            $xpActivity = match ($interactionType) {
                'view' => 'deep_cards_view',
                'complete' => 'deep_cards_complete',
                'share' => 'deep_cards_share',
                default => 'deep_cards_interaction'
            };

            $xpResult = $user->awardXp($xpActivity);

            return [
                'success' => true,
                'card' => $card->toArray(),
                'xp_awarded' => $xpResult['xp_awarded'] ?? 0,
                'xp_message' => $xpResult['message'] ?? ''
            ];
        });
    }

    /**
     * Get card statistics
     */
    public function getCardStats(): array
    {
        $totalCards = Card::count();
        $cardsByCategory = Card::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get()
            ->toArray();
        
        $cardsByDifficulty = Card::selectRaw('difficulty, COUNT(*) as count')
            ->groupBy('difficulty')
            ->get()
            ->toArray();

        return [
            'total_cards' => $totalCards,
            'by_category' => $cardsByCategory,
            'by_difficulty' => $cardsByDifficulty
        ];
    }

    /**
     * Search cards by keyword
     */
    public function searchCards(string $keyword): array
    {
        return Card::where('title', 'LIKE', "%{$keyword}%")
            ->orWhere('content', 'LIKE', "%{$keyword}%")
            ->orWhere('category', 'LIKE', "%{$keyword}%")
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get daily card
     */
    public function getDailyCard(): ?array
    {
        // Use today's date as seed for consistent daily card
        $today = now()->format('Y-m-d');
        $seed = crc32($today);
        
        $card = Card::inRandomOrder($seed)->first();
        return $card ? $card->toArray() : null;
    }

    /**
     * Get card of the day for specific date
     */
    public function getCardOfTheDay(string $date): ?array
    {
        $seed = crc32($date);
        $card = Card::inRandomOrder($seed)->first();
        return $card ? $card->toArray() : null;
    }

    /**
     * Get trending cards (most viewed/interacted)
     */
    public function getTrendingCards(int $limit = 10): array
    {
        // This would require tracking card interactions
        // For now, return random cards
        return Card::inRandomOrder()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get cards for specific mood
     */
    public function getCardsForMood(string $mood): array
    {
        $moodCategories = match ($mood) {
            'happy', 'joyful', 'excited' => ['gratitude', 'celebration', 'positivity'],
            'sad', 'depressed', 'lonely' => ['comfort', 'hope', 'connection'],
            'anxious', 'stressed', 'worried' => ['calm', 'mindfulness', 'relaxation'],
            'angry', 'frustrated' => ['forgiveness', 'patience', 'understanding'],
            'confused', 'lost' => ['guidance', 'clarity', 'direction'],
            default => ['general', 'reflection']
        };

        return Card::whereIn('category', $moodCategories)
            ->inRandomOrder()
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Get card completion progress for user
     */
    public function getUserCardProgress(User $user): array
    {
        // This would require tracking which cards user has completed
        // For now, return basic stats
        $totalCards = Card::count();
        
        return [
            'total_cards' => $totalCards,
            'completed_cards' => 0, // Would need to track this
            'completion_percentage' => 0,
            'favorite_category' => 'general',
            'last_completed' => null
        ];
    }

    /**
     * Get card categories
     */
    public function getCardCategories(): array
    {
        return Card::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();
    }

    /**
     * Get card difficulties
     */
    public function getCardDifficulties(): array
    {
        return Card::select('difficulty')
            ->distinct()
            ->orderBy('difficulty')
            ->pluck('difficulty')
            ->toArray();
    }

    /**
     * Get cards for meditation session
     */
    public function getMeditationCards(int $sessionLength = 10): array
    {
        $cardCount = match (true) {
            $sessionLength <= 5 => 1,
            $sessionLength <= 10 => 2,
            $sessionLength <= 20 => 3,
            default => 4
        };

        return Card::where('category', 'meditation')
            ->orWhere('category', 'mindfulness')
            ->inRandomOrder()
            ->limit($cardCount)
            ->get()
            ->toArray();
    }

    /**
     * Get cards for journaling prompts
     */
    public function getJournalingCards(): array
    {
        return Card::where('category', 'journaling')
            ->orWhere('category', 'reflection')
            ->orWhere('category', 'self-discovery')
            ->inRandomOrder()
            ->limit(3)
            ->get()
            ->toArray();
    }
} 