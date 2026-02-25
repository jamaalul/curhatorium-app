<?php

namespace App\Http\Controllers;

use App\Models\Reschedule;
use App\Models\RescheduleSlot;
use App\Services\RescheduleService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RescheduleController extends Controller
{
    protected $rescheduleService;
    protected $fonnteService;
    protected $ticketService;

    public function __construct(
        RescheduleService $rescheduleService,
        \App\Services\FonnteService $fonnteService,
        TicketService $ticketService
    ) {
        $this->rescheduleService = $rescheduleService;
        $this->fonnteService = $fonnteService;
        $this->ticketService = $ticketService;
    }

    /**
     * Show the client interface for reschedule selection
     *
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function clientInterface(string $token)
    {
        // Find the reschedule by token
        $reschedule = Reschedule::where('token', $token)
            ->with(['rescheduleSlots.slot', 'originalSlot', 'consultation.professionalScheduleSlot.bookedBy'])
            ->first();

        // Check if the reschedule exists and is valid
        if (!$reschedule || !$this->rescheduleService->isTokenValid($reschedule, $token)) {
            return view('reschedule.expired', ['reason' => 'Invalid or expired reschedule link.']);
        }

        // Check if the reschedule is already completed or cancelled
        if ($reschedule->status === 'accepted') {
            return view('reschedule.expired', ['reason' => 'This reschedule has already been completed.']);
        } elseif ($reschedule->status === 'cancelled') {
            return view('reschedule.expired', ['reason' => 'This reschedule has been cancelled.']);
        } elseif ($reschedule->status === 'expired') {
            return view('reschedule.expired', ['reason' => 'This reschedule offer has expired.']);
        }

        // Get the consultation details
        $consultation = $reschedule->consultation;
        $originalSlot = $reschedule->originalSlot;
        $offeredSlots = $reschedule->rescheduleSlots;

        // Format the dates for display
        $originalDate = \Carbon\Carbon::parse($originalSlot->slot_start_time)->format('d M Y');
        $originalTime = \Carbon\Carbon::parse($originalSlot->slot_start_time)->format('H:i');

        // Format the offered slots
        foreach ($offeredSlots as $slot) {
            $slot->formatted_date = \Carbon\Carbon::parse($slot->slot->slot_start_time)->format('d M Y');
            $slot->formatted_time = \Carbon\Carbon::parse($slot->slot->slot_start_time)->format('H:i');
        }

        // Get time remaining until expiration
        $expiresAt = \Carbon\Carbon::parse($reschedule->expires_at);
        $timeRemaining = $expiresAt->diffForHumans();

        return view('reschedule.client', [
            'reschedule' => $reschedule,
            'consultation' => $consultation,
            'originalSlot' => $originalSlot,
            'originalDate' => $originalDate,
            'originalTime' => $originalTime,
            'offeredSlots' => $offeredSlots,
            'timeRemaining' => $timeRemaining,
        ]);
    }

    /**
     * Handle the client's slot selection
     *
     * @param \Illuminate\Http\Request $request
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function selectSlot(Request $request, string $token)
    {
        // Validate the request
        $request->validate([
            'slot_id' => 'required_if:action,accept',
            'action' => 'required|in:accept,cancel',
        ]);

        // Find the reschedule by token
        $reschedule = Reschedule::where('token', $token)
            ->with(['rescheduleSlots.slot', 'originalSlot', 'consultation.professionalScheduleSlot.bookedBy'])
            ->first();

        // Check if the reschedule exists and is valid
        if (!$reschedule || !$this->rescheduleService->isTokenValid($reschedule, $token)) {
            return view('reschedule.expired', ['reason' => 'Invalid or expired reschedule link.']);
        }

        $action = $request->input('action');
        $selectedSlotId = $request->input('slot_id');

        if ($action === 'accept') {
            // Handle the reschedule action
            $result = $this->rescheduleService->handleClientResponse($reschedule, $selectedSlotId, $action);

            if (!$result) {
                return view('reschedule.error', ['message' => 'Unable to process reschedule. Please try again.']);
            }
            // Get the selected slot details
            $selectedSlotRelation = $reschedule->rescheduleSlots()
                ->where('professional_schedule_slot_id', $selectedSlotId)
                ->first();
            if (!$selectedSlotRelation) {
                return view('reschedule.error', ['message' => 'Selected slot not found.']);
            }
            $selectedSlot = $selectedSlotRelation->slot;

            // Format the new slot date and time
            $newDate = \Carbon\Carbon::parse($selectedSlot->slot_start_time)->format('d M Y');
            $newTime = \Carbon\Carbon::parse($selectedSlot->slot_start_time)->format('H:i');

            // Send notification to the client
            $client = $reschedule->consultation->professionalScheduleSlot->bookedBy;
            $message = "Halo {$client->username},\n\n"
                . "Jadwal konsultasi Anda telah berhasil diubah!\n\n"
                . "🗓️ Jadwal Baru: {$newDate}\n"
                . "🕐 Waktu: {$newTime}\n\n"
                . "Terima kasih,\n"
                . "Tim Curhatorium";
            $this->fonnteService->sendWhatsApp($reschedule->consultation->no_wa, $message);

            return view('reschedule.success', [
                'reschedule' => $reschedule,
                'newDate' => $newDate,
                'newTime' => $newTime,
            ]);
        } else {
            // Cancel the booking and refund the ticket
            $consultation = $reschedule->consultation;
            $originalSlot = $reschedule->originalSlot;
            $client = $consultation->professionalScheduleSlot->bookedBy;
            $noWa = $consultation->no_wa;

            // Mark original slot as available
            $originalSlot->status = 'available';
            $originalSlot->booked_by_user_id = null;
            $originalSlot->save();

            // Delete the consultation
            $consultation->delete();

            // Refund the ticket
            $this->ticketService->refundTicket($client, 'consultation', 1);

            // Set reschedule status to expired (not cancelled)
            $reschedule->status = 'expired';
            $reschedule->client_response_at = \Carbon\Carbon::now();
            $reschedule->save();

            // Send notification to the client
            $message = "Halo {$client->username},\n\n"
                . "Konsultasi Anda telah dibatalkan dan tiket telah dikembalikan.\n\n"
                . "Terima kasih,\n"
                . "Tim Curhatorium";
            $this->fonnteService->sendWhatsApp($noWa, $message);

            return view('reschedule.cancelled', [
                'reschedule' => $reschedule,
                'message' => 'Your consultation has been cancelled and ticket refunded.',
            ]);
        }
    }
}
