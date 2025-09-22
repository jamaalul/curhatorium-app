<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SgdPaymentResource\Pages;
use App\Models\SgdGroup;
use App\Services\SgdPaymentService;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SgdPaymentResource extends Resource
{
    protected static ?string $model = SgdGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?string $navigationLabel = 'SGD Payments';
    
    protected static ?string $modelLabel = 'SGD Payment';
    
    protected static ?string $pluralModelLabel = 'SGD Payments';

    protected static ?string $navigationGroup = 'Finance';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('SGD Group')
                    ->description(fn (SgdGroup $record): string => $record->topic)
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'mental-health' => 'danger',
                        'education' => 'info',
                        'career' => 'warning',
                        'relationships' => 'success',
                        'other' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('-', ' ', $state)))
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('schedule')
                    ->dateTime()
                    ->sortable()
                    ->label('Scheduled'),
                    
                Tables\Columns\IconColumn::make('is_done')
                    ->boolean()
                    ->label('Completed'),
                    
                Tables\Columns\TextColumn::make('paid_tickets_consumed')
                    ->label('Paid Tickets')
                    ->getStateUsing(function (SgdGroup $record): int {
                        $paymentService = app(SgdPaymentService::class);
                        $paymentData = $paymentService->calculateHostPayment($record->id);
                        return $paymentData['payment_data']['paid_tickets_consumed'] ?? 0;
                    })
                    ->sortable()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('calm_starter_tickets_consumed')
                    ->label('Calm Starter Tickets')
                    ->getStateUsing(function (SgdGroup $record): int {
                        $paymentService = app(SgdPaymentService::class);
                        $paymentData = $paymentService->calculateHostPayment($record->id);
                        return $paymentData['payment_data']['calm_starter_tickets_consumed'] ?? 0;
                    })
                    ->sortable()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('total_tickets_consumed')
                    ->label('Total Tickets')
                    ->getStateUsing(function (SgdGroup $record): int {
                        $paymentService = app(SgdPaymentService::class);
                        $paymentData = $paymentService->calculateHostPayment($record->id);
                        return $paymentData['payment_data']['total_tickets_consumed'] ?? 0;
                    })
                    ->sortable()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('payment_amount')
                    ->label('Payment Amount')
                    ->getStateUsing(function (SgdGroup $record): string {
                        $paymentService = app(SgdPaymentService::class);
                        $paymentData = $paymentService->calculateHostPayment($record->id);
                        $amount = $paymentData['payment_data']['payment_amount'] ?? 0;
                        return 'Rp ' . number_format($amount, 0, ',', '.');
                    })
                    ->sortable()
                    ->alignCenter()
                    ->color('success')
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('host_name')
                    ->label('Host')
                    ->getStateUsing(function (SgdGroup $record): string {
                        return $record->host?->name ?? 'No host assigned';
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'mental-health' => 'Mental Health',
                        'education' => 'Education',
                        'career' => 'Career',
                        'relationships' => 'Relationships',
                        'other' => 'Other',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_done')
                    ->label('Completion Status')
                    ->placeholder('All Groups')
                    ->trueLabel('Completed')
                    ->falseLabel('Not Completed'),
                    
                Tables\Filters\Filter::make('has_payments')
                    ->label('Has Payments')
                    ->query(fn (Builder $query): Builder => $query->whereHas('sgdTicketConsumptions', function ($q) {
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
                    ->url(fn (SgdGroup $record): string => route('filament.admin.resources.sgd-payments.view', $record))
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('export_payment_data')
                    ->label('Export Payment Data')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (SgdGroup $record) {
                        $paymentService = app(SgdPaymentService::class);
                        $paymentData = $paymentService->calculateHostPayment($record->id);
                        $details = $paymentService->getGroupConsumptionDetails($record->id);
                        
                        // You can implement CSV/Excel export here
                        // For now, we'll just return the data
                        return response()->json([
                            'payment_data' => $paymentData,
                            'consumption_details' => $details
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Export Payment Data')
                    ->modalDescription('This will export detailed payment and consumption data for this SGD group.')
                    ->modalSubmitActionLabel('Export'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('export_selected')
                    ->label('Export Selected')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($records) {
                        $paymentService = app(SgdPaymentService::class);
                        $exportData = [];
                        
                        foreach ($records as $record) {
                            $paymentData = $paymentService->calculateHostPayment($record->id);
                            $exportData[] = [
                                'group_id' => $record->id,
                                'group_title' => $record->title,
                                'schedule' => $record->schedule,
                                'paid_tickets' => $paymentData['payment_data']['paid_tickets_consumed'],
                                'payment_amount' => $paymentData['payment_data']['payment_amount'],
                            ];
                        }
                        
                        return response()->json($exportData);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Export Selected Payments')
                    ->modalDescription('This will export payment data for all selected SGD groups.')
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
            'index' => Pages\ListSgdPayments::route('/'),
            'view' => Pages\ViewSgdPayment::route('/{record}'),
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
