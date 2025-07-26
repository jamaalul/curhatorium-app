<?php

namespace App\Filament\Resources\ManualMembershipResource\Pages;

use App\Filament\Resources\ManualMembershipResource;
use App\Models\User;
use App\Models\Membership;
use App\Models\UserMembership;
use App\Models\MembershipTicket;
use App\Models\UserTicket;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateManualMembership extends CreateRecord
{
    protected static string $resource = ManualMembershipResource::class;

    public function create(bool $another = false): void
    {
        $data = $this->form->getState();
        
        // Get user and membership
        $user = User::findOrFail($data['user_id']);
        $membership = Membership::findOrFail($data['membership_id']);
        
        $now = Carbon::now();
        $expires = $membership->duration_days > 0 ? $now->copy()->addDays($membership->duration_days) : null;

        // Memberships that can be stacked
        $stackable = [
            'Calm Starter',
            'Harmony',
            'Serenity',
            "Chat with Sanny's Aid",
            "Meet with Sanny's Aid",
        ];

        // Prevent multiple active subscriptions except for stackable ones
        if (!in_array($membership->name, $stackable)) {
            $hasActive = UserMembership::where('user_id', $user->id)
                ->whereHas('membership', function($q) use ($stackable) {
                    $q->whereNotIn('name', $stackable);
                })
                ->where('expires_at', '>', $now)
                ->exists();
            if ($hasActive) {
                throw new \Exception('User sudah memiliki langganan aktif.');
            }
        }

        // Create user_membership
        $userMembership = UserMembership::create([
            'user_id' => $user->id,
            'membership_id' => $membership->id,
            'started_at' => $now,
            'expires_at' => $expires ?? $now->copy()->addMonth(), // fallback 1 month
        ]);

        // Grant tickets
        $tickets = MembershipTicket::where('membership_id', $membership->id)->get();
        foreach ($tickets as $ticket) {
            // Treat as unlimited if limit_type is 'unlimited' OR limit_value is null
            $isUnlimited = $ticket->limit_type === 'unlimited' || is_null($ticket->limit_value);
            $value = $isUnlimited ? null : $ticket->limit_value;
            UserTicket::create([
                'user_id' => $user->id,
                'ticket_type' => $ticket->ticket_type,
                'limit_type' => $isUnlimited ? 'unlimited' : $ticket->limit_type,
                'limit_value' => $isUnlimited ? null : $value,
                'remaining_value' => $isUnlimited ? null : $value,
                'expires_at' => $expires ?? $now->copy()->addMonth(),
            ]);
        }

        $this->record = $userMembership;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Membership granted successfully!';
    }
} 