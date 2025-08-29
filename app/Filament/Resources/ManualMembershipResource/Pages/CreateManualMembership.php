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
use Filament\Notifications\Notification;

class CreateManualMembership extends CreateRecord
{
    protected static string $resource = ManualMembershipResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            // Validate required fields
            if (empty($data['user_id']) || empty($data['membership_id'])) {
                Notification::make()
                    ->title('Error')
                    ->body('User ID and Membership ID are required.')
                    ->danger()
                    ->send();
                
                $this->halt();
            }

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
                    Notification::make()
                        ->title('Error')
                        ->body('User sudah memiliki langganan aktif.')
                        ->danger()
                        ->send();
                    
                    $this->halt();
                }
            }

            // Return the data for UserMembership creation
            $formData = [
                'user_id' => $user->id,
                'membership_id' => $membership->id,
                'started_at' => $now,
                'expires_at' => $expires ?? $now->copy()->addDays(30), // fallback 30 days
            ];

            // Log the data being created for debugging
            \Log::info('Creating UserMembership', $formData);

            return $formData;
        } catch (\Exception $e) {
            \Log::error('Error in mutateFormDataBeforeCreate', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            Notification::make()
                ->title('Error')
                ->body('An error occurred while processing the request: ' . $e->getMessage())
                ->danger()
                ->send();
            
            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        try {
            // Get the created record
            $userMembership = $this->record;
            
            \Log::info('UserMembership created successfully', [
                'user_membership_id' => $userMembership->id,
                'user_id' => $userMembership->user_id,
                'membership_id' => $userMembership->membership_id,
                'expires_at' => $userMembership->expires_at
            ]);
            
            // Grant tickets
            $tickets = MembershipTicket::where('membership_id', $userMembership->membership_id)->get();
            $ticketsCreated = 0;
            
            \Log::info('Found membership tickets', [
                'membership_id' => $userMembership->membership_id,
                'ticket_count' => $tickets->count()
            ]);
            
            foreach ($tickets as $ticket) {
                // Treat as unlimited if limit_type is 'unlimited' OR limit_value is null
                $isUnlimited = $ticket->limit_type === 'unlimited' || is_null($ticket->limit_value);
                $value = $isUnlimited ? null : $ticket->limit_value;
                
                $userTicket = UserTicket::create([
                    'user_id' => $userMembership->user_id,
                    'ticket_type' => $ticket->ticket_type,
                    'limit_type' => $isUnlimited ? 'unlimited' : $ticket->limit_type,
                    'limit_value' => $isUnlimited ? null : $value,
                    'remaining_value' => $isUnlimited ? null : $value,
                    'expires_at' => $userMembership->expires_at,
                ]);
                
                \Log::info('UserTicket created', [
                    'user_ticket_id' => $userTicket->id,
                    'ticket_type' => $ticket->ticket_type,
                    'limit_type' => $isUnlimited ? 'unlimited' : $ticket->limit_type,
                    'limit_value' => $value
                ]);
                
                $ticketsCreated++;
            }

            // Show success notification
            Notification::make()
                ->title('Membership granted successfully!')
                ->body("Membership '{$userMembership->membership->name}' has been granted to user with {$ticketsCreated} tickets.")
                ->success()
                ->send();
                
            \Log::info('Membership granting completed successfully', [
                'user_membership_id' => $userMembership->id,
                'tickets_created' => $ticketsCreated
            ]);
                
        } catch (\Exception $e) {
            \Log::error('Error in afterCreate', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Notification::make()
                ->title('Warning')
                ->body('Membership was created but there was an issue granting tickets: ' . $e->getMessage())
                ->warning()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Membership granted successfully!';
    }

    protected function getCreatedNotificationBody(): ?string
    {
        $userMembership = $this->record;
        return "Membership '{$userMembership->membership->name}' has been granted to user.";
    }
} 