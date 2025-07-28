<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfessionalResource\Pages;
use App\Filament\Resources\ProfessionalResource\RelationManagers;
use App\Models\Professional;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProfessionalResource extends Resource
{
    protected static ?string $model = Professional::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\FileUpload::make('avatar')
                    ->label('Avatar')
                    ->image()
                    ->directory('avatars')
                    ->required(),
                Forms\Components\TagsInput::make('specialties')->label('Specialties')->required(),
                Forms\Components\Select::make('availability')
                    ->options([
                        'online' => 'Online',
                        'busy' => 'Busy',
                        'offline' => 'Offline',
                    ])->required(),
                Forms\Components\TextInput::make('availabilityText')->label('Availability Text')->required()->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'psychiatrist' => 'Psychiatrist',
                        'partner' => 'Partner',
                    ])->required(),
                Forms\Components\TextInput::make('whatsapp_number')->label('WhatsApp Number')->maxLength(32),
                Forms\Components\TextInput::make('bank_account_number')->label('Bank Account Number')->maxLength(64),
                Forms\Components\TextInput::make('bank_name')->label('Bank Name')->maxLength(64),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TagsColumn::make('specialties')->label('Specialties'),
                Tables\Columns\TextColumn::make('availability')->badge(),
                Tables\Columns\TextColumn::make('availabilityText')->label('Availability Text'),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('whatsapp_number')->label('WhatsApp'),
                Tables\Columns\TextColumn::make('bank_account_number')->label('Bank Account'),
                Tables\Columns\TextColumn::make('bank_name')->label('Bank Name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProfessionals::route('/'),
            'create' => Pages\CreateProfessional::route('/create'),
            'edit' => Pages\EditProfessional::route('/{record}/edit'),
        ];
    }
}
