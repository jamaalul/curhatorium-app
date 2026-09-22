<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CbtModuleResource\Pages;
use App\Filament\Resources\CbtModuleResource\RelationManagers;
use App\Models\CbtModule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CbtModuleResource extends Resource
{
    protected static ?string $model = CbtModule::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'E-Class';

    protected static ?string $navigationLabel = 'Modul E-Class';

    protected static ?string $modelLabel = 'Modul E-Class';

    protected static ?string $pluralModelLabel = 'Modul E-Class';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state): void {
                        if ((string) ($get('slug') ?? '') !== Str::slug((string) $old)) {
                            return;
                        }

                        $set('slug', Str::slug((string) $state));
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->alphaDash()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\RichEditor::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->label('Harga')
                    ->prefix('Rp')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(9999999999.99)
                    ->step(0.01),
                Forms\Components\Toggle::make('is_published')
                    ->label('Published')
                    ->default(false)
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, bool $state): void {
                        $set('published_at', $state ? ($get('published_at') ?? now()) : null);
                    }),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Tanggal Published')
                    ->seconds(false)
                    ->nullable()
                    ->visible(fn (Get $get): bool => (bool) $get('is_published')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tanggal Published')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('chapters_count')
                    ->label('Chapter')
                    ->counts('chapters')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status')
                    ->placeholder('Semua status')
                    ->trueLabel('Published')
                    ->falseLabel('Draft'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn (CbtModule $record): bool => static::canForceDeleteRecord($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ChaptersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCbtModules::route('/'),
            'create' => Pages\CreateCbtModule::route('/create'),
            'edit' => Pages\EditCbtModule::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canForceDeleteRecord(CbtModule $module): bool
    {
        return ! $module->chapters()->withTrashed()->exists()
            && ! $module->orders()->exists()
            && ! $module->entitlements()->exists()
            && ! $module->moduleProgresses()->exists()
            && ! $module->certificates()->exists();
    }
}
