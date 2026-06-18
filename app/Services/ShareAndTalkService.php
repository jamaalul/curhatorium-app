<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Models\Professional;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                    $nextAvailabilityFormatted = 'Hari ini, '.$slotTime->format('H:i');
                } elseif ($slotTime->isTomorrow()) {
                    $nextAvailabilityFormatted = 'Besok, '.$slotTime->format('H:i');
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
     * Create a new instant chat consultation session
     */
    public function createInstantChatConsultation(int $professionalId, User $user): Consultation
    {
        return DB::transaction(function () use ($professionalId, $user) {
            $professional = Professional::findOrFail($professionalId);

            // Check for existing active session
            $existingSession = $this->findExistingConsultation($user->id, $professional->id, 'chat');

            if ($existingSession) {
                return $existingSession;
            }

            $room = 'sharetalk_'.uniqid().'_'.Str::random(5);
            $fullConsultationType = $professional->type === 'psychiatrist' ? 'Chat w/ Psikolog' : 'Chat w/ Rangers';

            $consultation = Consultation::create([
                'user_id' => $user->id,
                'professional_id' => $professional->id,
                'room' => $room,
                'consultation_type' => $fullConsultationType,
                'status' => 'waiting',
                'start' => now(),
                'end' => now()->addMinutes(65),
                'pending_end' => now()->addMinutes(5),
            ]);

            // Send WhatsApp notification
            $this->sendProfessionalNotification($professional, $room);

            return $consultation;
        });
    }

    /**
     * Create a new video consultation session
     */
    public function createInstantVideoConsultation(int $professionalId, User $user): Consultation
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
            $existingSession = $this->findExistingConsultation($user->id, $professional->id, 'video');

            if ($existingSession) {
                return $existingSession;
            }

            $room = 'sharetalk_video_'.uniqid().'_'.Str::random(5);
            $jitsiRoom = 'curhatorium_video_'.Str::random(16);

            $consultation = Consultation::create([
                'user_id' => $user->id,
                'professional_id' => $professional->id,
                'room' => $room,
                'consultation_type' => 'Video Call w/ Psikolog',
                'status' => 'pending',
                'start' => now(),
                'end' => now()->addMinutes(60),
                'pending_end' => now()->addMinutes(5),
                'jitsi_room' => $jitsiRoom,
            ]);

            // Send WhatsApp notification
            $this->sendVideoNotification($professional, $room);

            return $consultation;
        });
    }

    /**
     * Send a message in a consultation
     */
    public function sendMessage(string $room, string $message, string $senderType = 'user', ?int $senderId = null): ConsultationMessage
    {
        return DB::transaction(function () use ($room, $message, $senderType, $senderId) {
            $consultation = Consultation::where('room', $room)->firstOrFail();

            // Update session status if needed
            if ($consultation->status === 'waiting' && $senderType === 'professional') {
                $consultation->update(['status' => 'active']);
            }

            $morphType = $senderType === 'user' ? User::class : Professional::class;

            return ConsultationMessage::create([
                'sender_id' => $senderId,
                'sender_type' => $morphType,
                'consultation_id' => $consultation->id,
                'message' => $message,
            ]);
        });
    }

    /**
     * Get messages for a consultation
     */
    public function getSessionMessages(string $room): array
    {
        $consultation = Consultation::where('room', $room)->first();
        if (! $consultation) {
            return [];
        }

        return ConsultationMessage::where('consultation_id', $consultation->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get session status
     */
    public function getSessionStatus(string $room): ?array
    {
        $consultation = Consultation::where('room', $room)->first();

        if (! $consultation) {
            return null;
        }

        return [
            'status' => $consultation->status,
            'start' => $consultation->start?->toIso8601String(),
            'end' => $consultation->end?->toIso8601String(),
        ];
    }

    /**
     * Cancel a session by user
     */
    public function cancelSessionByUser(string $room, User $user): bool
    {
        return DB::transaction(function () use ($room) {
            $consultation = Consultation::where('room', $room)->first();

            if (! $consultation || ! in_array($consultation->status, ['waiting', 'pending'])) {
                return false;
            }

            $consultation->status = 'cancelled';
            $consultation->save();

            // Set professional back to online if instant
            if ($consultation->professional) {
                $professional = $consultation->professional;
                $professional->availability = 'online';
                $professional->availabilityText = 'Tersedia';
                $professional->save();
            }

            return true;
        });
    }

    /**
     * Cancel expired waiting sessions
     */
    public function cancelExpiredSessions(): int
    {
        $expiredSessions = Consultation::whereIn('status', ['waiting', 'pending'])
            ->where(function ($query) {
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

        if (! $professional) {
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
    public function cancelSession(string $room, User $user): bool
    {
        return DB::transaction(function () use ($room, $user) {
            $consultation = Consultation::where('room', $room)
                ->where('user_id', $user->id)
                ->whereIn('status', ['waiting', 'pending'])
                ->first();

            if (! $consultation) {
                return false;
            }

            $consultation->status = 'cancelled';
            $consultation->save();

            return true;
        });
    }

    /**
     * End session and award XP
     */
    public function endSession(string $room, User $user): array
    {
        return DB::transaction(function () use ($room, $user) {
            $consultation = Consultation::where('room', $room)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if (! $consultation) {
                return ['success' => false, 'message' => 'Session not found or not active'];
            }

            $consultation->status = 'completed';
            $consultation->end = now();
            $consultation->save();

            // Award XP based on professional type
            $professional = $consultation->professional;
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
                'xp_message' => $xpResult['message'] ?? '',
            ];
        });
    }

    /**
     * Activate session manually
     */
    public function activateSession(string $room): bool
    {
        return DB::transaction(function () use ($room) {
            $consultation = Consultation::where('room', $room)->first();

            if (! $consultation || ! in_array($consultation->status, ['waiting', 'pending'])) {
                return false;
            }

            $consultation->status = 'active';
            $consultation->start = now();
            $consultation->end = now()->addMinutes(65);
            $consultation->pending_end = null;
            $consultation->save();

            return true;
        });
    }

    /**
     * Find existing session
     */
    private function findExistingConsultation(int $userId, int $professionalId, string $type): ?Consultation
    {
        $matchString = $type === 'video' ? 'Video' : 'Chat';

        return Consultation::where('user_id', $userId)
            ->where('professional_id', $professionalId)
            ->where('consultation_type', 'LIKE', "%$matchString%")
            ->whereIn('status', ['waiting', 'pending', 'active'])
            ->first();
    }

    /**
     * Send WhatsApp notification to professional
     */
    private function sendProfessionalNotification(Professional $professional, string $room): void
    {
        try {
            $message =
                "Kamu mendapat pesanan konsultasi baru.\n".
                'Akses URL berikut sebelum '.now()->addMinutes(5)->format('H:i')." agar pesanannya tidak dibatalkan.\n\n".
                'https://curhatorium.com/share-and-talk/activate-session/'.$room;

            Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN'),
            ])->post('https://api.fonnte.com/send', [
                'target' => $professional->whatsapp_number,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp notification: '.$e->getMessage());
        }
    }

    /**
     * Send video consultation notification
     */
    private function sendVideoNotification(Professional $professional, string $room): void
    {
        try {
            $message =
                "Kamu mendapat pesanan konsultasi VIDEO baru.\n".
                'Akses URL berikut sebelum '.now()->addMinutes(5)->format('H:i')." agar pesanannya tidak dibatalkan.\n\n".
                'Dashboard: https://curhatorium.com/share-and-talk/activate-session/'.$room;

            Http::withHeaders([
                'Authorization' => env('FONNTE_TOKEN'),
            ])->post('https://api.fonnte.com/send', [
                'target' => $professional->whatsapp_number,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send video WhatsApp notification: '.$e->getMessage());
        }
    }
}
