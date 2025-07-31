<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShareTalkPaymentResource\Pages;
use App\Models\ChatSession;
use App\Services\ShareTalkPaymentService;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ShareTalkPaymentResource extends Resource
{
    protected static ?string $model = ChatSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Share & Talk Payments';
    
    protected static ?string $modelLabel = 'Share & Talk Payment';
    
    protected static ?string $pluralModelLabel = 'Share & Talk Payments';

    protected static ?string $navigationGroup = 'Finance';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('session_id')
                    ->searchable()
                    ->sortable()
                    ->label('Session ID')
                    ->limit(20),
                    
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'chat' => 'info',
                        'video' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('professional.name')
                    ->label('Professional')
                    ->sortable()
                    ->searchable()
                    ->placeholder('No professional assigned'),
                    
                Tables\Columns\TextColumn::make('professional.type')
                    ->label('Professional Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'psychiatrist' => 'danger',
                        'partner' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'waiting' => 'warning',
                        'active' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created'),
                    
                Tables\Columns\TextColumn::make('paid_tickets_consumed')
                    ->label('Paid Tickets')
                    ->getStateUsing(function (ChatSession $record): int {
                        $paymentService = app(ShareTalkPaymentService::class);
                        $paymentData = $paymentService->calculateProfessionalPayment($record->id);
                        return $paymentData['payment_data']['paid_tickets_consumed'] ?? 0;
                    })
                    ->sortable()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('calm_starter_tickets_consumed')
                    ->label('Calm Starter Tickets')
                    ->getStateUsing(function (ChatSession $record): int {
                        $paymentService = app(ShareTalkPaymentService::class);
                        $paymentData = $paymentService->calculateProfessionalPayment($record->id);
                        return $paymentData['payment_data']['calm_starter_tickets_consumed'] ?? 0;
                    })
                    ->sortable()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('total_tickets_consumed')
                    ->label('Total Tickets')
                    ->getStateUsing(function (ChatSession $record): int {
                        $paymentService = app(ShareTalkPaymentService::class);
                        $paymentData = $paymentService->calculateProfessionalPayment($record->id);
                        return $paymentData['payment_data']['total_tickets_consumed'] ?? 0;
                    })
                    ->sortable()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('payment_amount')
                    ->label('Payment Amount')
                    ->getStateUsing(function (ChatSession $record): string {
                        $paymentService = app(ShareTalkPaymentService::class);
                        $paymentData = $paymentService->calculateProfessionalPayment($record->id);
                        $amount = $paymentData['payment_data']['payment_amount'] ?? 0;
                        return 'Rp ' . number_format($amount, 0, ',', '.');
                    })
                    ->sortable()
                    ->alignCenter()
                    ->color('success')
                    ->weight('bold'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'chat' => 'Chat',
                        'video' => 'Video',
                    ]),
                    
                Tables\Filters\SelectFilter::make('professional_type')
                    ->options([
                        'psychiatrist' => 'Psychiatrist',
                        'partner' => 'Partner/Ranger',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->whereHas('professional', function ($q) use ($data) {
                            $q->where('type', $data['value']);
                        });
                    }),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'waiting' => 'Waiting',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                    
                Tables\Filters\Filter::make('has_payments')
                    ->label('Has Payments')
                    ->query(fn (Builder $query): Builder => $query->whereHas('shareTalkTicketConsumptions', function ($q) {
                        $q->where('ticket_source', 'paid');
                    }))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['has_payments'] ?? false) {
                            $indicators['has_payments'] = 'Has Paid Tickets';
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_details')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->url(fn (ChatSession $record): string => route('filament.admin.resources.share-talk-payments.view', $record))
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('export_payment_data')
                    ->label('Export Payment Data')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (ChatSession $record) {
                        $paymentService = app(ShareTalkPaymentService::class);
                        $paymentData = $paymentService->calculateProfessionalPayment($record->id);
                        $details = $paymentService->getSessionConsumptionDetails($record->id);
                        
                        return response()->json([
                            'payment_data' => $paymentData,
                            'consumption_details' => $details
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Export Payment Data')
                    ->modalDescription('This will export detailed payment and consumption data for this chat session.')
                    ->modalSubmitActionLabel('Export'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('export_selected')
                    ->label('Export Selected')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($records) {
                        $paymentService = app(ShareTalkPaymentService::class);
                        $exportData = [];
                        
                        foreach ($records as $record) {
                            $paymentData = $paymentService->calculateProfessionalPayment($record->id);
                            $exportData[] = [
                                'session_id' => $record->session_id,
                                'type' => $record->type,
                                'professional_name' => $record->professional?->name ?? 'N/A',
                                'professional_type' => $record->professional?->type ?? 'N/A',
                                'paid_tickets' => $paymentData['payment_data']['paid_tickets_consumed'],
                                'payment_amount' => $paymentData['payment_data']['payment_amount'],
                            ];
                        }
                        
                        return response()->json($exportData);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Export Selected Payments')
                    ->modalDescription('This will export payment data for all selected chat sessions.')
                    ->modalSubmitActionLabel('Export'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShareTalkPayments::route('/'),
            'view' => Pages\ViewShareTalkPayment::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Read-only resource
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // Read-only resource
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // Read-only resource
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->is_admin ?? false;
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->is_admin ?? false;
    }
} 