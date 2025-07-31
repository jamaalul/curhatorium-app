<?php

namespace App\Filament\Resources\ShareTalkPaymentResource\Pages;

use App\Filament\Resources\ShareTalkPaymentResource;
use App\Services\ShareTalkPaymentService;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewShareTalkPayment extends ViewRecord
{
    protected static string $resource = ShareTalkPaymentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        $paymentService = app(ShareTalkPaymentService::class);
        $paymentData = $paymentService->calculateProfessionalPayment($this->record->id);
        $consumptionDetails = $paymentService->getSessionConsumptionDetails($this->record->id);

        return $infolist
            ->schema([
                Infolists\Components\Section::make('Chat Session Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('session_id')
                            ->label('Session ID'),
                        Infolists\Components\TextEntry::make('type')
                            ->label('Session Type')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('professional.name')
                            ->label('Professional')
                            ->placeholder('No professional assigned'),
                        Infolists\Components\TextEntry::make('professional.type')
                            ->label('Professional Type')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
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
                                    ->label('Consumed At')
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
                                    ->label('Consumed At')
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
                                    ->label('Consumed At')
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