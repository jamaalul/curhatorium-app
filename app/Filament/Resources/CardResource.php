<?php

namespace App\Filament\Resources;

use App\Models\Card;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\CardResource\Pages;

class CardResource extends Resource
{
    protected static ?string $model = Card::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('content')->required(),
                TextInput::make('category')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('content')->limit(50),
                TextColumn::make('category'),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCards::route('/'),
            'create' => Pages\CreateCard::route('/create'),
            'edit' => Pages\EditCard::route('/{record}/edit'),
        ];
    }
} 