<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShareTalkPaymentResource\Pages;
use App\Models\ShareTalkTicketConsumption;
use App\Services\ShareTalkPaymentService;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ShareTalkPaymentResource extends Resource
{
    protected static ?string $model = ShareTalkTicketConsumption::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Share & Talk Payments';
    
    protected static ?string $modelLabel = 'Share & Talk Payment';
    
    protected static ?string $pluralModelLabel = 'Share & Talk Payments';

    protected static ?string $navigationGroup = 'Finance';

    public static function table(Table $table): Table
    {
        return $table
            ->query(ShareTalkTicketConsumption::query()->with(['user', 'chatSession.professional']))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('ticket_source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'danger',
                        'calm_starter' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('consumed_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Consumed At'),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ticket_source')
                    ->options([
                        'paid' => 'Paid',
                        'calm_starter' => 'Calm Starter',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('consumed_at', 'desc');
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