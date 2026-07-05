<?php

namespace App\Observers;

use App\Models\User;
use App\Services\SubscriptionService;

class UserObserver
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    /**
     * Handle the User "created" event.
     *
     * Automatically grants the free membership plan so every
     * new user starts with a subscription and entitlements.
     */
    public function created(User $user): void
    {
        $this->subscriptionService->grantFreePlan($user);
    }
}
