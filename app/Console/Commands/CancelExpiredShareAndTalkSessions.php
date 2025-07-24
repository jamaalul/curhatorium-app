<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ShareAndTalkController;

class CancelExpiredShareAndTalkSessions extends Command
{
    protected $signature = 'share-and-talk:cancel-expired-sessions';
    protected $description = 'Cancel Share and Talk sessions that are still waiting after 5 minutes and return tickets.';

    public function handle()
    {
        $controller = new ShareAndTalkController();
        $controller->cancelExpiredWaitingSessions();
        $this->info('Expired waiting sessions cancelled and tickets returned.');
    }
} 