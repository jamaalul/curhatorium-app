<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketplaceTransactionResource\Pages;
use App\Models\MarketplaceOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MarketplaceTransactionResource extends Resource
{
    protected static ?string $model = MarketplaceOrder::class;

    protected static ?string $navigationGroup = 'Transactions';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Marketplace Transactions';

    protected static ?string $modelLabel = 'Marketplace Transaction';

    protected static ?string $pluralModelLabel = 'Marketplace Transactions';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_id')
                    ->label('Product ID')
                    ->disabled(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Quantity')
                    ->disabled(),
                Forms\Components\TextInput::make('total_price')
                    ->label('Total Price')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Created At')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('product_id')
                    ->label('Product ID')
                    ->description(fn (MarketplaceOrder $record): ?string => $record->product?->name)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
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
            'index' => Pages\ListMarketplaceTransactions::route('/'),
        ];
    }
}
