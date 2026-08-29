<?php

namespace App\Services;

use App\Exceptions\AiQuotaExceededException;
use App\Exceptions\AiSubscriptionRequiredException;
use App\Models\AiWindow;
use App\Models\User;
use App\Models\UserEntitlement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiTokenWindowService
{
    /**
     * Resolve the active AI window and entitlement for the given user,
     * creating a new window if none exists. Throws if the user has no
     * active subscription, no entitlement, or has exceeded their quota.
     *
     * @return array{window: AiWindow, entitlement: UserEntitlement}
     */
    public function resolveWindowOrFail(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $now = now();
            $hasActiveEntitlement = UserEntitlement::query()
                ->where('user_id', $user->id)
                ->where('benefit', 'ai_window_token')
                ->where('period_start', '<=', $now)
                ->where('period_end', '>=', $now)
                ->exists();

            if (! $user->subscription || ! $hasActiveEntitlement) {
                app(SubscriptionService::class)->grantFreePlan($user);
                $user->unsetRelation('subscription');
            }

            $window = AiWindow::activeForUser($user->id)
                ->lockForUpdate()
                ->first();

            if (! $window) {
                $window = $this->createWindowForUser($user);
            }

            $entitlement = $this->loadActiveEntitlement($user);

            if ($entitlement->amount_total !== -1 && $window->tokens_used >= $entitlement->amount_total) {
                throw new AiQuotaExceededException($window->ends_at);
            }

            return ['window' => $window, 'entitlement' => $entitlement];
        });
    }

    /**
     * Record token usage on the given AI window.
     * Uses a transaction with row locking for concurrency safety.
     */
    public function recordTokenUsage(AiWindow $window, int $promptTokens, int $completionTokens): void
    {
        $totalTokens = $promptTokens + $completionTokens;

        if ($totalTokens <= 0) {
            return;
        }

        DB::transaction(function () use ($window, $totalTokens, $promptTokens, $completionTokens) {
            $lockedWindow = AiWindow::lockForUpdate()->find($window->id);

            if (! $lockedWindow) {
                Log::warning('AiWindow not found during token recording', [
                    'window_id' => $window->id,
                ]);

                return;
            }

            $lockedWindow->tokens_used += $totalTokens;
            $lockedWindow->save();

            Log::info('AI token usage recorded', [
                'window_id' => $lockedWindow->id,
                'user_id' => $lockedWindow->user_id,
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens_used' => $lockedWindow->tokens_used,
            ]);
        });
    }

    /**
     * Create a new AI window for the user based on their membership plan.
     */
    private function createWindowForUser(User $user): AiWindow
    {
        $subscription = $user->subscription;

        if (! $subscription) {
            throw new AiSubscriptionRequiredException;
        }

        $membershipPlan = $subscription->membershipPlan;

        if (! $membershipPlan) {
            throw new AiSubscriptionRequiredException('Paket langganan kamu tidak valid.');
        }

        $now = now();

        return AiWindow::create([
            'user_id' => $user->id,
            'starts_at' => $now,
            'ends_at' => $now->copy()->addHours($membershipPlan->ai_window_hours),
            'tokens_used' => 0,
        ]);
    }

    /**
     * Load the user's active ai_window_token entitlement.
     */
    private function loadActiveEntitlement(User $user): UserEntitlement
    {
        $now = now();

        $entitlement = UserEntitlement::query()
            ->where('user_id', $user->id)
            ->where('benefit', 'ai_window_token')
            ->where('period_start', '<=', $now)
            ->where('period_end', '>=', $now)
            ->first();

        if (! $entitlement) {
            throw new AiSubscriptionRequiredException('Kamu tidak memiliki entitlement token AI yang aktif.');
        }

        return $entitlement;
    }
}
