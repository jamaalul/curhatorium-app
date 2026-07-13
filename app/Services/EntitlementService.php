<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserEntitlement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntitlementService
{
    /**
     * Check if user has an available entitlement for a specific benefit.
     */
    public function hasAvailableEntitlement(User $user, string $benefit, int $amount = 1): bool
    {
        return $this->getAvailableEntitlementQuery($user, $benefit)
            ->where(function ($query) use ($amount) {
                $query->where('amount_total', -1) // Unlimited
                    ->orWhereRaw('amount_total - amount_used >= ?', [$amount]);
            })
            ->exists();
    }

    /**
     * Get the total available amount for an entitlement
     */
    public function getAvailableEntitlementAmount(User $user, string $benefit)
    {
        $entitlements = $this->getAvailableEntitlementQuery($user, $benefit)->get();

        $total = 0;
        foreach ($entitlements as $entitlement) {
            if ($entitlement->amount_total == -1) {
                return 'Tak Terbatas';
            }
            $total += max(0, $entitlement->amount_total - $entitlement->amount_used);
        }

        return $total;
    }

    /**
     * Consume an entitlement amount for a user.
     */
    public function consumeEntitlement(User $user, string $benefit, int $amount = 1): bool
    {
        return DB::transaction(function () use ($user, $benefit, $amount) {
            $entitlement = $this->getAvailableEntitlementQuery($user, $benefit)
                ->where(function ($query) use ($amount) {
                    $query->where('amount_total', -1)
                        ->orWhereRaw('amount_total - amount_used >= ?', [$amount]);
                })
                ->lockForUpdate()
                ->first();

            if (! $entitlement) {
                Log::warning('Entitlement consumption failed: No valid entitlement found', [
                    'user_id' => $user->id,
                    'benefit' => $benefit,
                    'amount' => $amount,
                ]);

                return false;
            }

            if ($entitlement->amount_total !== -1) {
                $entitlement->amount_used += $amount;
                $entitlement->save();
            }

            Log::info('Entitlement consumed', [
                'user_id' => $user->id,
                'benefit' => $benefit,
                'amount' => $amount,
                'entitlement_id' => $entitlement->id,
            ]);

            return true;
        });
    }

    /**
     * Base query to get an active entitlement for a benefit.
     */
    private function getAvailableEntitlementQuery(User $user, string $benefit)
    {
        $now = Carbon::now();

        return UserEntitlement::where('user_id', $user->id)
            ->where('benefit', $benefit)
            ->where('period_start', '<=', $now)
            ->where('period_end', '>=', $now);
    }

    /**
     * Refund an entitlement amount for a user.
     */
    public function refundEntitlement(User $user, string $benefit, int $amount = 1): bool
    {
        return DB::transaction(function () use ($user, $benefit, $amount) {
            $entitlement = UserEntitlement::where('user_id', $user->id)
                ->where('benefit', $benefit)
                ->where(function ($query) use ($amount) {
                    $query->where('amount_total', -1)
                        ->orWhere('amount_used', '>=', $amount);
                })
                ->where('period_end', '>=', Carbon::now())
                ->orderBy('period_end', 'desc')
                ->lockForUpdate()
                ->first();

            if (! $entitlement) {
                Log::warning('Entitlement refund failed: No valid entitlement found', [
                    'user_id' => $user->id,
                    'benefit' => $benefit,
                    'amount' => $amount,
                ]);

                return false;
            }

            if ($entitlement->amount_total !== -1) {
                $entitlement->amount_used -= $amount;
                $entitlement->save();
            }

            Log::info('Entitlement refunded', [
                'user_id' => $user->id,
                'benefit' => $benefit,
                'amount' => $amount,
                'entitlement_id' => $entitlement->id,
            ]);

            return true;
        });
    }
}
