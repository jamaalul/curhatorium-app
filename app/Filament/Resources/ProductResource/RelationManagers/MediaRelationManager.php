<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\ProductMedia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class MediaRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    protected static ?string $title = 'Media Produk';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('media_type')
                    ->label('Tipe Media')
                    ->options([
                        'image' => 'Gambar',
                        'video' => 'Video',
                    ])
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('order_number')
                    ->label('Urutan')
                    ->required()
                    ->integer()
                    ->minValue(1)
                    ->default(fn (RelationManager $livewire): int => ((int) $livewire->getOwnerRecord()->media()->max('order_number')) + 1)
                    ->unique(
                        table: ProductMedia::class,
                        column: 'order_number',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, RelationManager $livewire): Unique => $rule->where(
                            'product_id',
                            $livewire->getOwnerRecord()->getKey(),
                        ),
                    ),
                Forms\Components\TextInput::make('media_url')
                    ->label('URL Media')
                    ->required()
                    ->url()
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('media_url')
            ->columns([
                Tables\Columns\ViewColumn::make('preview')
                    ->label('Preview')
                    ->view('filament.tables.columns.product-media-preview'),
                Tables\Columns\TextColumn::make('media_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'image' => 'Gambar',
                        'video' => 'Video',
                    })
                    ->color(fn (ProductMedia $record): string => match ($record->media_type) {
                        'image' => 'success',
                        'video' => 'info',
                    }),
                Tables\Columns\TextColumn::make('media_url')
                    ->label('URL Media')
                    ->limit(50)
                    ->url(fn (ProductMedia $record): string => $record->media_url)
                    ->openUrlInNewTab()
                    ->copyable(),
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
