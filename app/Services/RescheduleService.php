<?php

namespace App\Services;

use App\Models\Reschedule;
use App\Models\RescheduleSlot;
use App\Models\ProfessionalScheduleSlot;
use App\Models\Consultation;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RescheduleService
{
    /**
     * Create a reschedule offer for a consultation
     *
     * @param ProfessionalScheduleSlot $originalSlot
     * @param array $offeredSlotIds
     * @param string|null $notes
     * @return Reschedule
     */
    public function createRescheduleOffer(ProfessionalScheduleSlot $originalSlot, array $offeredSlotIds, ?string $notes = null)
    {
        // Create the reschedule record
        $token = $this->generateSecureToken();
        $expiresAt = Carbon::now()->addHours(48); // 48 hours validity

        $consultation = $originalSlot->consultation;

        $reschedule = Reschedule::create([
            'consultation_id' => $consultation->id,
            'original_slot_id' => $originalSlot->id,
            'status' => 'pending',
            'token' => $token,
            'expires_at' => $expiresAt,
            'notes' => $notes,
        ]);

        // Add offered slots
        foreach ($offeredSlotIds as $slotId) {
            RescheduleSlot::create([
                'reschedule_id' => $reschedule->id,
                'professional_schedule_slot_id' => $slotId,
                'is_selected' => false,
            ]);
        }

        return $reschedule;
    }

    /**
     * Update an existing reschedule offer with offered slots
     *
     * @param Reschedule $reschedule
     * @param array $offeredSlotIds
     * @param string|null $notes
     * @return Reschedule
     */
    public function updateRescheduleOffer(Reschedule $reschedule, array $offeredSlotIds, ?string $notes = null)
    {
        // Update notes if provided
        if ($notes !== null) {
            $reschedule->notes = $notes;
            $reschedule->save();
        }

        // Remove existing offered slots
        RescheduleSlot::where('reschedule_id', $reschedule->id)->delete();

        // Add new offered slots
        foreach ($offeredSlotIds as $slotId) {
            RescheduleSlot::create([
                'reschedule_id' => $reschedule->id,
                'professional_schedule_slot_id' => $slotId,
                'is_selected' => false,
            ]);
        }

        return $reschedule;
    }

    /**
     * Generate a secure random token
     *
     * @return string
     */
    public function generateSecureToken()
    {
        return Str::random(64);
    }

    /**
     * Check if a token is valid
     *
     * @param Reschedule $reschedule
     * @param string $token
     * @return bool
     */
    public function isTokenValid(Reschedule $reschedule, string $token)
    {
        if ($reschedule->token !== $token) {
            return false;
        }

        if ($reschedule->status !== 'pending') {
            return false;
        }

        if (Carbon::now()->isAfter(\Carbon\Carbon::parse($reschedule->expires_at))) {
            return false;
        }

        return true;
    }

    /**
     * Handle a client's response to a reschedule offer
     *
     * @param Reschedule $reschedule
     * @param string $selectedSlotId
     * @param string $action
     * @return bool
     */
    public function handleClientResponse(Reschedule $reschedule, string $selectedSlotId, string $action)
    {
        if ($action === 'accept') {
            // Find the selected slot
            $rescheduleSlot = RescheduleSlot::where('reschedule_id', $reschedule->id)
                ->where('professional_schedule_slot_id', $selectedSlotId)
                ->first();

            if (!$rescheduleSlot) {
                return false;
            }

            // Mark the selected slot
            $rescheduleSlot->is_selected = true;
            $rescheduleSlot->save();

            // Update the consultation to use the new slot
            $consultation = $reschedule->consultation;
            $consultation->professional_schedule_slot_id = $selectedSlotId;
            $consultation->save();

            // Mark the original slot as available
            $originalSlot = $reschedule->originalSlot;
            $originalSlot->status = 'available';
            $originalSlot->booked_by_user_id = null;
            $originalSlot->save();

            // Mark the new slot as booked
            $newSlot = ProfessionalScheduleSlot::find($selectedSlotId);
            $newSlot->status = 'booked';
            $newSlot->booked_by_user_id = $consultation->professionalScheduleSlot->booked_by_user_id;
            $newSlot->save();

            // Update reschedule status
            $reschedule->status = 'accepted';
            $reschedule->client_response_at = Carbon::now();
            $reschedule->save();

            return true;
        } elseif ($action === 'cancel') {
            // Just update the reschedule status
            $reschedule->status = 'cancelled';
            $reschedule->client_response_at = Carbon::now();
            $reschedule->save();

            return true;
        }

        return false;
    }

    /**
     * Expire old reschedules that are past their expiration time
     *
     * @return int The number of reschedules that were expired
     */
    public function expireOldReschedules()
    {
        return Reschedule::where('status', 'pending')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'expired']);
    }
}