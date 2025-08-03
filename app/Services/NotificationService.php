<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    /**
     * Send welcome email to new user
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            Log::info('Sending welcome email', ['user_id' => $user->id, 'email' => $user->email]);
            
            // You can implement actual email sending here
            // For now, we'll just log the action
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send membership purchase confirmation
     */
    public function sendMembershipConfirmation(User $user, array $membershipData): bool
    {
        try {
            Log::info('Sending membership confirmation', [
                'user_id' => $user->id,
                'membership_name' => $membershipData['membership']['name'] ?? 'Unknown'
            ]);
            
            // You can implement actual email sending here
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send membership confirmation', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send ticket expiration reminder
     */
    public function sendTicketExpirationReminder(User $user, array $ticketData): bool
    {
        try {
            Log::info('Sending ticket expiration reminder', [
                'user_id' => $user->id,
                'ticket_type' => $ticketData['ticket_type'] ?? 'Unknown',
                'expires_at' => $ticketData['expires_at'] ?? 'Unknown'
            ]);
            
            // You can implement actual email sending here
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send ticket expiration reminder', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send membership expiration reminder
     */
    public function sendMembershipExpirationReminder(User $user, array $membershipData): bool
    {
        try {
            Log::info('Sending membership expiration reminder', [
                'user_id' => $user->id,
                'membership_name' => $membershipData['membership']['name'] ?? 'Unknown',
                'expires_at' => $membershipData['expires_at'] ?? 'Unknown'
            ]);
            
            // You can implement actual email sending here
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send membership expiration reminder', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send WhatsApp notification (if configured)
     */
    public function sendWhatsAppNotification(string $phoneNumber, string $message): bool
    {
        try {
            $apiKey = env('WHATSAPP_API_KEY');
            $apiUrl = env('WHATSAPP_API_URL');
            
            if (!$apiKey || !$apiUrl) {
                Log::warning('WhatsApp API not configured');
                return false;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post($apiUrl, [
                'phone' => $phoneNumber,
                'message' => $message
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp notification sent successfully', [
                    'phone' => $phoneNumber,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('WhatsApp API error', [
                    'phone' => $phoneNumber,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp notification', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send session reminder notification
     */
    public function sendSessionReminder(User $user, array $sessionData): bool
    {
        try {
            Log::info('Sending session reminder', [
                'user_id' => $user->id,
                'session_type' => $sessionData['type'] ?? 'Unknown',
                'session_time' => $sessionData['scheduled_at'] ?? 'Unknown'
            ]);
            
            // Send email notification
            $emailSent = $this->sendSessionReminderEmail($user, $sessionData);
            
            // Send WhatsApp notification if phone number is available
            $whatsappSent = false;
            if ($user->phone_number) {
                $message = $this->formatSessionReminderMessage($sessionData);
                $whatsappSent = $this->sendWhatsAppNotification($user->phone_number, $message);
            }
            
            return $emailSent || $whatsappSent;
        } catch (\Exception $e) {
            Log::error('Failed to send session reminder', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send XP milestone notification
     */
    public function sendXpMilestoneNotification(User $user, int $xpGained, int $totalXp): bool
    {
        try {
            Log::info('Sending XP milestone notification', [
                'user_id' => $user->id,
                'xp_gained' => $xpGained,
                'total_xp' => $totalXp
            ]);
            
            // You can implement actual notification sending here
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send XP milestone notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send daily tracker reminder
     */
    public function sendDailyTrackerReminder(User $user): bool
    {
        try {
            Log::info('Sending daily tracker reminder', ['user_id' => $user->id]);
            
            // You can implement actual notification sending here
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send daily tracker reminder', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send mission completion notification
     */
    public function sendMissionCompletionNotification(User $user, array $missionData): bool
    {
        try {
            Log::info('Sending mission completion notification', [
                'user_id' => $user->id,
                'mission_name' => $missionData['name'] ?? 'Unknown'
            ]);
            
            // You can implement actual notification sending here
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send mission completion notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send session reminder email
     */
    private function sendSessionReminderEmail(User $user, array $sessionData): bool
    {
        try {
            // You can implement actual email sending here
            // For now, we'll just log the action
            Log::info('Session reminder email would be sent', [
                'user_id' => $user->id,
                'email' => $user->email,
                'session_data' => $sessionData
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send session reminder email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Format session reminder message for WhatsApp
     */
    private function formatSessionReminderMessage(array $sessionData): string
    {
        $sessionType = $sessionData['type'] ?? 'session';
        $scheduledTime = $sessionData['scheduled_at'] ?? 'soon';
        $professionalName = $sessionData['professional_name'] ?? 'professional';
        
        return "Hai! Jangan lupa kamu punya {$sessionType} dengan {$professionalName} pada {$scheduledTime}. Siapkan dirimu ya! 😊";
    }

    /**
     * Send bulk notifications to multiple users
     */
    public function sendBulkNotification(array $userIds, string $message, string $type = 'general'): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($userIds as $userId) {
            try {
                $user = User::find($userId);
                if (!$user) {
                    $results['failed']++;
                    $results['errors'][] = "User not found: {$userId}";
                    continue;
                }

                $success = match ($type) {
                    'email' => $this->sendEmailNotification($user, $message),
                    'whatsapp' => $this->sendWhatsAppNotification($user->phone_number, $message),
                    default => $this->sendGeneralNotification($user, $message)
                };

                if ($success) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Failed to send notification to user: {$userId}";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Error sending notification to user {$userId}: " . $e->getMessage();
            }
        }

        Log::info('Bulk notification completed', $results);
        return $results;
    }

    /**
     * Send general notification
     */
    private function sendGeneralNotification(User $user, string $message): bool
    {
        try {
            Log::info('Sending general notification', [
                'user_id' => $user->id,
                'message' => $message
            ]);
            
            // You can implement actual notification sending here
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send general notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(User $user, string $message): bool
    {
        try {
            Log::info('Sending email notification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'message' => $message
            ]);
            
            // You can implement actual email sending here
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
} 