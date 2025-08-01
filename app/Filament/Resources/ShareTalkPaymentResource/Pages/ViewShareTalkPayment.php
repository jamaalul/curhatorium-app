<?php

namespace App\Filament\Resources\ShareTalkPaymentResource\Pages;

use App\Filament\Resources\ShareTalkPaymentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewShareTalkPayment extends ViewRecord
{
    protected static string $resource = ShareTalkPaymentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Ticket Consumption Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('chatSession.session_id')
                            ->label('Session ID'),
                        Infolists\Components\TextEntry::make('chatSession.type')
                            ->label('Session Type')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        Infolists\Components\TextEntry::make('chatSession.status')
                            ->label('Session Status')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        Infolists\Components\TextEntry::make('chatSession.created_at')
                            ->label('Session Created At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('chatSession.professional.name')
                            ->label('Professional')
                            ->placeholder('No professional assigned'),
                        Infolists\Components\TextEntry::make('chatSession.professional.type')
                            ->label('Professional Type')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Consumption Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('User Name'),
                        Infolists\Components\TextEntry::make('user.username')
                            ->label('Username'),
                        Infolists\Components\TextEntry::make('ticket_source')
                            ->label('Ticket Source')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'danger',
                                'calm_starter' => 'success',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                        Infolists\Components\TextEntry::make('consumed_at')
                            ->label('Consumed At')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('payment_amount')
                            ->label('Payment Amount')
                            ->state(function ($record): string {
                                if ($record->ticket_source === 'paid') {
                                    $professionalType = $record->chatSession->professional->type ?? 'partner';
                                    $rate = $professionalType === 'psychiatrist' ? 100000 : 50000;
                                    return 'Rp ' . number_format($rate, 0, ',', '.');
                                }
                                return 'Rp 0';
                            })
                            ->color('success')
                            ->weight('bold')
                            ->size('lg'),
                    ])
                    ->columns(3),
            ]);
    }
} 