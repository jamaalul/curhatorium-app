<?php

namespace App\Services;

use App\Models\Professional;
use App\Models\ChatSession;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShareAndTalkService
{
    /**
     * Get professionals with optional type filtering
     */
    public function getProfessionals(?string $type = null, ?string $date = null): array
    {
        $query = Professional::query();

        if ($type) {
            $query->where('type', $type);
        }

        if ($date) {
            $query->whereHas('scheduleSlots', function ($q) use ($date) {
                $q->where('status', 'available')
                  ->whereDate('slot_start_time', $date);
            });
        }

        $professionals = $query->with(['scheduleSlots' => function ($q) {
            $q->where('status', 'available')
              ->where('slot_start_time', '>=', now())
              ->orderBy('slot_start_time', 'asc');
        }])->get();

        return $professionals->map(function ($professional) {
            $nextSlot = $professional->scheduleSlots->first();
            $nextAvailabilityFormatted = 'Belum ada jadwal tersedia';
            if ($nextSlot) {
                $slotTime = Carbon::parse($nextSlot->slot_start_time)->locale('id');
                if ($slotTime->isToday()) {
                    $nextAvailabilityFormatted = 'Hari ini, ' . $slotTime->format('H:i');
                } elseif ($slotTime->isTomorrow()) {
                    $nextAvailabilityFormatted = 'Besok, ' . $slotTime->format('H:i');
                } else {
                    $nextAvailabilityFormatted = $slotTime->translatedFormat('l, j F, H:i');
                }
            }

            return [
                'id' => $professional->id,
                'name' => $professional->name,
                'title' => $professional->title,
                'avatar' => $professional->avatar,
                'specialties' => $professional->specialties,
                'type' => $professional->type,
                'rating' => $professional->rating,
                'next_availability_formatted' => $nextAvailabilityFormatted,
            ];
        })->toArray();
    }

    /**
     * Create a new chat consultation session
     */
    public function createChatSession(int $professionalId, User $user): ChatSession
    {
        return DB::transaction(function () use ($professionalId, $user) {
            $professional = Professional::findOrFail($professionalId);
            
            // Check for existing active session
            $existingSession = $this->findExistingSession($user->id, $professional->id);
            
            if ($existingSession) {
                return $existingSession;
            }

            $sessionId = Str::uuid();

            $session = ChatSession::create([
                'session_id' => $sessionId,
                'user_id' => $user->id,
                'professional_id' => $professional->id,
                'start' => now(),
                'end' => now()->addMinutes(65),
                'status' => 'waiting',
                'type' => 'chat',
                'pending_end' => now()->addMinutes(5),
            ]);


            // Send WhatsApp notification
            $this->sendProfessionalNotification($professional, $sessionId);

            return $session;
        });
    }

    /**
     * Create a new video consultation session
     */
    public function createVideoSession(int $professionalId, User $user): ChatSession
    {
        return DB::transaction(function () use ($professionalId, $user) {
            $professional = Professional::findOrFail($professionalId);
            
            // Validate professional type
            if ($professional->type !== 'psychiatrist') {
                throw new \Exception('Video consultations are only available with psychiatrists.');
            }
            
            // Check availability
            if ($professional->getEffectiveAvailability() !== 'online') {
                throw new \Exception('This professional is currently not available for video consultations.');
            }
            
            // Check for existing active session
            $existingSession = $this->findExistingVideoSession($user->id, $professional->id);
            
            if ($existingSession) {
                return $existingSession;
            }

            $sessionId = Str::uuid();
            $jitsiRoom = 'curhatorium_video_' . Str::random(16) . '_' . $sessionId;

            $session = ChatSession::create([
                'session_id' => $sessionId,
                'user_id' => $user->id,
                'professional_id' => $professional->id,
                'start' => now(),
                'end' => now()->addMinutes(60),
                'status' => 'pending',
                'type' => 'video',
                'pending_end' => now()->addMinutes(5),
                'jitsi_room' => $jitsiRoom,
            ]);


            // Send WhatsApp notification
            $this->sendVideoNotification($professional, $sessionId);

            return $session;
        });
    }

    /**
     * Send a message in a chat session
     */
    public function sendMessage(string $sessionId, string $message, string $senderType = 'user', ?int $senderId = null): Message
    {
        return DB::transaction(function () use ($sessionId, $message, $senderType, $senderId) {
            $session = ChatSession::where('session_id', $sessionId)->firstOrFail();
            
            // Update session status if needed
            if ($session->status === 'waiting' && $senderType === 'professional') {
                $session->update(['status' => 'active']);
            }

            return Message::create([
                'sender_id' => $senderId,
                'sender_type' => $senderType,
                'session_id' => $sessionId,
                'message' => $message,
            ]);
        });
    }

    /**
     * Get messages for a session
     */
    public function getSessionMessages(string $sessionId): array
    {
        return Message::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get session status
     */
    public function getSessionStatus(string $sessionId): ?array
    {
        $session = ChatSession::where('session_id', $sessionId)->first();
        
        if (!$session) {
            return null;
        }

        return [
            'status' => $session->status,
            'created_at' => $session->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Cancel a session by user
     */
    public function cancelSessionByUser(string $sessionId, User $user): bool
    {
        return DB::transaction(function () use ($sessionId, $user) {
            $session = ChatSession::where('session_id', $sessionId)->first();
            
            if (!$session || !in_array($session->status, ['waiting', 'pending'])) {
                return false;
            }

            $session->status = 'cancelled';
            $session->save();


            // Set professional back to online
            $professional = $session->professional;
            $professional->availability = 'online';
            $professional->availabilityText = 'Tersedia';
            $professional->save();

            return true;
        });
    }

    /**
     * Cancel expired waiting sessions
     */
    public function cancelExpiredSessions(): int
    {
        $expiredSessions = ChatSession::whereIn('status', ['waiting', 'pending'])
            ->where(function($query) {
                $query->where('start', '<', now()->subMinutes(5))
                      ->orWhere('pending_end', '<', now());
            })
            ->get();

        $cancelledCount = 0;
            
        foreach ($expiredSessions as $session) {
            DB::transaction(function () use ($session, &$cancelledCount) {
                $session->status = 'cancelled';
                $session->save();
                

                $cancelledCount++;
            });
        }

        return $cancelledCount;
    }

    /**
     * Set professional online
     */
    public function setProfessionalOnline(int $professionalId): bool
    {
        $professional = Professional::find($professionalId);
        
        if (!$professional) {
            return false;
        }

        $professional->availability = 'online';
        $professional->availabilityText = 'Tersedia';
        $professional->save();

        return true;
    }

    /**
     * Cancel session (for API)
     */
    public function cancelSession(string $sessionId, User $user): bool
    {
        return DB::transaction(function () use ($sessionId, $user) {
            $session = ChatSession::where('session_id', $sessionId)
                ->where('user_id', $user->id)
                ->whereIn('status', ['waiting', 'pending'])
                ->first();

            if (!$session) {
                return false;
            }

            $session->status = 'cancelled';
            $session->save();


            return true;
        });
    }

    /**
     * End session and award XP
     */
    public function endSession(string $sessionId, User $user): array
    {
        return DB::transaction(function () use ($sessionId, $user) {
            $session = ChatSession::where('session_id', $sessionId)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if (!$session) {
                return ['success' => false, 'message' => 'Session not found or not active'];
            }

            $session->status = 'completed';
            $session->end = now();
            $session->save();

            // Award XP based on professional type
            $professional = $session->professional;
            $xpResult = ['xp_awarded' => 0, 'message' => ''];
            
            if ($professional) {
                if ($professional->type === 'psychiatrist') {
                    $xpResult = $user->awardXp('share_talk_psychiatrist');
                } else {
                    $xpResult = $user->awardXp('share_talk_ranger');
                }
            }

            return [
                'success' => true,
                'message' => 'Session ended successfully.',
                'xp_awarded' => $xpResult['xp_awarded'] ?? 0,
                'xp_message' => $xpResult['message'] ?? ''
            ];
        });
    }

    /**
     * Activate session manually
     */
    public function activateSession(string $sessionId): bool
    {
        return DB::transaction(function () use ($sessionId) {
            $session = ChatSession::where('session_id', $sessionId)->first();
            
            if (!$session || !in_array($session->status, ['waiting', 'pending'])) {
                return false;
            }

            $session->status = 'active';
            $session->start = now();
            $session->end = now()->addMinutes(65);
            $session->pending_end = null;
            $session->save();

            return true;
        });
    }

    /**
     * Find existing session
     */
    private function findExistingSession(int $userId, int $professionalId): ?ChatSession
    {
        return ChatSession::where('user_id', $userId)
            ->where('professional_id', $professionalId)
            ->whereIn('status', ['waiting', 'pending', 'active'])
            ->first();
    }

    /**
     * Find existing video session
     */
    private function findExistingVideoSession(int $userId, int $professionalId): ?ChatSession
    {
        return ChatSession::where('user_id', $userId)
            ->where('professional_id', $professionalId)
            ->where('type', 'video')
            ->whereIn('status', ['waiting', 'pending', 'active'])
            ->first();
    }

    /**
     * Send WhatsApp notification to professional
     */
    private function sendProfessionalNotification(Professional $professional, string $sessionId): void
    {
        try {
            $message = 
                "Kamu mendapat pesanan konsultasi baru.\n" .
                "Akses URL berikut sebelum " . now()->addMinutes(5)->format('H:i') . " agar pesanannya tidak dibatalkan.\n\n" .
                "https://curhatorium.com/share-and-talk/activate-session/" . $sessionId;

            Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN'),
            ])->post('https://api.fonnte.com/send', [
                'target' => $professional->whatsapp_number,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp notification: ' . $e->getMessage());
        }
    }

    /**
     * Send video consultation notification
     */
    private function sendVideoNotification(Professional $professional, string $sessionId): void
    {
        try {
            $message = 
                "Kamu mendapat pesanan konsultasi VIDEO baru.\n" .
                "Akses URL berikut sebelum " . now()->addMinutes(5)->format('H:i') . " agar pesanannya tidak dibatalkan.\n\n" .
                "Dashboard: https://curhatorium.com/share-and-talk/activate-session/" . $sessionId;

            Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN'),
            ])->post('https://api.fonnte.com/send', [
                'target' => $professional->whatsapp_number,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send video WhatsApp notification: ' . $e->getMessage());
        }
    }
} 