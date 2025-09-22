<?php

namespace App\Services;

use App\Models\SgdGroup;
use App\Models\SgdTicketConsumption;
use App\Models\Professional;
use Carbon\Carbon;

class SgdPaymentService
{
    /**
     * Calculate payment for SGD host based on paid ticket consumption
     * 
     * @param int $sgdGroupId
     * @return array
     */
    public function calculateHostPayment($sgdGroupId)
    {
        $group = SgdGroup::with('host')->find($sgdGroupId);
        if (!$group) {
            return [
                'success' => false,
                'message' => 'SGD group not found'
            ];
        }

        // Count paid ticket consumptions (excluding Calm Starter)
        $paidConsumptions = SgdTicketConsumption::where('sgd_group_id', $sgdGroupId)
            ->where('ticket_source', 'paid')
            ->count();

        // Count total consumptions for reference
        $totalConsumptions = SgdTicketConsumption::where('sgd_group_id', $sgdGroupId)->count();

        return [
            'success' => true,
            'sgd_group' => [
                'id' => $group->id,
                'title' => $group->title,
                'schedule' => $group->schedule,
                'is_done' => $group->is_done,
            ],
            'host' => $group->host ? [
                'id' => $group->host->id,
                'name' => $group->host->name,
                'type' => $group->host->type,
            ] : null,
            'payment_data' => [
                'paid_tickets_consumed' => $paidConsumptions,
                'total_tickets_consumed' => $totalConsumptions,
                'calm_starter_tickets_consumed' => $totalConsumptions - $paidConsumptions,
                'payment_amount' => $this->calculatePaymentAmount($paidConsumptions),
            ]
        ];
    }

    /**
     * Get payment summary for a specific time period
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string|null $hostId
     * @return array
     */
    public function getPaymentSummary($startDate, $endDate, $hostId = null)
    {
        $query = SgdTicketConsumption::where('ticket_source', 'paid')
            ->whereBetween('consumed_at', [$startDate, $endDate]);

        if ($hostId) {
            // If we have host information in SGD groups, filter by host
            $query->whereHas('sgdGroup', function($q) use ($hostId) {
                // Assuming there's a host_id field in sgd_groups table
                // $q->where('host_id', $hostId);
            });
        }

        $paidConsumptions = $query->count();
        $totalConsumptions = SgdTicketConsumption::whereBetween('consumed_at', [$startDate, $endDate])->count();

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'paid_tickets_consumed' => $paidConsumptions,
                'total_tickets_consumed' => $totalConsumptions,
                'calm_starter_tickets_consumed' => $totalConsumptions - $paidConsumptions,
                'total_payment_amount' => $this->calculatePaymentAmount($paidConsumptions),
            ]
        ];
    }

    /**
     * Calculate payment amount based on number of paid tickets
     * This is a placeholder - adjust the calculation based on your business logic
     * 
     * @param int $paidTicketsCount
     * @return float
     */
    public function calculatePaymentAmount($paidTicketsCount)
    {
        // Example: Rp 50,000 per paid ticket
        $ratePerTicket = 5000;
        return $paidTicketsCount * $ratePerTicket;
    }

    /**
     * Get detailed consumption data for a specific SGD group
     * 
     * @param int $sgdGroupId
     * @return array
     */
    public function getGroupConsumptionDetails($sgdGroupId)
    {
        $consumptions = SgdTicketConsumption::where('sgd_group_id', $sgdGroupId)
            ->with(['user:id,name,username', 'sgdGroup:id,title,schedule'])
            ->get();

        $paidConsumptions = $consumptions->where('ticket_source', 'paid');
        $calmStarterConsumptions = $consumptions->where('ticket_source', 'calm_starter');

        return [
            'group' => $consumptions->first()?->sgdGroup,
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
} 