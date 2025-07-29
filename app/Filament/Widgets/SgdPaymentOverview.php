<?php

namespace App\Filament\Widgets;

use App\Models\SgdGroup;
use App\Models\SgdTicketConsumption;
use App\Services\SgdPaymentService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SgdPaymentOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $paymentService = app(SgdPaymentService::class);
        
        // Get this month's data
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $monthlySummary = $paymentService->getPaymentSummary($startOfMonth, $endOfMonth);
        
        // Get total data
        $totalPaidTickets = SgdTicketConsumption::where('ticket_source', 'paid')->count();
        $totalCalmStarterTickets = SgdTicketConsumption::where('ticket_source', 'calm_starter')->count();
        $totalTickets = SgdTicketConsumption::count();
        
        // Calculate total payment amount
        $totalPaymentAmount = $paymentService->calculatePaymentAmount($totalPaidTickets);
        
        return [
            Stat::make('Total SGD Groups', SgdGroup::count())
                ->description('All time')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
                
            Stat::make('Completed SGD Groups', SgdGroup::where('is_done', true)->count())
                ->description('All time')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('This Month - Paid Tickets', $monthlySummary['summary']['paid_tickets_consumed'])
                ->description('This month')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
                
            Stat::make('This Month - Payment Amount', 'Rp ' . number_format($monthlySummary['summary']['total_payment_amount'], 0, ',', '.'))
                ->description('This month')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
                
            Stat::make('All Time - Paid Tickets', $totalPaidTickets)
                ->description('All time')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('warning'),
                
            Stat::make('All Time - Calm Starter Tickets', $totalCalmStarterTickets)
                ->description('All time')
                ->descriptionIcon('heroicon-m-gift')
                ->color('info'),
                
            Stat::make('All Time - Total Payment', 'Rp ' . number_format($totalPaymentAmount, 0, ',', '.'))
                ->description('All time')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
} 