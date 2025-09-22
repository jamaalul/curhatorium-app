<?php

namespace App\Filament\Resources\SgdPaymentResource\Pages;

use App\Filament\Resources\SgdPaymentResource;
use App\Services\SgdPaymentService;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewSgdPayment extends ViewRecord
{
    protected static string $resource = SgdPaymentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        $paymentService = app(SgdPaymentService::class);
        $paymentData = $paymentService->calculateHostPayment($this->record->id);
        $consumptionDetails = $paymentService->getGroupConsumptionDetails($this->record->id);

        return $infolist
            ->schema([
                Infolists\Components\Section::make('SGD Group Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('Group Title'),
                        Infolists\Components\TextEntry::make('topic')
                            ->label('Topic'),
                        Infolists\Components\TextEntry::make('category')
                            ->label('Category')
                            ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('-', ' ', $state))),
                        Infolists\Components\TextEntry::make('schedule')
                            ->label('Scheduled Date')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('host.name')
                            ->label('Host')
                            ->placeholder('No host assigned'),
                        Infolists\Components\IconEntry::make('is_done')
                            ->label('Completed')
                            ->boolean(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Payment Summary')
                    ->schema([
                        Infolists\Components\TextEntry::make('paid_tickets')
                            ->label('Paid Tickets Consumed')
                            ->state($paymentData['payment_data']['paid_tickets_consumed'] ?? 0)
                            ->color('success')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('calm_starter_tickets')
                            ->label('Calm Starter Tickets Consumed')
                            ->state($paymentData['payment_data']['calm_starter_tickets_consumed'] ?? 0)
                            ->color('info'),
                        Infolists\Components\TextEntry::make('total_tickets')
                            ->label('Total Tickets Consumed')
                            ->state($paymentData['payment_data']['total_tickets_consumed'] ?? 0)
                            ->color('gray'),
                        Infolists\Components\TextEntry::make('payment_amount')
                            ->label('Payment Amount')
                            ->state('Rp ' . number_format($paymentData['payment_data']['payment_amount'] ?? 0, 0, ',', '.'))
                            ->color('success')
                            ->weight('bold')
                            ->size('lg'),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make('Paid Ticket Users')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('paid_users')
                            ->schema([
                                Infolists\Components\TextEntry::make('user_name')
                                    ->label('User Name'),
                                Infolists\Components\TextEntry::make('username')
                                    ->label('Username'),
                                Infolists\Components\TextEntry::make('consumed_at')
                                    ->label('Joined At')
                                    ->dateTime(),
                            ])
                            ->columns(3)
                            ->state($consumptionDetails['consumptions']['paid']->map(function ($consumption) {
                                return [
                                    'user_name' => $consumption->user->name ?? 'N/A',
                                    'username' => $consumption->user->username ?? 'N/A',
                                    'consumed_at' => $consumption->consumed_at,
                                ];
                            })->toArray())
                            ->visible(fn () => $consumptionDetails['consumptions']['paid']->count() > 0),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Infolists\Components\Section::make('Calm Starter Ticket Users')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('calm_starter_users')
                            ->schema([
                                Infolists\Components\TextEntry::make('user_name')
                                    ->label('User Name'),
                                Infolists\Components\TextEntry::make('username')
                                    ->label('Username'),
                                Infolists\Components\TextEntry::make('consumed_at')
                                    ->label('Joined At')
                                    ->dateTime(),
                            ])
                            ->columns(3)
                            ->state($consumptionDetails['consumptions']['calm_starter']->map(function ($consumption) {
                                return [
                                    'user_name' => $consumption->user->name ?? 'N/A',
                                    'username' => $consumption->user->username ?? 'N/A',
                                    'consumed_at' => $consumption->consumed_at,
                                ];
                            })->toArray())
                            ->visible(fn () => $consumptionDetails['consumptions']['calm_starter']->count() > 0),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Infolists\Components\Section::make('All Participants')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('all_users')
                            ->schema([
                                Infolists\Components\TextEntry::make('user_name')
                                    ->label('User Name'),
                                Infolists\Components\TextEntry::make('username')
                                    ->label('Username'),
                                Infolists\Components\TextEntry::make('ticket_source')
                                    ->label('Ticket Source')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'paid' => 'success',
                                        'calm_starter' => 'info',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('consumed_at')
                                    ->label('Joined At')
                                    ->dateTime(),
                            ])
                            ->columns(4)
                            ->state($consumptionDetails['consumptions']['total']->map(function ($consumption) {
                                return [
                                    'user_name' => $consumption->user->name ?? 'N/A',
                                    'username' => $consumption->user->username ?? 'N/A',
                                    'ticket_source' => $consumption->ticket_source,
                                    'consumed_at' => $consumption->consumed_at,
                                ];
                            })->toArray()),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
} 