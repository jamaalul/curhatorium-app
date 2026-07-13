<?php

namespace App\Console\Commands;

use App\Events\SessionForceEnded;
use App\Models\Consultation;
use App\Models\ConsultationMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanUpActiveShareTalkSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'share-talk:cleanup-active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up active Share and Talk sessions that have passed their end time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of expired active sessions...');

        $expiredSessions = Consultation::with(['user', 'professional', 'professionalScheduleSlot'])
            ->whereIn('status', ['active', 'waiting'])
            ->where(function ($query) {
                $query->where('end', '<', now())
                    ->orWhereNull('end');
            })
            ->get();

        $this->info("Found {$expiredSessions->count()} expired active sessions to clean up.");

        foreach ($expiredSessions as $session) {
            $this->info("Processing session: {$session->room}");

            DB::transaction(function () use ($session) {
                // Update Consultation status
                $session->status = 'completed';
                $session->save();

                // Update ProfessionalScheduleSlot status
                if ($session->professionalScheduleSlot) {
                    $session->professionalScheduleSlot->status = 'completed';
                    $session->professionalScheduleSlot->save();
                }

                // Cleanup ConsultationMessage
                ConsultationMessage::where('consultation_id', $session->id)->delete();
            });

            // Award XP to user if applicable
            $user = $session->user;
            $professional = $session->professional;

            if ($user && $professional) {
                try {
                    if ($professional->type === 'psychiatrist') {
                        $user->awardXp('share_talk_psychiatrist');
                    } else {
                        $user->awardXp('share_talk_ranger');
                    }
                    $this->info("  - Awarded XP to user {$user->name}");
                } catch (\Exception $e) {
                    $this->error('  - Failed to award XP: '.$e->getMessage());
                }
            }

            SessionForceEnded::dispatch($session->room);

            $this->info("  - Cleaned up session {$session->room}");
        }

        $this->info('Cleanup completed successfully!');

        return self::SUCCESS;
    }
}
