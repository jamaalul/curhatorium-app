<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class GrantFreeSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:grant-free';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant free membership subscriptions to users who do not have an active subscription';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptionService)
    {
        $this->info('Finding users without an active subscription...');

        $usersCount = 0;

        User::doesntHave('subscription')
            ->chunkById(100, function ($users) use ($subscriptionService, &$usersCount) {
                foreach ($users as $user) {
                    $subscriptionService->grantFreePlan($user);
                    $usersCount++;
                }
            });

        $this->info("Granted free subscriptions to {$usersCount} users.");
    }
}
