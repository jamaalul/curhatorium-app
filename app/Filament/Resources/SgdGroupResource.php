<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SgdGroupResource\Pages;
use App\Filament\Resources\SgdGroupResource\RelationManagers;
use App\Models\SgdGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SgdGroupResource extends Resource
{
    protected static ?string $model = SgdGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'SGD Groups';
    
    protected static ?string $modelLabel = 'SGD Group';
    
    protected static ?string $pluralModelLabel = 'SGD Groups';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Group Title'),
                    
                Forms\Components\Textarea::make('topic')
                    ->required()
                    ->maxLength(255)
                    ->label('Group Topic')
                    ->rows(3),
                    
                Forms\Components\Select::make('category')
                    ->required()
                    ->options([
                        'mental-health' => 'Mental Health',
                        'education' => 'Education',
                        'career' => 'Career',
                        'relationships' => 'Relationships',
                        'other' => 'Other',
                    ])
                    ->label('Category'),
                    
                Forms\Components\DateTimePicker::make('schedule')
                    ->required()
                    ->label('Meeting Schedule'),
                    
                Forms\Components\Select::make('host_id')
                    ->label('Host (Professional)')
                    ->options(function () {
                        return \App\Models\Professional::where('type', 'partner')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->placeholder('Select a professional host')
                    ->helperText('Only professionals with partner type can be selected as hosts'),
                    
                Forms\Components\Toggle::make('is_done')
                    ->label('Meeting Completed')
                    ->default(false),
                    
                Forms\Components\TextInput::make('meeting_address')
                    ->maxLength(255)
                    ->label('Meeting Address')
                    ->helperText('Leave empty to auto-generate'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Title'),
                    
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
                    
                Tables\Columns\TextColumn::make('host.name')
                    ->label('Host')
                    ->sortable()
                    ->searchable()
                    ->placeholder('No host assigned'),
                    
                Tables\Columns\IconColumn::make('is_done')
                    ->boolean()
                    ->label('Completed'),
                    
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
                    ->label('Meeting Status'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSgdGroups::route('/'),
            'create' => Pages\CreateSgdGroup::route('/create'),
            'edit' => Pages\EditSgdGroup::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return Auth::user()->is_admin ?? false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->is_admin ?? false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->is_admin ?? false;
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
