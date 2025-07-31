<?php

namespace App\Services;

use App\Models\ChatSession;
use App\Models\ShareTalkTicketConsumption;
use App\Models\Professional;
use Carbon\Carbon;

class ShareTalkPaymentService
{
    /**
     * Calculate payment for Share and Talk professional based on paid ticket consumption
     * 
     * @param int $chatSessionId
     * @return array
     */
    public function calculateProfessionalPayment($chatSessionId)
    {
        $session = ChatSession::with('professional')->find($chatSessionId);
        if (!$session) {
            return [
                'success' => false,
                'message' => 'Chat session not found'
            ];
        }

        // Count paid ticket consumptions (excluding Calm Starter)
        $paidConsumptions = ShareTalkTicketConsumption::where('chat_session_id', $chatSessionId)
            ->where('ticket_source', 'paid')
            ->count();

        // Count total consumptions for reference
        $totalConsumptions = ShareTalkTicketConsumption::where('chat_session_id', $chatSessionId)->count();

        return [
            'success' => true,
            'chat_session' => [
                'id' => $session->id,
                'session_id' => $session->session_id,
                'type' => $session->type,
                'status' => $session->status,
                'created_at' => $session->created_at,
            ],
            'professional' => $session->professional ? [
                'id' => $session->professional->id,
                'name' => $session->professional->name,
                'type' => $session->professional->type,
            ] : null,
            'payment_data' => [
                'paid_tickets_consumed' => $paidConsumptions,
                'total_tickets_consumed' => $totalConsumptions,
                'calm_starter_tickets_consumed' => $totalConsumptions - $paidConsumptions,
                'payment_amount' => $this->calculatePaymentAmount($paidConsumptions, $session->professional?->type),
            ]
        ];
    }

    /**
     * Get payment summary for a specific time period
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string|null $professionalId
     * @return array
     */
    public function getPaymentSummary($startDate, $endDate, $professionalId = null)
    {
        $query = ShareTalkTicketConsumption::where('ticket_source', 'paid')
            ->whereBetween('consumed_at', [$startDate, $endDate]);

        if ($professionalId) {
            // Filter by professional
            $query->whereHas('chatSession', function($q) use ($professionalId) {
                $q->where('professional_id', $professionalId);
            });
        }

        $paidConsumptions = $query->count();
        $totalConsumptions = ShareTalkTicketConsumption::whereBetween('consumed_at', [$startDate, $endDate])->count();

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'paid_tickets_consumed' => $paidConsumptions,
                'total_tickets_consumed' => $totalConsumptions,
                'calm_starter_tickets_consumed' => $totalConsumptions - $paidConsumptions,
                'total_payment_amount' => $this->calculateTotalPaymentAmount($paidConsumptions),
            ]
        ];
    }

    /**
     * Calculate payment amount based on number of paid tickets and professional type
     * 
     * @param int $paidTicketsCount
     * @param string|null $professionalType
     * @return float
     */
    public function calculatePaymentAmount($paidTicketsCount, $professionalType = null)
    {
        // Different rates based on professional type
        $ratePerTicket = match($professionalType) {
            'psychiatrist' => 100000, // Rp 100,000 for psychiatrist
            'partner' => 50000,       // Rp 50,000 for partner/ranger
            default => 50000,         // Default rate
        };
        
        return $paidTicketsCount * $ratePerTicket;
    }

    /**
     * Calculate total payment amount for summary (using default rate)
     * 
     * @param int $paidTicketsCount
     * @return float
     */
    private function calculateTotalPaymentAmount($paidTicketsCount)
    {
        // Use default rate for summary calculations
        $ratePerTicket = 50000;
        return $paidTicketsCount * $ratePerTicket;
    }

    /**
     * Get detailed consumption data for a specific chat session
     * 
     * @param int $chatSessionId
     * @return array
     */
    public function getSessionConsumptionDetails($chatSessionId)
    {
        $consumptions = ShareTalkTicketConsumption::where('chat_session_id', $chatSessionId)
            ->with(['user:id,name,username', 'chatSession:id,session_id,type,status'])
            ->get();

        $paidConsumptions = $consumptions->where('ticket_source', 'paid');
        $calmStarterConsumptions = $consumptions->where('ticket_source', 'calm_starter');

        return [
            'session' => $consumptions->first()?->chatSession,
            'consumptions' => [
                'paid' => $paidConsumptions->values(),
                'calm_starter' => $calmStarterConsumptions->values(),
                'total' => $consumptions->values(),
            ],
            'summary' => [
                'paid_count' => $paidConsumptions->count(),
                'calm_starter_count' => $calmStarterConsumptions->count(),
                'total_count' => $consumptions->count(),
            ]
        ];
    }

    /**
     * Get payment summary by professional type
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getPaymentSummaryByProfessionalType($startDate, $endDate)
    {
        $paidConsumptions = ShareTalkTicketConsumption::where('ticket_source', 'paid')
            ->whereBetween('consumed_at', [$startDate, $endDate])
            ->with('chatSession.professional')
            ->get();

        $summary = [
            'psychiatrist' => [
                'count' => 0,
                'amount' => 0,
            ],
            'partner' => [
                'count' => 0,
                'amount' => 0,
            ],
            'total' => [
                'count' => 0,
                'amount' => 0,
            ]
        ];

        foreach ($paidConsumptions as $consumption) {
            $professionalType = $consumption->chatSession->professional?->type ?? 'partner';
            $amount = $this->calculatePaymentAmount(1, $professionalType);
            
            $summary[$professionalType]['count']++;
            $summary[$professionalType]['amount'] += $amount;
            $summary['total']['count']++;
            $summary['total']['amount'] += $amount;
        }

        return $summary;
    }
} 